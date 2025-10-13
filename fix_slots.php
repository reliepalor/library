<?php

// Fix study area slots based on existing active study attendances
echo "Fixing study area slots...\n\n";

$activeStudyCount = \App\Models\Attendance::whereNull('logout')
    ->where(function($query) {
        $query->where('activity', 'like', '%stay%')
              ->orWhere('activity', 'like', '%study%')
              ->orWhere('activity', 'like', '%read%');
    })
    ->count();

echo "Active study attendances found: {$activeStudyCount}\n";

$studyArea = \App\Models\StudyArea::firstOrCreate(
    ['name' => 'Main Study Area'],
    ['max_capacity' => 30, 'available_slots' => 30]
);

echo "Current slots: {$studyArea->available_slots}/{$studyArea->max_capacity}\n";

$newSlots = max(0, $studyArea->max_capacity - $activeStudyCount);
echo "Calculated new slots: {$newSlots}\n";

$studyArea->available_slots = $newSlots;
$studyArea->save();

\Illuminate\Support\Facades\Cache::forget('study_area_availability');

$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "After fix: {$status['available']}/{$status['max_capacity']}\n";

echo "\nSlots fixed successfully!\n";
