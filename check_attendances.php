<?php

// Check active study attendances
echo "Checking active study attendances...\n\n";

$activeStudyAttendances = \App\Models\Attendance::whereNull('logout')
    ->where(function($query) {
        $query->where('activity', 'like', '%stay%')
              ->orWhere('activity', 'like', '%study%')
              ->orWhere('activity', 'like', '%read%');
    })
    ->get();

echo 'Found ' . $activeStudyAttendances->count() . " active study attendances:\n";

foreach ($activeStudyAttendances as $attendance) {
    echo '  - ID: ' . $attendance->id . ', Activity: ' . $attendance->activity . ', User Type: ' . $attendance->user_type . "\n";
}

echo "\nTotal active attendances (all activities): " . \App\Models\Attendance::whereNull('logout')->count() . "\n";

$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Current study area status: {$status['available']}/{$status['max_capacity']}\n";
