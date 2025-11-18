<?php

namespace App\Console\Commands;

use App\Services\AttendanceSaveResetService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetAttendance extends Command
{
    protected $signature = 'attendance:reset';
    protected $description = 'Archive today\'s attendance records, reset study area availability, and clear active sessions';

    protected AttendanceSaveResetService $attendanceSaveResetService;

    public function __construct(AttendanceSaveResetService $attendanceSaveResetService)
    {
        parent::__construct();

        $this->attendanceSaveResetService = $attendanceSaveResetService;
    }

    public function handle()
    {
        $today = Carbon::now(config('app.timezone', 'UTC'));
        $this->info('Starting attendance save/reset for ' . $today->toDateString());

        try {
            $result = $this->attendanceSaveResetService->saveAndReset($today);

            if ($result['saved']) {
                $this->info("Saved {$result['student_records']} student and {$result['teacher_records']} teacher records.");
            } else {
                $this->info('No attendance records found for today. Study area reset only.');
            }

            Log::info('Daily attendance save/reset completed', $result);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to reset attendance: ' . $e->getMessage());
            Log::error('Attendance reset failed', [
                'date' => $today->toDateString(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
