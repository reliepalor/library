<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\TeachersVisitorsAttendance;
use App\Models\StudyArea;
use App\Helpers\StudyAreaHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ResetAttendance extends Command
{
    protected $signature = 'attendance:reset';
    protected $description = 'Archive today\'s attendance records for both students and teachers/visitors, reset study area availability, and clear attendance tables';

    public function handle()
    {
        $today = Carbon::today();
        $this->info('Starting attendance reset for ' . $today->toDateString());

        // Begin transaction
        DB::beginTransaction();

        try {
            // Get today's attendance records for both students and teachers/visitors
            $studentAttendance = Attendance::with(['student' => function($query) {
                    $query->select('student_id', 'college', 'gender');
                }])
                ->whereDate('login', $today)
                ->get();

            $teacherAttendance = TeachersVisitorsAttendance::with(['teacherVisitor' => function($query) {
                    $query->select('id', 'department', 'role', 'gender');
                }])
                ->whereDate('login', $today)
                ->get();

            $totalRecords = $studentAttendance->count() + $teacherAttendance->count();
            $this->info("Found {$studentAttendance->count()} student and {$teacherAttendance->count()} teacher/visitor records to archive");

            // Archive student attendance records
            $this->archiveStudentAttendance($studentAttendance, $today);

            // Archive teacher/visitor attendance records
            $this->archiveTeacherAttendance($teacherAttendance, $today);

            // Delete today's attendance records from both tables
            $deletedStudents = Attendance::whereDate('login', $today)->delete();
            $deletedTeachers = TeachersVisitorsAttendance::whereDate('login', $today)->delete();

            // Reset study area availability (all active sessions end, so reset to max capacity)
            $this->resetStudyAreaAvailability();

            DB::commit();

            $this->info("Successfully archived {$totalRecords} attendance records and reset study area availability");
            Log::info('Daily attendance reset completed', [
                'date' => $today->toDateString(),
                'student_records' => $studentAttendance->count(),
                'teacher_records' => $teacherAttendance->count(),
                'total_archived' => $totalRecords,
                'deleted_students' => $deletedStudents,
                'deleted_teachers' => $deletedTeachers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to reset attendance: ' . $e->getMessage());
            Log::error('Attendance reset failed', [
                'date' => $today->toDateString(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Archive student attendance records to attendance_histories table
     */
    private function archiveStudentAttendance($attendance, $today)
    {
        foreach ($attendance as $record) {
            // Calculate duration in minutes if both time_in and time_out exist
            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'student',
                'student_id' => $record->student_id,
                'college' => $record->student ? $record->student->college : null,
                'gender' => $record->student ? $record->student->gender : null,
                'activity' => $record->activity,
                'date' => $today->toDateString(),
                'time_in' => $record->login ? $record->login->toTimeString() : null,
                'time_out' => $record->logout ? $record->logout->toTimeString() : null,
                'duration' => $duration,
            ]);
        }
    }

    /**
     * Archive teacher/visitor attendance records to attendance_histories table
     */
    private function archiveTeacherAttendance($attendance, $today)
    {
        foreach ($attendance as $record) {
            // Calculate duration in minutes if both time_in and time_out exist
            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'teacher',
                'teacher_visitor_id' => $record->teacher_visitor_id,
                'department' => $record->teacherVisitor ? $record->teacherVisitor->department : null,
                'role' => $record->teacherVisitor ? $record->teacherVisitor->role : null,
                'gender' => $record->teacherVisitor ? $record->teacherVisitor->gender : null,
                'activity' => $record->activity,
                'date' => $today->toDateString(),
                'time_in' => $record->login ? $record->login->toTimeString() : null,
                'time_out' => $record->logout ? $record->logout->toTimeString() : null,
                'duration' => $duration,
            ]);
        }
    }

    /**
     * Reset study area availability to maximum capacity
     */
    private function resetStudyAreaAvailability()
    {
        try {
            $studyArea = StudyArea::firstOrCreate(
                ['name' => 'Main Study Area'],
                ['max_capacity' => 30, 'available_slots' => 30]
            );

            // Reset to maximum capacity since all sessions end
            $studyArea->available_slots = $studyArea->max_capacity;
            $studyArea->save();

            // Clear the cache to ensure fresh data
            Cache::forget('study_area_availability');

            $this->info("Study area availability reset to {$studyArea->max_capacity} slots");
            Log::info('Study area availability reset during attendance reset', [
                'max_capacity' => $studyArea->max_capacity,
                'reset_slots' => $studyArea->available_slots
            ]);

        } catch (\Exception $e) {
            $this->error('Failed to reset study area availability: ' . $e->getMessage());
            Log::error('Study area reset failed during attendance reset', [
                'error' => $e->getMessage()
            ]);
            // Don't throw exception here as it's not critical to the main attendance reset
        }
    }
}
