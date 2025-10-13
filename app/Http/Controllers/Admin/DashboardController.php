<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Student;
use App\Models\BorrowRequest;
use App\Models\Attendance;
use App\Models\BorrowedBook;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStudents',
            'activeBorrows',
            'todayAttendance',
            'recentBorrows',
            'todayAttendanceRecords'
        ));
    }
}