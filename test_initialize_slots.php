<?php

// Test StudyAreaHelper initializeSlots method
echo "Testing StudyAreaHelper::initializeSlots()...\n\n";

echo "Current status before initialization:\n";
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Available slots: {$status['available']}/{$status['max_capacity']}\n\n";

// Count active study attendances
$activeStudyCount = \App\Models\Attendance::whereNull('logout')
    ->where(function($query) {
        $query->where('activity', 'like', '%stay%')
              ->orWhere('activity', 'like', '%study%')
              ->orWhere('activity', 'like', '%read%');
    })
    ->count();

echo "Active study attendances found: {$activeStudyCount}\n\n";

echo "Initializing slots...\n";
$result = \App\Helpers\StudyAreaHelper::initializeSlots();
echo "Initialization result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

echo "Status after initialization:\n";
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Available slots: {$status['available']}/{$status['max_capacity']}\n";

$expectedSlots = max(0, $status['max_capacity'] - $activeStudyCount);
echo "Expected slots: {$expectedSlots}\n";
echo "Match: " . ($status['available'] == $expectedSlots ? 'YES' : 'NO') . "\n";

echo "\nTest completed.\n";
