<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudyAreaController;

// Existing route
Route::get('/update-status', function (Request $request) {
    \App\Models\SeatStatus::create([
        'status' => $request->input('status')
    ]);
    return response()->json(['message' => 'Stored']);
});

// Study Area Routes
Route::prefix('study-area')->group(function () {
    Route::get('/availability', [StudyAreaController::class, 'getAvailability']);
    Route::post('/update-slots', [StudyAreaController::class, 'updateSlots']);
});