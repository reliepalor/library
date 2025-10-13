<?php

require_once 'vendor/autoload.php';

use App\Helpers\StudyAreaHelper;

echo "Testing StudyAreaHelper methods...\n\n";

echo "Testing isStudyActivity:\n";
$studyActivities = ['Study', 'Read', 'Stay in library', 'Research'];
$nonStudyActivities = ['Borrow book', 'Meeting', 'Consultation'];

foreach ($studyActivities as $activity) {
    $result = StudyAreaHelper::isStudyActivity($activity);
    echo "  '$activity' -> " . ($result ? 'STUDY' : 'NOT STUDY') . "\n";
}

foreach ($nonStudyActivities as $activity) {
    $result = StudyAreaHelper::isStudyActivity($activity);
    echo "  '$activity' -> " . ($result ? 'STUDY' : 'NOT STUDY') . "\n";
}

echo "\nTesting updateAvailability:\n";
$status = StudyAreaHelper::getStatus();
echo "Initial status: " . $status['available'] . "/" . $status['max_capacity'] . "\n";

// Test decrement
$result = StudyAreaHelper::updateAvailability(null, 'decrement', 1);
$status = StudyAreaHelper::getStatus();
echo "After decrement: " . $status['available'] . "/" . $status['max_capacity'] . " (Success: " . ($result ? 'YES' : 'NO') . ")\n";

// Test increment
$result = StudyAreaHelper::updateAvailability(null, 'increment', 1);
$status = StudyAreaHelper::getStatus();
echo "After increment: " . $status['available'] . "/" . $status['max_capacity'] . " (Success: " . ($result ? 'YES' : 'NO') . ")\n";

// Test edge cases
echo "\nTesting edge cases:\n";

// Try to decrement when no slots available
$status = StudyAreaHelper::getStatus();
echo "Current status: " . $status['available'] . "/" . $status['max_capacity'] . "\n";

// Set to 0 available slots first
for ($i = 0; $i < $status['available']; $i++) {
    StudyAreaHelper::updateAvailability(null, 'decrement', 1);
}

$status = StudyAreaHelper::getStatus();
echo "After setting to 0: " . $status['available'] . "/" . $status['max_capacity'] . "\n";

// Try to decrement when no slots available
$result = StudyAreaHelper::updateAvailability(null, 'decrement', 1);
echo "Attempting to decrement when no slots available: " . ($result ? 'SUCCESS' : 'FAILED (as expected)') . "\n";

// Try to increment beyond max capacity
$status = StudyAreaHelper::getStatus();
echo "Current status: " . $status['available'] . "/" . $status['max_capacity'] . "\n";

$result = StudyAreaHelper::updateAvailability(null, 'increment', $status['max_capacity'] + 5);
$status = StudyAreaHelper::getStatus();
echo "After incrementing beyond max: " . $status['available'] . "/" . $status['max_capacity'] . " (Success: " . ($result ? 'YES' : 'NO') . ")\n";

echo "\nTest completed.\n";
