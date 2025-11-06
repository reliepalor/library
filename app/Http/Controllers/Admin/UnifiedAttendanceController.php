<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Student;
use App\Models\TeacherVisitor;
use App\Models\BorrowedBook;
use App\Models\Books;
use App\Mail\AttendanceNotification;
use App\Services\AvatarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Events\AttendanceUpdated;
use App\Mail\LogoutCodeMail;
use Illuminate\Support\Facades\Cache;
use App\Helpers\StudyAreaHelper;
use App\Models\StudyArea;

class UnifiedAttendanceController extends Controller
{
    /**
     * Display today's attendance for both students and teachers
     */
    public function index()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        // Get attendance records for both students and teachers
        $studentAttendance = $this->getStudentAttendance($startOfDay, $endOfDay);
        $teacherAttendance = $this->getTeacherAttendance($startOfDay, $endOfDay);
        
        // Get borrow requests
        $borrowRequests = $this->getTodayBorrowRequests();

        // Process and format data
        $processedStudentData = $this->processStudentAttendance($studentAttendance, $borrowRequests);
        $processedTeacherData = $this->processTeacherAttendance($teacherAttendance);

        // Calculate overall statistics
        $overallStats = [
            'total' => $studentAttendance->count() + $teacherAttendance->count(),
            'students_present' => $studentAttendance->whereNull('logout')->count(),
            'teachers_present' => $teacherAttendance->whereNull('logout')->count(),
            'students_total' => $studentAttendance->count(),
            'teachers_total' => $teacherAttendance->count(),
        ];

