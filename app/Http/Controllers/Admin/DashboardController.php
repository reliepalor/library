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
        // Get total books count
        $totalBooks = Books::count();

        // Get total students count
        $totalStudents = Student::active()->count();

        // Get active borrows (books that are currently borrowed)
        $activeBorrows = BorrowRequest::where('status', 'approved')->count();

        // Get today's attendance count
        $todayAttendance = Attendance::whereDate('created_at', Carbon::today())->count();

        // Get recent borrow requests with student and book details
        $recentBorrows = BorrowedBook::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        // Get today's attendance records
        $todayAttendanceRecords = Attendance::with('student')
            ->whereDate('created_at', Carbon::today())
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