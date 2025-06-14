<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Student;
use App\Models\BorrowRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStudents',
            'activeBorrows',
            'todayAttendance',
            'recentBorrows',
            'books',
            'students',
            'todayAttendanceRecords'
        ));
    }
} 