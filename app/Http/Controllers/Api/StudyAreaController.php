<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyArea;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StudyAreaController extends Controller
{
    /**
     * Get current study area availability
     */
    public function getAvailability()
    {
        try {
            $studyArea = StudyArea::getAvailableSlots();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'available_slots' => $studyArea->available_slots,
                    'max_capacity' => $studyArea->max_capacity,
                    'status_color' => $studyArea->status_color,
                    'is_full' => $studyArea->available_slots <= 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting study area availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get study area availability'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update study area slots based on user activity
     */
    public function updateSlots(Request $request)
    {
        $request->validate([
            'action' => 'required|in:increment,decrement',
            'amount' => 'sometimes|integer|min:1',
        ]);

        try {
            $studyArea = StudyArea::getAvailableSlots();
            $amount = $request->input('amount', 1);
            
            if ($request->action === 'decrement') {
                if ($studyArea->available_slots < $amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough available slots',
                        'available_slots' => $studyArea->available_slots
                    ], Response::HTTP_BAD_REQUEST);
                }
                $studyArea->decrement('available_slots', $amount);
            } else {
                $studyArea->increment('available_slots', $amount);
                // Ensure we don't exceed max capacity
                if ($studyArea->available_slots > $studyArea->max_capacity) {
                    $studyArea->update(['available_slots' => $studyArea->max_capacity]);
                }
            }

            $studyArea->refresh();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'available_slots' => $studyArea->available_slots,
                    'status_color' => $studyArea->status_color,
                    'is_full' => $studyArea->available_slots <= 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating study area slots: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update study area slots'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