        return view('admin.attendance.index', [
            'studentAttendance' => $processedStudentData['attendance'],
            'teacherAttendance' => $processedTeacherData['attendance'],
            'studentStats' => $processedStudentData['stats'],
            'teacherStats' => $processedTeacherData['stats'],
            'overallStats' => $overallStats,
            'collegeStats' => $processedStudentData['collegeStats']
        ]);
    }

    /**
     * Get student attendance records
     */
    private function getStudentAttendance($startOfDay, $endOfDay)
    {
        return Attendance::students()
            ->with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college', 'email', 'gender')
                      ->with('user:id,profile_picture');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get teacher attendance records
     */
    private function getTeacherAttendance($startOfDay, $endOfDay)
    {
        return Attendance::teachers()
            ->with(['teacherVisitor' => function($query) {
                $query->select('id', 'lname', 'fname', 'department', 'role', 'email', 'gender')
                      ->with('user:id,profile_picture');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get today's borrow requests
     */
    private function getTodayBorrowRequests()
    {
        return BorrowedBook::with('book:id,book_code,name')
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->get()
            ->groupBy(function($item) {
                return $item->student_id ?? $item->teacher_visitor_id;
            });
    }

    /**
     * Process student attendance data
     */
    private function processStudentAttendance($attendance, $borrowRequests)
    {
        $stats = [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'logged_out' => $attendance->whereNotNull('logout')->count(),
            'borrowed' => $attendance->where('activity', 'like', '%Borrow%')->count(),
        ];

        $collegeStats = $this->calculateCollegeStats($attendance);

        $formattedAttendance = $attendance->map(function ($record) use ($borrowRequests) {
            $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $record->student_id);

            // Build name from available data
            $student = $record->student;
            $fname = $student ? ($student->fname ?? '') : '';
            $lname = $student ? ($student->lname ?? '') : '';
            $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $record->student_id));

            return [
                'id' => $record->id,
                'user_type' => 'student',
                'identifier' => $student ? ($student->student_id ?? $record->student_id) : $record->student_id,
                'name' => $name ?: 'Unknown Student',
                'profile_picture' => $student?->user?->profile_picture,
                'fname' => $student?->fname,
                'lname' => $student?->lname,
                'college_or_dept' => $student ? ($student->college ?? '') : '',
                'gender' => $student ? ($student->gender ?? 'N/A') : 'N/A',
                'activity' => $activity,
                'time_in' => $record->login ? Carbon::parse($record->login)->format('h:i A') : 'N/A',
                'time_out' => $record->logout ? Carbon::parse($record->logout)->format('h:i A') : ''
            ];
        });

        return [
            'attendance' => $formattedAttendance,
            'stats' => $stats,
            'collegeStats' => $collegeStats
        ];
    }

    /**
     * Process teacher attendance data
     */
    private function processTeacherAttendance($attendance)
    {
        $stats = [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'logged_out' => $attendance->whereNotNull('logout')->count(),
        ];

        $formattedAttendance = $attendance->map(function ($record) {
            // Build name from available data
            $teacher = $record->teacherVisitor;
            $fname = $teacher ? ($teacher->fname ?? '') : '';
            $lname = $teacher ? ($teacher->lname ?? '') : '';
            $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $record->teacher_visitor_id));

            return [
                'id' => $record->id,
                'user_type' => 'teacher',
                'identifier' => $record->teacher_visitor_id,
                'name' => $name ?: 'Unknown Staff',
                'profile_picture' => $teacher?->user?->profile_picture,
                'fname' => $teacher?->fname,
                'lname' => $teacher?->lname,
                'college_or_dept' => $teacher ? ($teacher->department ?? '') : '',
                'role' => $teacher ? ($teacher->role ?? '') : '',
                'gender' => $teacher ? ($teacher->gender ?? 'N/A') : 'N/A',
                'activity' => $record->activity,
                'time_in' => $record->login ? Carbon::parse($record->login)->format('h:i A') : 'N/A',
                'time_out' => $record->logout ? Carbon::parse($record->logout)->format('h:i A') : ''
            ];
        });

        return [
            'attendance' => $formattedAttendance,
            'stats' => $stats
        ];
    }

    /**
     * Calculate college-wise statistics
     */
    private function calculateCollegeStats($attendance)
    {
        $colleges = ['CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA'];
        $collegeStats = [];

        foreach ($colleges as $college) {
            $collegeAttendance = $attendance->filter(function($record) use ($college) {
                return $record->student && $record->student->college === $college;
            });

            $collegeStats[$college] = [
                'present' => $collegeAttendance->whereNull('logout')->count(),
                'total' => Student::where('college', $college)->count()
            ];
        }

        return $collegeStats;
    }

    /**
     * Get activity with borrow request status
     */
    private function getActivityWithBorrowStatus($attendance, $borrowRequests, $identifier)
    {
        $activity = $attendance->activity;
        $userBorrowRequests = $borrowRequests->get($identifier, collect());

        $linkedBorrowRequests = $userBorrowRequests->where('attendance_id', $attendance->id);

        if ($linkedBorrowRequests->isNotEmpty()) {
            $mostRecentRequest = $linkedBorrowRequests->sortByDesc('created_at')->first();

            switch ($mostRecentRequest->status) {
                case 'pending':
                    $activity = 'Wait for approval';
                    break;
                case 'approved':
                    $originalActivity = $mostRecentRequest->original_activity ?? 'Borrow';
                    $activityPrefix = ($originalActivity === 'Stay&Borrow') ? 'Stay&Borrow:' : 'Borrow:';
                    $activity = $mostRecentRequest->book
                        ? $activityPrefix . $mostRecentRequest->book->book_code
                        : $attendance->activity;
                    break;
                case 'rejected':
                    $activity = 'Borrow book rejected';
                    break;
                case 'returned':
                    $activity = 'Book returned';
                    break;
            }
        }

        return $activity;
    }

    /**
     * Log attendance (login/logout) for both students and teachers
     */
    public function log(Request $request)
    {
        try {
            // Support both old format (student_id) and new format (user_type + identifier)
            $studentId = $request->input('student_id');
            $userType = $request->input('user_type');
            $identifier = $request->input('identifier');
            $activity = $request->input('activity');

            // If student_id is provided (old format), use it
            if ($studentId) {
                $userType = 'student';
                $identifier = $studentId;
            }

            if (!$userType || !$identifier || !$activity) {
                return response()->json([
                    'error' => 'Missing required fields',
                    'message' => 'user_type/student_id, identifier, and activity are required'
                ], 422);
            }
            
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();
            $now = now();

            // Find existing active session
            $attendance = $this->findActiveSession($userType, $identifier, $startOfDay, $endOfDay);

            if ($attendance) {
                // Logout
                return $this->handleLogout($attendance, $now);
            } else {
                // Login
                return $this->handleLogin($userType, $identifier, $activity, $now);
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
     * Find active attendance session
     */
    private function findActiveSession($userType, $identifier, $startOfDay, $endOfDay)
    {
        $query = Attendance::whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout')
            ->where('user_type', $userType);

        if ($userType === 'student') {
            $query->where('student_id', $identifier);
        } else {
            $query->where('teacher_visitor_id', $identifier);
        }

        return $query->first();
    }

    /**
     * Handle logout process
     */
    private function handleLogout($attendance, $now)
    {
        DB::transaction(function () use ($attendance, $now) {
            // Explicitly update only the logout field to prevent any side effects
            $attendance->update(['logout' => $now]);

            // Handle book return if borrowing
            if (str_contains($attendance->activity, 'Borrow')) {
                $this->handleBookReturn($attendance->activity);
            }
        });

        $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);
        $attendee = $attendance->attendee();

        // Send logout email notification
        try {
            $email = $attendance->getAttendeeEmail();
            if ($email) {
                Mail::to($email)->queue(
                    new AttendanceNotification($attendee, $attendance->user_type, 'logout', $now, $attendance->activity, $duration)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send logout email: ' . $e->getMessage());
        }

        // Get the user's name for the response
        $userName = $attendance->user_type === 'student' 
            ? ($attendee ? ($attendee->full_name ?? $attendee->lname . ', ' . $attendee->fname ?? 'Unknown Student') : 'Unknown Student')
            : ($attendee ? ($attendee->full_name ?? $attendee->lname . ', ' . $attendee->fname ?? 'Unknown Staff') : 'Unknown Staff');

        // Prepare the response data
        $responseData = [
            'success' => true,
            'action' => 'logged out',
            'user_type' => $attendance->user_type,
            'name' => $userName,
            'identifier' => $attendance->user_type === 'student' 
                ? $attendance->student_id 
                : $attendance->teacher_visitor_id,
            'time' => $now->format('g:i A'),
            'attendance_id' => $attendance->id,
            'activity' => $attendance->activity
        ];

        // Broadcast the attendance update
        try {
            // Format the record for the frontend
            $record = [
                'id' => $attendance->id,
                'login' => $attendance->login,
                'logout' => $attendance->logout,
                'activity' => $attendance->activity,
                'status' => $attendance->logout ? 'out' : 'in',
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at,
            ];

            if ($attendance->user_type === 'student') {
                // Build name from available data
                $student = $attendance->student;
                $fname = $student ? ($student->fname ?? '') : '';
                $lname = $student ? ($student->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $attendance->student_id));

                $record['student_id'] = $attendance->student_id;
                $record['student'] = [
                    'name' => $name ?: 'Unknown Student',
                    'section' => $student ? ($student->section ?? null) : null,
                    'college' => $student ? ($student->college ?? null) : null,
                    'course' => $student ? ($student->course ?? null) : null,
                ];
            } else {
                // Build name from available data
                $teacher = $attendance->teacherVisitor;
                $fname = $teacher ? ($teacher->fname ?? '') : '';
                $lname = $teacher ? ($teacher->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $attendance->teacher_visitor_id));

                $record['teacher_id'] = $attendance->teacher_visitor_id;
                $record['teacher'] = [
                    'name' => $name ?: 'Unknown Staff',
                    'type' => $teacher ? ($teacher->type ?? 'Staff') : 'Staff',
                ];
            }

            // Broadcast the update
            broadcast(new AttendanceUpdated($record, $attendance->user_type))->toOthers();
            
            Log::info("Broadcasted attendance update", [
                'attendance_id' => $attendance->id,
                'user_type' => $attendance->user_type,
                'action' => 'logged out'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to broadcast attendance update: " . $e->getMessage(), [
                'attendance_id' => $attendance->id,
                'error' => $e->getTraceAsString()
            ]);
        }

        return response()->json($responseData);
    }

    /**
     * Handle login process
     */
    private function handleLogin($userType, $identifier, $activity, $now)
    {
        $attendanceData = [
            'user_type' => $userType,
            'activity' => $activity,
            'login' => $now,
        ];

        if ($userType === 'student') {
            $attendanceData['student_id'] = $identifier;
            $attendee = Student::where('student_id', $identifier)->first();
        } else {
            $attendanceData['teacher_visitor_id'] = $identifier;
            $attendee = TeacherVisitor::find($identifier);
        }

        $attendance = Attendance::create($attendanceData);

        // Send login email notification
        try {
            if ($attendee && $attendee->email) {
                Mail::to($attendee->email)->queue(
                    new AttendanceNotification($attendee, $userType, 'login', $now, $activity)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send login email: ' . $e->getMessage());
        }

        // Get the user's name for the response
        $userName = $userType === 'student' 
            ? ($attendee ? ($attendee->full_name ?? $attendee->lname . ', ' . $attendee->fname ?? 'Unknown Student') : 'Unknown Student')
            : ($attendee ? ($attendee->full_name ?? $attendee->lname . ', ' . $attendee->fname ?? 'Unknown Staff') : 'Unknown Staff');

        // Prepare the response data
        $responseData = [
            'success' => true,
            'action' => 'logged in',
            'user_type' => $userType,
            'name' => $userName,
            'identifier' => $identifier,
            'time' => $now->format('g:i A'),
            'attendance_id' => $attendance->id,
            'activity' => $attendance->activity
        ];

        // Broadcast the attendance update
        try {
            // Format the record for the frontend
            $record = [
                'id' => $attendance->id,
                'login' => $attendance->login,
                'logout' => $attendance->logout,
                'activity' => $attendance->activity,
                'status' => $attendance->logout ? 'out' : 'in',
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at,
            ];

            if ($userType === 'student') {
                // Build name from available data
                $student = $attendance->student;
                $fname = $student ? ($student->fname ?? '') : '';
                $lname = $student ? ($student->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $attendance->student_id));

                $record['student_id'] = $attendance->student_id;
                $record['student'] = [
                    'name' => $name ?: 'Unknown Student',
                    'section' => $student ? ($student->section ?? null) : null,
                    'college' => $student ? ($student->college ?? null) : null,
                    'course' => $student ? ($student->course ?? null) : null,
                ];
            } else {
                // Build name from available data
                $teacher = $attendance->teacherVisitor;
                $fname = $teacher ? ($teacher->fname ?? '') : '';
                $lname = $teacher ? ($teacher->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $attendance->teacher_visitor_id));

                $record['teacher_id'] = $attendance->teacher_visitor_id;
                $record['teacher'] = [
                    'name' => $name ?: 'Unknown Staff',
                    'type' => $teacher ? ($teacher->type ?? 'Staff') : 'Staff',
                ];
            }

            // Broadcast the update
            broadcast(new AttendanceUpdated($record, $userType))->toOthers();
            
            Log::info("Broadcasted attendance update", [
                'attendance_id' => $attendance->id,
                'user_type' => $userType,
                'action' => 'logged in'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to broadcast attendance update: " . $e->getMessage(), [
                'attendance_id' => $attendance->id,
                'error' => $e->getTraceAsString()
            ]);
        }

        return response()->json($responseData);
    }

    /**
     * Handle book return when user logs out
     */
    private function handleBookReturn($activity)
    {
        $parts = explode(':', $activity);
        if (count($parts) > 1) {
            $bookCode = trim($parts[1]);
            BorrowedBook::where('book_id', $bookCode)
                ->where('status', 'approved')
                ->update([
                    'status' => 'returned',
                    'returned_at' => now()
                ]);
        }
    }

    /**
     * Check if user has active session
     */
    public function check(Request $request)
    {
        // Support both old format (student_id) and new format (user_type + identifier)
        $studentId = $request->query('student_id');
        $userType = $request->query('user_type');
        $identifier = $request->query('identifier');

        // If only student_id is provided (legacy), auto-detect user type
        if ($studentId && !$userType) {
            $identifier = $studentId;
            if (\App\Models\Student::where('student_id', $identifier)->exists()) {
                $userType = 'student';
            } else if (\App\Models\TeacherVisitor::find($identifier)) {
                $userType = 'teacher';
            }
        }

        if (!$userType || !$identifier) {
            return response()->json(['error' => 'User type and identifier are required'], 400);
        }

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $attendance = $this->findActiveSession($userType, $identifier, $startOfDay, $endOfDay);

        return response()->json([
            'hasActiveSession' => (bool) $attendance,
            'user_type' => $userType,
            'identifier' => $identifier,
            'activity' => $attendance ? $attendance->activity : null
        ]);
    }

    /**
     * Scan QR code and return user info
     */
    public function scan(Request $request)
    {
        // Support both old format (student_id) and new format (user_type + identifier)
        $studentId = $request->query('student_id');
        $userType = $request->query('user_type');
        $identifier = $request->query('identifier');

        // If only student_id is provided (legacy), auto-detect user type
        if ($studentId && !$userType) {
            $identifier = $studentId;
            if (\App\Models\Student::where('student_id', $identifier)->exists()) {
                $userType = 'student';
            } else if (\App\Models\TeacherVisitor::find($identifier)) {
                $userType = 'teacher';
            }
        }

        if (!$userType || !$identifier) {
            return response()->json(['error' => 'User type and identifier are required'], 400);
        }

        try {
            if ($userType === 'student') {
                $user = Student::with('user')->where('student_id', $identifier)->first();
                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                return response()->json([
                    'students' => $user, // Keep for compatibility
                    'user_type' => 'student',
                    'user' => $user,
                    'student_name' => $user->fname . ' ' . $user->lname,
                    'profile_picture' => $user->user?->profile_picture,
                    'name' => $user->fname . ' ' . $user->lname,
                    'identifier' => $user->student_id
                ]);
            } else {
                $user = TeacherVisitor::with('user')->find($identifier);
                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                return response()->json([
                    'user_type' => 'teacher',
                    'user' => $user,
                    'profile_picture' => $user->user?->profile_picture,
                    'name' => $user->fname . ' ' . $user->lname,
                    'identifier' => $user->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Scan error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save and reset today's attendance
     */
    public function saveAndReset()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $studentAttendance = $this->getStudentAttendance($startOfDay, $endOfDay);
        $teacherAttendance = $this->getTeacherAttendance($startOfDay, $endOfDay);
        $borrowRequests = $this->getTodayBorrowRequests();

        // Save to history
        $this->saveAttendanceHistory($studentAttendance, $teacherAttendance, $borrowRequests);

        // Delete today's records
        $studentAttendance->each->delete();
        $teacherAttendance->each->delete();

        // Reset study area availability (all active sessions end, so reset to max capacity)
        $this->resetStudyAreaAvailability();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance records saved and reset successfully.');
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

            Log::info('Study area availability reset during attendance save and reset', [
                'max_capacity' => $studyArea->max_capacity,
                'reset_slots' => $studyArea->available_slots
            ]);

        } catch (\Exception $e) {
            Log::error('Study area reset failed during attendance save and reset', [
                'error' => $e->getMessage()
            ]);
            // Don't throw exception here as it's not critical to the main attendance reset
        }
    }

    /**
     * Save attendance to history
     */
    private function saveAttendanceHistory($studentAttendance, $teacherAttendance, $borrowRequests)
    {
        $today = Carbon::today()->toDateString();

        // Save student records
        foreach ($studentAttendance as $record) {
            $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $record->student_id);

            // Calculate duration in minutes if both time_in and time_out exist
            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'student',
                'student_id' => $record->student_id,
                'college' => $record->student->college,
                'gender' => $record->student->gender,
                'activity' => $activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'duration' => $duration,
                'date' => $today
            ]);
        }

        // Save teacher records
        foreach ($teacherAttendance as $record) {
            // Calculate duration in minutes if both time_in and time_out exist
            $duration = null;
            if ($record->login && $record->logout) {
                $duration = Carbon::parse($record->login)->diffInMinutes(Carbon::parse($record->logout));
            }

            AttendanceHistory::create([
                'user_type' => 'teacher',
                'teacher_visitor_id' => $record->teacher_visitor_id,
                'department' => $record->teacherVisitor->department,
                'role' => $record->teacherVisitor->role,
                'gender' => $record->teacherVisitor->gender,
                'activity' => $record->activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'duration' => $duration,
                'date' => $today
            ]);
        }
    }

    /**
     * Return available books (active and not currently borrowed) for quick borrowing.
     */
    public function availableBooks(Request $request)
    {
        try {
            $limit = (int) ($request->query('limit', 30));
            $search = trim((string) $request->query('search', ''));
            $college = trim((string) $request->query('college', ''));

            $query = Books::active();

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('author', 'like', "%$search%")
                      ->orWhere('book_code', 'like', "%$search%")
                      ->orWhere('section', 'like', "%$search%");
                });
            }

            if ($college !== '') {
                // Map college filter to section for now
                $query->where('section', 'like', "%$college%");
            }

            // Exclude currently borrowed (approved & not returned)
            $query->whereNotIn('book_code', function ($sub) {
                $sub->select('book_id')
                    ->from('borrowed_books')
                    ->whereNull('returned_at')
                    ->where('status', 'approved');
            });

            $books = $query->orderBy('name')->limit($limit)->get(['book_code','name','author','section','image1']);

            return response()->json([
                'data' => $books,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available books: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => 'Failed to fetch available books.'
            ], 500);
        }
    }

    /**
     * Get list of colleges for filtering books
     */
    public function colleges()
    {
        try {
            $colleges = Books::active()
                ->select('section')
                ->distinct()
                ->whereNotNull('section')
                ->where('section', '!=', '')
                ->orderBy('section')
                ->pluck('section')
                ->filter()
                ->values()
                ->toArray();

            return response()->json($colleges);
        } catch (\Exception $e) {
            Log::error('Error fetching colleges: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => 'Failed to fetch colleges.'
            ], 500);
        }
    }

    /**
     * Get realtime attendance data for both students and teachers
     */
    public function realtime()
    {
        try {
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            // Get attendance records for both students and teachers
            $studentAttendance = $this->getStudentAttendance($startOfDay, $endOfDay);
            $teacherAttendance = $this->getTeacherAttendance($startOfDay, $endOfDay);

            // Get borrow requests
            $borrowRequests = $this->getTodayBorrowRequests();

            // Process and format data
            $processedStudentData = $this->processStudentAttendance($studentAttendance, $borrowRequests);
            $processedTeacherData = $this->processTeacherAttendance($teacherAttendance);

            // Format the response to match what the frontend expects
            $formattedStudentAttendance = $studentAttendance->map(function($record) {
                // Build name from available data
                $student = $record->student;
                $fname = $student ? ($student->fname ?? '') : '';
                $lname = $student ? ($student->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $record->student_id));

                return [
                    'student_id' => $record->student_id,
                    'student' => [
                        'name' => $name ?: 'Unknown Student',
                        'section' => $student ? ($student->section ?? null) : null,
                        'college' => $student ? ($student->college ?? null) : null,
                        'course' => $student ? ($student->course ?? null) : null,
                    ],
                    'profile_picture' => $student?->user?->profile_picture,
                    'gender' => $student ? ($student->gender ?? 'N/A') : 'N/A',
                    'time_in' => $record->login,
                    'time_out' => $record->logout,
                    'activity' => $record->activity,
                    'status' => $record->logout ? 'out' : 'in'
                ];
            });

            $formattedTeacherAttendance = $teacherAttendance->map(function($record) use ($borrowRequests) {
                // Build name from available data
                $teacher = $record->teacherVisitor;
                $fname = $teacher ? ($teacher->fname ?? '') : '';
                $lname = $teacher ? ($teacher->lname ?? '') : '';
                $name = trim(($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $record->teacher_visitor_id));

                // Get activity with borrow status
                $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $record->teacher_visitor_id);

                return [
                    'id' => $record->id,
                    'teacher_id' => $record->teacher_visitor_id,
                    'teacher' => [
                        'name' => $name ?: 'Unknown Staff',
                        'type' => $teacher ? ($teacher->type ?? 'teacher') : 'teacher',
                        'department' => $teacher ? ($teacher->department ?? null) : null,
                        'department_name' => $teacher ? ($teacher->department ?? null) : null, // Alias for compatibility
                    ],
                    'profile_picture' => $teacher?->user?->profile_picture,
                    'gender' => $teacher ? ($teacher->gender ?? 'N/A') : 'N/A',
                    'time_in' => $record->login,
                    'time_out' => $record->logout,
                    'activity' => $activity, // Use the processed activity
                    'status' => $record->logout ? 'out' : 'in',
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'studentAttendance' => $formattedStudentAttendance,
                    'teacherAttendance' => $formattedTeacherAttendance
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in realtime attendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate logout process by generating and emailing verification code
     */
public function initiateLogout(Request $request)
{
    try {
        $request->validate([
            'student_id' => 'nullable|string',
            'user_type' => 'nullable|in:student,teacher',
            'identifier' => 'nullable|string',
        ]);

        $userType = $request->input('user_type');
        $identifier = $request->input('identifier');

        if ($request->filled('student_id') && !$userType) {
            $identifier = $request->input('student_id');
            // Auto-detect: prefer student match; if not found, try teacher/visitor
            $existsStudent = Student::where('student_id', $identifier)->exists();
            if ($existsStudent) {
                $userType = 'student';
            } else {
                $maybeTeacher = \App\Models\TeacherVisitor::find($identifier);
                if ($maybeTeacher) {
                    $userType = 'teacher';
                }
            }
        }

        if (!$userType || !$identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Missing user information.'
            ], 422);
        }

        if ($userType === 'student') {
            $attendee = Student::where('student_id', $identifier)->first();
        } else {
            $attendee = \App\Models\TeacherVisitor::find($identifier);
        }

        if (!$attendee) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($userType) . ' not found.'
            ], 404);
        }

        $email = trim($attendee->email ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($userType) . ' does not have a valid email address. Please contact library staff.'
            ], 400);
        }

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $query = Attendance::where('user_type', $userType)
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout');
        if ($userType === 'student') {
            $query->where('student_id', $identifier);
        } else {
            $query->where('teacher_visitor_id', $identifier);
        }
        $activeAttendance = $query->first();

        if (!$activeAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active attendance session found for today.'
            ], 400);
        }

        // If 2FA is disabled, immediately logout and return success
        $twoFaEnabled = \Illuminate\Support\Facades\Cache::get('logout_2fa_enabled', true);
        if (!$twoFaEnabled) {
            $now = now();
            $activeAttendance->logout = $now;
            $activeAttendance->save();

            // Study area availability and borrow return handling remain the same as confirm
            if (StudyAreaHelper::isStudyActivity($activeAttendance->activity)) {
                StudyAreaHelper::updateAvailability(null, 'increment', 1);
            }

            if (str_contains($activeAttendance->activity, 'Borrow')) {
                $parts = explode(':', $activeAttendance->activity);
                if (count($parts) > 1) {
                    $bookCode = trim($parts[1]);
                    BorrowedBook::where('book_id', $bookCode)
                        ->where('status', 'approved')
                        ->update([
                            'status' => 'returned',
                            'returned_at' => $now
                        ]);
                }
            }

            try {
                $duration = $activeAttendance->login->diffForHumans($now, ['parts' => 2]);
                Mail::to($attendee->email)->queue(new AttendanceNotification($attendee, $userType, 'logout', $now, $activeAttendance->activity, $duration));
            } catch (\Exception $e) {
                \Log::error('Failed to send logout notification (2FA off): ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout completed successfully (2FA disabled).',
                'logout_time' => $now->setTimezone('Asia/Manila')->format('h:i A')
            ]);
        }

        $cacheKey = 'logout_code_' . $userType . '_' . $attendee->id;
        $existingCode = Cache::get($cacheKey);
        if ($existingCode && ($existingCode['attempts'] ?? 0) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'A verification code has already been sent. Please check your email.'
            ], 400);
        }

        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($cacheKey, [
            'code' => $code,
            'attempts' => 0,
            'attendance_id' => $activeAttendance->id,
            'user_type' => $userType,
            'attendee_id' => $attendee->id,
        ], now()->addMinutes(2));

        try {
            Mail::to($attendee->email)->send(new \App\Mail\LogoutVerificationCode($attendee, $code));
        } catch (\Throwable $e1) {
            try {
                \Mail::raw(
                    "Your " . config('app.name') . " logout verification code is: {$code}\nThis code expires in 2 minutes.",
                    function($message) use ($attendee) {
                        $message->to($attendee->email)
                                ->subject('Your Logout Verification Code - ' . config('app.name'));
                    }
                );
            } catch (\Throwable $e2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again or contact library staff.'
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code has been sent to your email.',
            'email' => $this->maskEmail($attendee->email),
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid request data.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error in initiateLogout: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server error occurred.'
        ], 500);
    }
}

