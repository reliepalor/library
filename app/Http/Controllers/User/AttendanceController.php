<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use App\Mail\AttendanceNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendance records.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $attendances = Attendance::with('student')
            ->whereDate('login', $today)
            ->orderBy('login', 'desc')
            ->get();

        return view('user.attendance.index', compact('attendances'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:students,student_id',
            'activity' => 'required|string',
        ]);

        Attendance::create([
            'student_id' => $request->student_id,
            'activity' => $request->activity,
            'login' => now()->setTimezone('Asia/Manila'),
        ]);

        return redirect()->back()->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Log attendance action (handles both login and logout).
     */
    public function log(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|string|exists:students,student_id',
                'activity' => 'required|string',
            ]);

            $studentId = $request->student_id;
            $activity = $request->activity;
            $today = Carbon::today()->toDateString();
            $now = now();

            // Check if there is an attendance record for this student today with login but no logout
            $attendance = Attendance::where('student_id', $studentId)
                ->whereDate('login', $today)
                ->whereNull('logout')
                ->first();

            if ($attendance) {
                // If found, set logout time
                $attendance->logout = $now;
                $attendance->save();

                // If the activity was a book borrowing, update the book's borrowed status
                if (str_contains($attendance->activity, 'Borrow')) {
                    $parts = explode(':', $attendance->activity);
                    if (count($parts) > 1) {
                        $bookCode = trim($parts[1]);
                        // Update the borrowed book status and set returned_at
                        \App\Models\BorrowedBook::where('book_id', $bookCode)
                            ->where('status', 'approved')
                            ->update([
                                'status' => 'returned',
                                'returned_at' => now()
                            ]);
                    }
                }

                // Calculate duration
                $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);

                // Send logout notification email
                $student = Student::where('student_id', $studentId)->first();
                if ($student && $student->email) {
                    try {
                        Log::info("Attempting to send logout email to student {$student->email}");
                        Mail::to($student->email)->send(new AttendanceNotification(
                            $student,
                            'logout',
                            $now,
                            $attendance->activity,
                            $duration
                        ));
                        Log::info("Logout email sent to student {$student->email}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send logout email to student {$student->email}: " . $e->getMessage());
                    }
                }

                return response()->json([
                    'message' => 'Logout time recorded successfully.',
                    'type' => 'logout',
                    'student_id' => $studentId
                ]);
            } else {
                // Create new attendance record with login time and the selected activity
                $attendance = Attendance::create([
                    'student_id' => $studentId,
                    'activity' => $activity,
                    'login' => $now,
                ]);

                // Send login notification email
                $student = Student::where('student_id', $studentId)->first();
                if ($student && $student->email) {
                    try {
                        Log::info("Attempting to send login email to student {$student->email}");
                        Mail::to($student->email)->send(new AttendanceNotification(
                            $student,
                            'login',
                            $now,
                            $activity
                        ));
                        Log::info("Login email sent to student {$student->email}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send login email to student {$student->email}: " . $e->getMessage());
                    }
                }

                return response()->json([
                    'message' => 'Login time recorded successfully.',
                    'type' => 'login',
                    'student_id' => $studentId
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Attendance logging error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if student has an active attendance session today.
     */
    public function check(Request $request)
    {
        $studentId = $request->query('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        $today = Carbon::today()->toDateString();
        
        // Check if there is an attendance record for this student today with login but no logout
        $attendance = Attendance::where('student_id', $studentId)
            ->whereDate('login', $today)
            ->whereNull('logout')
            ->first();

        return response()->json([
            'hasActiveSession' => (bool) $attendance,
            'student_id' => $studentId,
            'activity' => $attendance ? $attendance->activity : null
        ]);
    }

    /**
     * Show the attendance scan page.
     */
    public function scan(Request $request)
    {
        $studentId = $request->query('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        $student = Student::where('student_id', $studentId)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json(['students' => $student]);
    }
}