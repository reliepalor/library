<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Student;
use App\Models\TeacherVisitor;
use App\Models\BorrowedBook;
use App\Mail\AttendanceNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Events\AttendanceUpdated;

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
                $query->select('student_id', 'lname', 'fname', 'college', 'email')
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
                $query->select('id', 'lname', 'fname', 'department', 'role', 'email');
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
            
            return [
                'id' => $record->id,
                'user_type' => 'student',
                'identifier' => $record->student->student_id ?? 'N/A',
                'name' => $record->getAttendeeName(),
                'profile_picture' => $record->student?->user?->profile_picture,
                'college_or_dept' => $record->student->college ?? 'N/A',
                'activity' => $activity,
                'time_in' => $record->login ? Carbon::parse($record->login)->format('h:i A') : 'N/A',
                'time_out' => $record->logout ? Carbon::parse($record->logout)->format('h:i A') : 'N/A'
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
            return [
                'id' => $record->id,
                'user_type' => 'teacher',
                'identifier' => $record->teacher_visitor_id,
                'name' => $record->getAttendeeName(),
                'profile_picture' => null,
                'college_or_dept' => $record->teacherVisitor->department ?? 'N/A',
                'role' => $record->teacherVisitor->role ?? 'N/A',
                'activity' => $record->activity,
                'time_in' => $record->login ? Carbon::parse($record->login)->format('h:i A') : 'N/A',
                'time_out' => $record->logout ? Carbon::parse($record->logout)->format('h:i A') : 'N/A'
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
                    $activity = $mostRecentRequest->book 
                        ? 'Borrow: ' . $mostRecentRequest->book->book_code 
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
            $attendance->logout = $now;
            $attendance->save();

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
            ? ($attendance->student->name ?? 'Unknown Student')
            : ($attendance->teacherVisitor->name ?? 'Unknown Staff');

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
                $record['student_id'] = $attendance->student_id;
                $record['student'] = [
                    'name' => $attendance->student->name ?? 'N/A',
                    'section' => $attendance->student->section ?? null,
                    'college' => $attendance->student->college ?? null,
                    'course' => $attendance->student->course ?? null,
                ];
            } else {
                $record['teacher_id'] = $attendance->teacher_visitor_id;
                $record['teacher'] = [
                    'name' => $attendance->teacherVisitor->name ?? 'N/A',
                    'type' => $attendance->teacherVisitor->type ?? 'Staff',
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
            ? ($attendance->student->name ?? 'Unknown Student')
            : ($attendance->teacherVisitor->name ?? 'Unknown Staff');

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
                $record['student_id'] = $attendance->student_id;
                $record['student'] = [
                    'name' => $attendance->student->name ?? 'N/A',
                    'section' => $attendance->student->section ?? null,
                    'college' => $attendance->student->college ?? null,
                    'course' => $attendance->student->course ?? null,
                ];
            } else {
                $record['teacher_id'] = $attendance->teacher_visitor_id;
                $record['teacher'] = [
                    'name' => $attendance->teacherVisitor->name ?? 'N/A',
                    'type' => $attendance->teacherVisitor->type ?? 'Staff',
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

        // If student_id is provided (old format), use it
        if ($studentId) {
            $userType = 'student';
            $identifier = $studentId;
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

        // If student_id is provided (old format), use it
        if ($studentId) {
            $userType = 'student';
            $identifier = $studentId;
        }

        if (!$userType || !$identifier) {
            return response()->json(['error' => 'User type and identifier are required'], 400);
        }

        try {
            if ($userType === 'student') {
                $user = Student::with('user')->where('student_id', $identifier)->first();
                
                if (!$user) {
                    return response()->json(['error' => 'Student not found'], 404);
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
                $user = TeacherVisitor::find($identifier);
                
                if (!$user) {
                    return response()->json(['error' => 'Teacher/Visitor not found'], 404);
                }

                return response()->json([
                    'user_type' => 'teacher',
                    'user' => $user,
                    'profile_picture' => null,
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

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance records saved and reset successfully.');
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
            
            AttendanceHistory::create([
                'user_type' => 'student',
                'student_id' => $record->student_id,
                'college' => $record->student->college,
                'activity' => $activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'date' => $today
            ]);
        }

        // Save teacher records
        foreach ($teacherAttendance as $record) {
            AttendanceHistory::create([
                'user_type' => 'teacher',
                'teacher_visitor_id' => $record->teacher_visitor_id,
                'department' => $record->teacherVisitor->department,
                'role' => $record->teacherVisitor->role,
                'activity' => $record->activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
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
                return [
                    'student_id' => $record->student_id,
                    'student' => [
                        'name' => $record->student->name ?? 'N/A',
                        'section' => $record->student->section ?? null,
                        'college' => $record->student->college ?? null,
                        'course' => $record->student->course ?? null,
                    ],
                    'time_in' => $record->login,
                    'time_out' => $record->logout,
                    'activity' => $record->activity,
                    'status' => $record->logout ? 'out' : 'in'
                ];
            });

            $formattedTeacherAttendance = $teacherAttendance->map(function($record) {
                return [
                    'teacher_id' => $record->teacher_visitor_id,
                    'teacher' => [
                        'name' => $record->teacherVisitor->name ?? 'N/A',
                        'type' => $record->teacherVisitor->type ?? 'teacher',
                    ],
                    'time_in' => $record->login,
                    'time_out' => $record->logout,
                    'activity' => $record->activity,
                    'status' => $record->logout ? 'out' : 'in'
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
            \Log::error('Error in realtime attendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
