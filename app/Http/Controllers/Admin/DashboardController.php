<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BorrowedBook;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        
        // Get today's attendance with proper time tracking and student relationship
        $todayAttendance = Attendance::with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college');
            }])
            ->whereDate('login', $today)
            ->orderBy('login', 'desc')
            ->get();

        // Format attendance data for display with proper time formatting
        $formattedAttendance = $todayAttendance->map(function ($attendance) {
            return [
                'student_id' => $attendance->student->student_id ?? 'N/A',
                'student_name' => ($attendance->student->lname ?? 'N/A') . ', ' . ($attendance->student->fname ?? 'N/A'),
                'college' => $attendance->student->college ?? 'N/A',
                'activity' => $attendance->activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A'
            ];
        });

        // Get recent borrow requests
        $recentBorrows = BorrowedBook::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'todayAttendance' => $formattedAttendance,
            'recentBorrows' => $recentBorrows
        ]);
    }
} 