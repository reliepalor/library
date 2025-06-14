<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAttendance extends Command
{
    protected $signature = 'attendance:reset';
    protected $description = 'Archive today\'s attendance and reset the attendance table';

    public function handle()
    {
        $today = Carbon::today();
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Get today's attendance records
            $todayAttendance = Attendance::with('student')
                ->whereDate('created_at', $today)
                ->get();

            // Archive attendance records
            foreach ($todayAttendance as $record) {
                AttendanceHistory::create([
                    'student_id' => $record->student_id,
                    'activity' => $record->activity,
                    'book_code' => $record->book_code,
                    'college' => $record->student->college,
                    'course' => $record->student->course,
                    'date' => $record->created_at->toDateString(),
                    'time_in' => $record->created_at->toTimeString(),
                    'time_out' => $record->time_out ? $record->time_out->toTimeString() : null,
                ]);
            }

            // Delete today's attendance records
            Attendance::whereDate('created_at', $today)->delete();

            DB::commit();
            $this->info('Attendance has been successfully archived and reset.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to reset attendance: ' . $e->getMessage());
        }
    }
} 