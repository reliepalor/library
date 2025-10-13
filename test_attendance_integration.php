<?php

// Test AttendanceController integration with StudyAreaHelper
echo "Testing AttendanceController integration with StudyAreaHelper...\n\n";

// Test scenarios
$testScenarios = [
    'Study' => true,      // Should decrement slots on login
    'Read' => true,       // Should decrement slots on login
    'Borrow book' => false, // Should NOT affect slots
    'Meeting' => false,   // Should NOT affect slots
];

foreach ($testScenarios as $activity => $shouldAffectSlots) {
    echo "Testing activity: '$activity'\n";

    // Get initial status
    $initialStatus = \App\Helpers\StudyAreaHelper::getStatus();
    echo "  Initial slots: {$initialStatus['available']}/{$initialStatus['max_capacity']}\n";

    // Simulate login (decrement if study activity)
    if ($shouldAffectSlots) {
        $result = \App\Helpers\StudyAreaHelper::updateAvailability(null, 'decrement', 1);
        $afterLoginStatus = \App\Helpers\StudyAreaHelper::getStatus();
        echo "  After login (decrement): {$afterLoginStatus['available']}/{$afterLoginStatus['max_capacity']} (Success: " . ($result ? 'YES' : 'NO') . ")\n";

        // Simulate logout (increment)
        $result = \App\Helpers\StudyAreaHelper::updateAvailability(null, 'increment', 1);
        $afterLogoutStatus = \App\Helpers\StudyAreaHelper::getStatus();
        echo "  After logout (increment): {$afterLogoutStatus['available']}/{$afterLogoutStatus['max_capacity']} (Success: " . ($result ? 'YES' : 'NO') . ")\n";
    } else {
        echo "  Non-study activity - slots should remain unchanged\n";
        $unchangedStatus = \App\Helpers\StudyAreaHelper::getStatus();
        echo "  Slots unchanged: {$unchangedStatus['available']}/{$unchangedStatus['max_capacity']}\n";
    }

    echo "\n";
}

// Test edge cases
echo "Testing edge cases:\n";

// Test when no slots available
echo "Testing when no slots available:\n";
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Current status: {$status['available']}/{$status['max_capacity']}\n";

// Decrement all available slots
for ($i = 0; $i < $status['available']; $i++) {
    \App\Helpers\StudyAreaHelper::updateAvailability(null, 'decrement', 1);
}

$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "After emptying slots: {$status['available']}/{$status['max_capacity']}\n";

// Try to decrement when no slots available (should fail)
$result = \App\Helpers\StudyAreaHelper::updateAvailability(null, 'decrement', 1);
$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Attempting to decrement when no slots: {$status['available']}/{$status['max_capacity']} (Success: " . ($result ? 'YES' : 'NO - correctly failed') . ")\n";

// Reset to full capacity for next tests
for ($i = 0; $i < $status['max_capacity']; $i++) {
    \App\Helpers\StudyAreaHelper::updateAvailability(null, 'increment', 1);
}

$status = \App\Helpers\StudyAreaHelper::getStatus();
echo "Reset to full capacity: {$status['available']}/{$status['max_capacity']}\n";

echo "\nAll tests completed.\n";
