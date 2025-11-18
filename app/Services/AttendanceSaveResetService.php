<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\BorrowedBook;
use App\Models\StudyArea;
use App\Traits\FormatsBorrowActivity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceSaveResetService
{
    use FormatsBorrowActivity;

    /**
     * Save today's attendance records to history and reset the live tables.
     *
     * @param  \Carbon\Carbon|null  $date
     * @return array<string, mixed>
     */
    public function saveAndReset(?Carbon $date = null): array
    {
        $timezone = config('app.timezone', 'UTC');
        $date = ($date ?? Carbon::now($timezone))->copy()->timezone($timezone);

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $studentAttendance = $this->getStudentAttendance($startOfDay, $endOfDay);
        $teacherAttendance = $this->getTeacherAttendance($startOfDay, $endOfDay);
        $borrowRequests = $this->getBorrowRequestsForDate($date);

        $result = [
            'saved' => false,
            'student_records' => $studentAttendance->count(),
            'teacher_records' => $teacherAttendance->count(),
            'date' => $date->toDateString(),
        ];

        if ($result['student_records'] === 0 && $result['teacher_records'] === 0) {
            $this->resetStudyAreaAvailability();
            Log::info('Attendance save/reset skipped - no records found for date', $result);
            return $result;
        }

        DB::transaction(function () use (
            $studentAttendance,
            $teacherAttendance,
            $borrowRequests,
            $date
        ) {
            $this->saveAttendanceHistory($studentAttendance, $teacherAttendance, $borrowRequests, $date);

            $studentAttendance->each->delete();
            $teacherAttendance->each->delete();

            $this->resetStudyAreaAvailability();
        });

        $result['saved'] = true;

        Log::info('Attendance records saved and reset', $result);

        return $result;
    }

    /**
     * Get student attendance entries for the provided range.
     */
    protected function getStudentAttendance(Carbon $startOfDay, Carbon $endOfDay)
    {
        return Attendance::students()
            ->with(['student' => function ($query) {
                $query->select('student_id', 'lname', 'fname', 'college', 'gender');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get teacher attendance entries for the provided range.
     */
    protected function getTeacherAttendance(Carbon $startOfDay, Carbon $endOfDay)
    {
        return Attendance::teachers()
            ->with(['teacherVisitor' => function ($query) {
                $query->select('id', 'lname', 'fname', 'department', 'role', 'gender');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get today's borrow requests grouped by attendee identifier.
     */
    protected function getBorrowRequestsForDate(Carbon $date): Collection
    {
        return BorrowedBook::with('book:id,book_code,name')
            ->whereDate('created_at', $date->toDateString())
            ->whereIn('status', ['pending', 'approved', 'rejected', 'returned'])
            ->get()
            ->groupBy(function ($item) {
                return $item->student_id ?? $item->teacher_visitor_id;
            });
    }

    /**
     * Persist attendance records into the history table.
     */
    protected function saveAttendanceHistory($studentAttendance, $teacherAttendance, Collection $borrowRequests, Carbon $date): void
    {
        $today = $date->toDateString();

        foreach ($studentAttendance as $record) {
            $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $record->student_id);

            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'student',
                'student_id' => $record->student_id,
                'college' => $record->student->college ?? null,
                'gender' => $record->student->gender ?? null,
                'activity' => $activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'duration' => $duration,
                'date' => $today,
            ]);
        }

        foreach ($teacherAttendance as $record) {
            $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $record->teacher_visitor_id);

            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'teacher',
                'teacher_visitor_id' => $record->teacher_visitor_id,
                'department' => $record->teacherVisitor->department ?? null,
                'role' => $record->teacherVisitor->role ?? null,
                'gender' => $record->teacherVisitor->gender ?? null,
                'activity' => $activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'duration' => $duration,
                'date' => $today,
            ]);
        }
    }

    /**
     * Reset study area availability to maximum capacity.
     */
    protected function resetStudyAreaAvailability(): void
    {
        try {
            $studyArea = StudyArea::firstOrCreate(
                ['name' => 'Main Study Area'],
                ['max_capacity' => 30, 'available_slots' => 30]
            );

            $studyArea->available_slots = $studyArea->max_capacity;
            $studyArea->save();

            Cache::forget('study_area_availability');

            Log::info('Study area availability reset during attendance save/reset', [
                'max_capacity' => $studyArea->max_capacity,
                'reset_slots' => $studyArea->available_slots,
            ]);

        } catch (\Exception $e) {
            Log::error('Study area reset failed during attendance save/reset', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}

