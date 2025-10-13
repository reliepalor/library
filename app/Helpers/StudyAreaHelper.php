<?php

namespace App\Helpers;

use App\Models\StudyArea;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StudyAreaHelper
{
    /**
     * Update study area availability based on user activity
     *
     * @param User|null $user The user performing the action (optional)
     * @param string $activityType The type of activity ('increment' or 'decrement')
     * @param int $amount The number of slots to update (default: 1)
     * @return bool True on success, false on failure
     */
    public static function updateAvailability(?User $user, string $activityType, int $amount = 1): bool
    {
        try {
            $studyArea = StudyArea::firstOrCreate(
                ['name' => 'Main Study Area'],
                ['max_capacity' => 30, 'available_slots' => 30]
            );

            Log::info("StudyArea updateAvailability called: type={$activityType}, amount={$amount}, current_slots={$studyArea->available_slots}");

            if ($activityType === 'decrement') {
                if ($studyArea->available_slots < $amount) {
                    Log::warning("Not enough slots available for decrement: requested={$amount}, available={$studyArea->available_slots}");
                    return false; // Not enough slots available
                }
                $studyArea->decrement('available_slots', $amount);
                Log::info("Decremented slots: new_available={$studyArea->available_slots}");
            } else {
                $newValue = $studyArea->available_slots + $amount;
                $studyArea->available_slots = min($newValue, $studyArea->max_capacity);
                $studyArea->save();
                Log::info("Incremented slots: new_available={$studyArea->available_slots}");
            }

            // Clear the cache to ensure fresh data on next request
            \Illuminate\Support\Facades\Cache::forget('study_area_availability');

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating study area availability: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if there are enough available slots
     */
    public static function hasAvailableSlots(int $required = 1): bool
    {
        $studyArea = StudyArea::firstOrCreate(
            ['name' => 'Main Study Area'],
            ['max_capacity' => 30, 'available_slots' => 30]
        );
        
        return $studyArea->available_slots >= $required;
    }

    /**
     * Get current availability status
     */
    public static function getStatus(): array
    {
        $studyArea = StudyArea::firstOrCreate(
            ['name' => 'Main Study Area'],
            ['max_capacity' => 30, 'available_slots' => 30]
        );

        return [
            'available' => $studyArea->available_slots,
            'max_capacity' => $studyArea->max_capacity,
            'status' => $studyArea->available_slots <= 5 ? 'danger' :
                      ($studyArea->available_slots <= 10 ? 'warning' : 'success')
        ];
    }

    /**
     * Check if an activity is study-related (occupies study space)
     *
     * @param string $activity The activity string to check
     * @return bool True if the activity is study-related
     */
    public static function isStudyActivity(string $activity): bool
    {
        $studyKeywords = ['stay', 'study', 'read'];

        $lowerActivity = strtolower($activity);

        foreach ($studyKeywords as $keyword) {
            if (str_contains($lowerActivity, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
