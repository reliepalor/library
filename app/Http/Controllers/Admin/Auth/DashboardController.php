<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Student;
use App\Models\BorrowRequest;
use App\Models\Attendance;
use App\Models\StudyArea;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total books count
        $totalBooks = Books::count();
        
        // Get total students count
        $totalStudents = Student::active()->count();
        
        // Get active borrows (books that are currently borrowed)
        $activeBorrows = BorrowRequest::where('status', 'approved')->count();
        
        // Get today's attendance
        $todayAttendance = Attendance::whereDate('created_at', Carbon::today())->count();
        
        // Get recent borrow requests with student and book details
        $recentBorrows = BorrowRequest::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        // Get list of all books
        $books = Books::all();
        
        // Get list of all students
        $students = Student::all();
        
        // Get today's attendance records
        $todayAttendanceRecords = Attendance::with('student')
            ->whereDate('created_at', Carbon::today())
            ->get();

        // Get study area information
        $studyArea = StudyArea::getAvailableSlots();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStudents',
            'activeBorrows',
            'todayAttendance',
            'recentBorrows',
            'books',
            'students',
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

            // Recalculate available slots based on active study sessions from both tables
            $studentStudySessions = Attendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%')
                          ->orWhere('activity', 'like', '%reading%')
                          ->orWhere('activity', 'like', '%research%')
                          ->orWhere('activity', 'like', '%group study%')
                          ->orWhere('activity', 'like', '%computer use%')
                          ->orWhere('activity', 'like', '%meeting%');
                })
                ->count();

            $teacherVisitorStudySessions = \App\Models\TeachersVisitorsAttendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%')
                          ->orWhere('activity', 'like', '%reading%')
                          ->orWhere('activity', 'like', '%research%')
                          ->orWhere('activity', 'like', '%group study%')
                          ->orWhere('activity', 'like', '%computer use%')
                          ->orWhere('activity', 'like', '%meeting%');
                })
                ->count();

            $totalActiveStudySessions = $studentStudySessions + $teacherVisitorStudySessions;

            $newAvailableSlots = max(0, $newCapacity - $totalActiveStudySessions);
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
