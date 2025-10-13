<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use App\Services\AvatarService;
use App\Mail\AttendanceNotification;
use App\Helpers\StudyAreaHelper;
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
        $attendances = Attendance::with('student.user')
            ->where('user_type', 'student')
            ->whereDate('login', $today)
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();

        $teacherAttendances = Attendance::with('teacherVisitor.user')
            ->where('user_type', 'teacher')
            ->whereDate('login', $today)
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();

        return view('user.attendance.index', compact('attendances', 'teacherAttendances'));
    }

    /**
     * Provide today's attendance in a lightweight JSON for realtime polling on the user page.
     */
    public function realtime(Request $request)
    {
        try {
            $today = Carbon::today();

            // Get student attendance from Attendance table
            $studentAttendances = Attendance::with(['student.user'])
                ->where('user_type', 'student')
                ->whereBetween('login', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->orderByDesc('created_at')
                ->orderByDesc('login')
                ->get();

            // Get teacher/visitor attendance from Attendance table where user_type is 'teacher'
            $teacherAttendances = Attendance::with(['teacherVisitor.user'])
                ->where('user_type', 'teacher')
                ->whereBetween('login', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->orderByDesc('created_at')
                ->orderByDesc('login')
                ->get();

            $studentList = $studentAttendances->map(function ($a) {
                $studentName = trim(($a->student->lname ?? '') . ', ' . ($a->student->fname ?? ''));
                $profile = optional(optional($a->student)->user)->profile_picture;
                $profileUrl = AvatarService::getProfilePictureUrl($profile, $studentName);

                return [
                    'id' => $a->id,
                    'student_id' => $a->student_id,
                    'student_name' => $studentName,
                    'college' => $a->student->college ?? '',
                    'year' => $a->student->year ?? '',
                    'activity' => $a->activity ?? '',
                    'time_in' => optional($a->login)->setTimezone('Asia/Manila')->format('h:i A'),
                    'time_out' => $a->logout ? optional($a->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-',
                    'profile_picture' => $profileUrl,
                    'has_logout' => !is_null($a->logout),
                ];
            });

            $teacherList = $teacherAttendances->map(function ($a) {
                $teacherName = trim(($a->teacherVisitor->lname ?? '') . ', ' . ($a->teacherVisitor->fname ?? ''));
                $profile = optional(optional($a->teacherVisitor)->user)->profile_picture;
                $profileUrl = AvatarService::getProfilePictureUrl($profile, $teacherName);

                return [
                    'id' => $a->id,
                    'teacher_visitor_id' => $a->teacher_visitor_id,
                    'name' => $teacherName,
                    'role' => $a->teacherVisitor->role ?? '',
                    'department' => $a->teacherVisitor->department ?? '',
                    'activity' => $a->activity ?? '',
                    'time_in' => optional($a->login)->setTimezone('Asia/Manila')->format('h:i A'),
                    'time_out' => $a->logout ? optional($a->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-',
                    'profile_picture' => $profileUrl,
                    'has_logout' => !is_null($a->logout),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'studentAttendance' => $studentList,
                    'teacherAttendance' => $teacherList,
                    'last_updated' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('User realtime attendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch attendance data',
            ], 500);
        }
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
            $now = now()->setTimezone('Asia/Manila');
            $today = $now->toDateString();

            // Check if there's a rejected borrow request for this student today
            $hasRejectedRequest = \App\Models\BorrowedBook::where('student_id', $studentId)
                ->whereDate('created_at', $today)
                ->where('status', 'rejected')
                ->exists();

            // If there's a rejected request, we still follow normal attendance logic
            // The attendance record will show "Borrow book rejected" in the activity column
            // but the student can still log in/out normally for other activities

            // Check for the most recent attendance record for this student today
            $attendance = Attendance::where('student_id', $studentId)
                ->whereDate('login', $today)
                ->orderBy('login', 'desc')
                ->first();

            // If the most recent record is already logged out, treat it as if there's no active session
            if ($attendance && !is_null($attendance->logout)) {
                $attendance = null;
            }

            if ($attendance) {
                // If found, set logout time
                $attendance->logout = $now;
                $attendance->save();

                // If the activity was study-related, increment available study slots
                if (StudyAreaHelper::isStudyActivity($attendance->activity)) {
                    StudyAreaHelper::updateAvailability(null, 'increment', 1);
                }

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
                                'returned_at' => $now
                            ]);
                    }
                }

                // Calculate duration
                $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);

                // Only send logout notification if this wasn't a system logout
                if (!$attendance->system_logout) {
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
                } else {
                    Log::info("Skipping logout email for system-logged-out session for student {$studentId}");
                }

                return response()->json([
                    'message' => 'Logout time recorded successfully.',
                    'type' => 'logout',
                    'student_id' => $studentId
                ]);
            } else {
                // Check if there are any pending or approved borrow requests for this student today
                $hasActiveBorrowRequest = \App\Models\BorrowedBook::where('student_id', $studentId)
                    ->whereDate('created_at', $today)
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();

                // Only prevent borrowing if they have a pending request for the same book
                if ($hasActiveBorrowRequest && str_contains($activity, 'Borrow')) {
                    // Check if they're trying to borrow the same book again
                    $bookCode = null;
                    if (preg_match('/Borrow:([A-Z0-9]+)/', $activity, $matches)) {
                        $bookCode = $matches[1];
                    }

                    if ($bookCode) {
                        $existingRequest = \App\Models\BorrowedBook::where('student_id', $studentId)
                            ->where('book_id', $bookCode)
                            ->whereDate('created_at', $today)
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();

                        if ($existingRequest) {
                            return response()->json([
                                'message' => 'You already have a pending or approved request for this book. Please wait for it to be processed.',
                                'type' => 'pending_request',
                                'student_id' => $studentId
                            ], 422);
                        }
                    }
                }

                // If the activity is study-related, decrement available study slots
                if (StudyAreaHelper::isStudyActivity($activity)) {
                    StudyAreaHelper::updateAvailability(null, 'decrement', 1);
                }

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
        
        // Get the most recent attendance record for this student today
        $attendance = Attendance::where('student_id', $studentId)
            ->whereDate('login', $today)
            ->orderBy('login', 'desc')
            ->first();

        // If the most recent record is already logged out, treat it as inactive
        $hasActiveSession = $attendance && is_null($attendance->logout);

        return response()->json([
            'hasActiveSession' => $hasActiveSession,
            'student_id' => $studentId,
            'activity' => $hasActiveSession ? $attendance->activity : null
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