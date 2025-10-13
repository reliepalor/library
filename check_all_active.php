<?php

// Check all active attendances
echo "Checking all active attendances...\n\n";

$allActive = \App\Models\Attendance::whereNull('logout')->get();

echo 'All active attendances (' . $allActive->count() . "):\n";

foreach ($allActive as $attendance) {
    echo '  - ID: ' . $attendance->id . ', Activity: ' . $attendance->activity . ', User Type: ' . $attendance->user_type . "\n";
}

echo "\nStudy area status: ";
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "{$status['available']}/{$status['max_capacity']}\n";
