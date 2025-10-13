<?php

// Test StudyAreaHelper methods
echo "Testing StudyAreaHelper methods...\n\n";

echo "Testing isStudyActivity:\n";
$studyActivities = ['Study', 'Read', 'Stay in library', 'Research'];
$nonStudyActivities = ['Borrow book', 'Meeting', 'Consultation'];

foreach ($studyActivities as $activity) {
    $result = \App\Helpers\StudyAreaHelper::isStudyActivity($activity);
    echo "  '$activity' -> " . ($result ? 'STUDY' : 'NOT STUDY') . "\n";
}

foreach ($nonStudyActivities as $activity) {
    $result = \App\Helpers\StudyAreaHelper::isStudyActivity($activity);
    echo "  '$activity' -> " . ($result ? 'STUDY' : 'NOT STUDY') . "\n";
}

echo "\nTesting updateAvailability:\n";
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Initial status: " . $status['available'] . "/" . $status['max_capacity'] . "\n";

// Test decrement
$result = \App\Helpers\StudyAreaHelper::updateAvailability(null, 'decrement', 1);
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "After decrement: " . $status['available'] . "/" . $status['max_capacity'] . " (Success: " . ($result ? 'YES' : 'NO') . ")\n";

// Test increment
$result = \App\Helpers\StudyAreaHelper::updateAvailability(null, 'increment', 1);
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "After increment: " . $status['available'] . "/" . $status['max_capacity'] . " (Success: " . ($result ? 'YES' : 'NO') . ")\n";

echo "\nTest completed.\n";