// Add this helper method to mask email for security
private function maskEmail($email)
{
    $parts = explode('@', $email);
    if (count($parts) != 2) {
        return $email;
    }
    
    $username = $parts[0];
    $domain = $parts[1];
    
    $maskedUsername = substr($username, 0, 2) . str_repeat('*', max(0, strlen($username) - 4)) . substr($username, -2);
    
    return $maskedUsername . '@' . $domain;
}

    /**
     * Confirm logout by verifying the code and updating attendance
     */
    public function confirmLogout(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'nullable|string',
                'user_type' => 'nullable|in:student,teacher',
                'identifier' => 'nullable|string',
                'code' => 'required|string|size:6',
            ]);

            $userType = $request->input('user_type');
            $identifier = $request->input('identifier');
            if ($request->filled('student_id') && !$userType) {
                $identifier = $request->input('student_id');
                $existsStudent = Student::where('student_id', $identifier)->exists();
                if ($existsStudent) {
                    $userType = 'student';
                } else {
                    $maybeTeacher = \App\Models\TeacherVisitor::find($identifier);
                    if ($maybeTeacher) {
                        $userType = 'teacher';
                    }
                }
            }

            if ($userType === 'student') {
                $attendee = Student::where('student_id', $identifier)->first();
            } else {
                $attendee = \App\Models\TeacherVisitor::find($identifier);
            }

            if (!$attendee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            $cacheKey = 'logout_code_' . $userType . '_' . $attendee->id;
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new one.'
                ], 400);
            }

            // Check attempts
            if ($cachedData['attempts'] >= 3) {
                Cache::forget($cacheKey); // Clear the cache
                return response()->json([
                    'success' => false,
                    'message' => 'Too many failed attempts. Please request a new verification code.'
                ], 429);
            }

            // Verify code
            if ($cachedData['code'] !== $request->code) {
                $cachedData['attempts']++;
                Cache::put($cacheKey, $cachedData, now()->addMinutes(2));

                $remainingAttempts = 3 - $cachedData['attempts'];
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code. ' . $remainingAttempts . ' attempts remaining.'
                ], 400);
            }

            // Code is correct, proceed with logout
            $attendance = Attendance::find($cachedData['attendance_id']);

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found.'
                ], 404);
            }

            // Update logout time
            $now = now();
            $attendance->logout = $now;
            $attendance->save();

            // Clear cache
            Cache::forget($cacheKey);

            // If the activity was study-related, increment available study slots
            if (StudyAreaHelper::isStudyActivity($attendance->activity)) {
                StudyAreaHelper::updateAvailability(null, 'increment', 1);
            }

            // Handle book returns if applicable
            if (str_contains($attendance->activity, 'Borrow')) {
                $parts = explode(':', $attendance->activity);
                if (count($parts) > 1) {
                    $bookCode = trim($parts[1]);
                    BorrowedBook::where('book_id', $bookCode)
                        ->where('status', 'approved')
                        ->update([
                            'status' => 'returned',
                            'returned_at' => $now
                        ]);
                }
            }

            // Send logout notification email
            try {
                $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);
                Mail::to($attendee->email)->queue(new AttendanceNotification($attendee, $userType, 'logout', $now, $attendance->activity, $duration));
            } catch (\Exception $e) {
                Log::error('Failed to send logout notification email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout confirmed successfully.',
                'logout_time' => $now->setTimezone('Asia/Manila')->format('h:i A')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in confirmLogout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.'
            ], 500);
        }
    }

    /**
     * Verify logout code (used by frontend modal)
     */
    public function verifyLogout(Request $request)
    {
        try {
            // If 2FA is disabled, do not accept verification
            if (\Illuminate\Support\Facades\Cache::get('logout_2fa_enabled', true) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout 2FA is disabled.'
                ], 400);
            }
            $request->validate([
                'student_id' => 'nullable|string',
                'user_type' => 'nullable|in:student,teacher',
                'identifier' => 'nullable|string',
                'code' => 'required|string|size:6',
            ]);

            $userType = $request->input('user_type');
            $identifier = $request->input('identifier');
            if ($request->filled('student_id') && !$userType) {
                $identifier = $request->input('student_id');
                if (Student::where('student_id', $identifier)->exists()) {
                    $userType = 'student';
                } else if (\App\Models\TeacherVisitor::find($identifier)) {
                    $userType = 'teacher';
                }
            }

            if ($userType === 'student') {
                $attendee = Student::where('student_id', $identifier)->first();
            } else {
                $attendee = \App\Models\TeacherVisitor::find($identifier);
            }

            if (!$attendee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            $cacheKey = 'logout_code_' . $userType . '_' . $attendee->id;
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new one.'
                ], 400);
            }

            // Check attempts
            if ($cachedData['attempts'] >= 3) {
                Cache::forget($cacheKey); // Clear the cache
                return response()->json([
                    'success' => false,
                    'message' => 'Too many failed attempts. Please request a new verification code.'
                ], 429);
            }

            // Verify code
            if ($cachedData['code'] !== $request->code) {
                $cachedData['attempts']++;
                Cache::put($cacheKey, $cachedData, now()->addMinutes(2));

                $remainingAttempts = 3 - $cachedData['attempts'];
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code. ' . $remainingAttempts . ' attempts remaining.'
                ], 400);
            }

            // Code is correct, proceed with logout
            $attendance = Attendance::find($cachedData['attendance_id']);

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found.'
                ], 404);
            }

            // Update logout time
            $now = now();
            $attendance->logout = $now;
            $attendance->save();

            // Clear cache
            Cache::forget($cacheKey);

            // If the activity was study-related, increment available study slots
            if (StudyAreaHelper::isStudyActivity($attendance->activity)) {
                StudyAreaHelper::updateAvailability(null, 'increment', 1);
            }

            // Handle book returns if applicable
            if (str_contains($attendance->activity, 'Borrow')) {
                $parts = explode(':', $attendance->activity);
                if (count($parts) > 1) {
                    $bookCode = trim($parts[1]);
                    BorrowedBook::where('book_id', $bookCode)
                        ->where('status', 'approved')
                        ->update([
                            'status' => 'returned',
                            'returned_at' => $now
                        ]);
                }
            }

            // Send logout notification email
            try {
                $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);
                \Illuminate\Support\Facades\Mail::to($attendee->email)->queue(new AttendanceNotification($attendee, $userType, 'logout', $now, $attendance->activity, $duration));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send logout notification email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout confirmed successfully.',
                'logout_time' => $now->setTimezone('Asia/Manila')->format('h:i A')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in verifyLogout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.'
            ], 500);
        }
    }

    /**
     * Resend logout verification code
     */
    public function resendLogoutCode(Request $request)
    {
        try {
            // If 2FA is disabled, block resend
            if (\Illuminate\Support\Facades\Cache::get('logout_2fa_enabled', true) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout 2FA is disabled.'
                ], 400);
            }
            $request->validate([
                'student_id' => 'nullable|string',
                'user_type' => 'nullable|in:student,teacher',
                'identifier' => 'nullable|string',
            ]);

            // Resolve attendee (student or teacher)
            $userType = $request->input('user_type');
            $identifier = $request->input('identifier');
            if ($request->filled('student_id')) {
                $userType = 'student';
                $identifier = $request->input('student_id');
            }

            if (!$userType || !$identifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing user information.'
                ], 422);
            }

            if ($userType === 'student') {
                $attendee = Student::where('student_id', $identifier)->first();
            } else {
                $attendee = \App\Models\TeacherVisitor::find($identifier);
            }

            if (!$attendee) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($userType) . ' not found.'
                ], 404);
            }

            // Check if user has an active session today
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            $activeAttendanceQuery = Attendance::where('user_type', $userType)
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->whereNull('logout');
            if ($userType === 'student') {
                $activeAttendanceQuery->where('student_id', $identifier);
            } else {
                $activeAttendanceQuery->where('teacher_visitor_id', $identifier);
            }
            $activeAttendance = $activeAttendanceQuery->first();

            if (!$activeAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active attendance session found for today.'
                ], 400);
            }

            // Generate new 6-digit code
            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            \Illuminate\Support\Facades\Log::info('Generated new logout code for resend', [
                'user_type' => $userType,
                'attendee_id' => $attendee->id,
                'code' => $code,
                'timestamp' => now()
            ]);

            // Store code in cache with attempts counter (expires in 2 minutes)
            $cacheKey = 'logout_code_' . $userType . '_' . $attendee->id;
            Cache::put($cacheKey, [
                'code' => $code,
                'attempts' => 0,
                'attendance_id' => $activeAttendance->id,
                'user_type' => $userType,
                'attendee_id' => $attendee->id
            ], now()->addMinutes(2));

            // Ensure email is valid
            $email = trim($attendee->email ?? '');
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($userType) . ' does not have a valid email address.'
                ], 400);
            }

            // Send email synchronously with mailable and raw fallback
            try {
                Mail::to($attendee->email)->send(new \App\Mail\LogoutVerificationCode($attendee, $code));
                Log::info('Logout code email sent successfully for resend', [
                    'user_type' => $userType,
                    'attendee_id' => $attendee->id,
                    'email' => $attendee->email,
                ]);
            } catch (\Throwable $e1) {
                Log::warning('Resend mailable failed, attempting raw fallback', [
                    'user_type' => $userType,
                    'attendee_id' => $attendee->id,
                    'email' => $attendee->email,
                    'error' => $e1->getMessage(),
                ]);
                try {
                    \Mail::raw(
                        "Your CSU Library logout verification code is: {$code}\nThis code expires in 2 minutes.",
                        function ($message) use ($attendee) {
                            $message->to($attendee->email)
                                    ->subject('Your Logout Verification Code - ' . config('app.name'));
                        }
                    );
                    Log::info('Raw logout code email sent as fallback for resend', [
                        'user_type' => $userType,
                        'attendee_id' => $attendee->id,
                        'email' => $attendee->email
                    ]);
                } catch (\Throwable $e2) {
                    Log::error('Failed to send logout code email for resend after fallback', [
                        'user_type' => $userType,
                        'attendee_id' => $attendee->id,
                        'email' => $attendee->email,
                        'mailable_error' => $e1->getMessage(),
                        'raw_error' => $e2->getMessage(),
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to resend verification code. Please contact library staff.'
                    ], 500);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification code resent to your email. The code will expire in 2 minutes.',
                'email_sent_to' => $this->maskEmail($attendee->email)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in resendLogoutCode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.'
            ], 500);
        }
    }
}
