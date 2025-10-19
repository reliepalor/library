<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Student;
use App\Models\BorrowRequest;
use App\Models\Attendance;
use App\Models\BorrowedBook;
use App\Models\StudyArea;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Books::count();

        $totalStudents = Student::active()->count();

        // Get active borrows (books that are currently borrowed)
        $activeBorrows = BorrowRequest::where('status', 'approved')->count();

        // Get today's attendance count
        $todayAttendance = Attendance::whereDate('created_at', Carbon::today())->count();

        // Get recent borrow requests with student, teacher/visitor and book details
        $recentBorrows = BorrowedBook::with(['student', 'book'])
            ->whereHas('student') // Ensure we have a student/teacher record
            ->latest()
            ->take(5)
            ->get();

        // Get today's attendance records (students and teachers/visitors)
        $todayAttendanceRecords = Attendance::with(['student', 'teacherVisitor'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        // Get study area information
        $studyArea = StudyArea::getAvailableSlots();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStudents',
            'activeBorrows',
            'todayAttendance',
            'recentBorrows',
            'todayAttendanceRecords',
            'studyArea'
        ));
    }

    public function updateStudyAreaSettings(Request $request)
    {
        $request->validate([
            'max_capacity' => 'required|integer|min:1|max:1000',
        ]);

        try {
            $studyArea = StudyArea::firstOrCreate(
                ['name' => 'Main Study Area'],
                ['max_capacity' => 30, 'available_slots' => 30]
            );

            $oldCapacity = $studyArea->max_capacity;
            $newCapacity = $request->max_capacity;

            $studyArea->update(['max_capacity' => $newCapacity]);

            // Recalculate available slots based on active study sessions
            $activeStudySessions = Attendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%');
                })
                ->count();

            $newAvailableSlots = max(0, $newCapacity - $activeStudySessions);
            $studyArea->update(['available_slots' => $newAvailableSlots]);

            Log::info("Study area capacity updated from {$oldCapacity} to {$newCapacity}, available slots recalculated to {$newAvailableSlots}");

            return response()->json([
                'success' => true,
                'message' => 'Study area capacity updated successfully',
                'data' => [
                    'max_capacity' => $newCapacity,
                    'available_slots' => $newAvailableSlots,
                    'status_color' => $newAvailableSlots <= 5 ? 'danger' : ($newAvailableSlots <= 10 ? 'warning' : 'success')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating study area settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update study area settings'
            ], 500);
        }
    }
}
