<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\AttendanceNotification;
use App\Mail\LogoutCodeMail;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Collection;
use App\Models\Books;
use App\Services\AvatarService;
use App\Helpers\StudyAreaHelper;

class AttendanceController extends Controller
{
    public function index()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $todayStudentAttendance = $this->getTodayStudentAttendance($startOfDay, $endOfDay);
        $todayTeacherAttendance = $this->getTodayTeacherAttendance($startOfDay, $endOfDay);

        $borrowRequests = $this->getTodayBorrowRequests();

        $studentProcessedData = $this->processStudentAttendanceData($todayStudentAttendance, $borrowRequests);
        $teacherProcessedData = $this->processTeacherAttendanceData($todayTeacherAttendance);

        $processedData = [
            'todayAttendance' => $studentProcessedData['todayAttendance']->merge($teacherProcessedData['todayAttendance']),
            'stats' => [
                'total' => $studentProcessedData['stats']['total'] + $teacherProcessedData['stats']['total'],
                'present' => $studentProcessedData['stats']['present'] + $teacherProcessedData['stats']['present'],
                'absent' => $studentProcessedData['stats']['logged_out'] + $teacherProcessedData['stats']['logged_out'],
                'borrowed' => $studentProcessedData['todayAttendance']->where('activity', 'like', '%Borrow%')->count(),
            ],
            'collegeStats' => $this->calculateCollegeStats($todayStudentAttendance), // Only for students
            'studentAttendance' => $studentProcessedData['todayAttendance'],
            'teacherAttendance' => $teacherProcessedData['todayAttendance']
        ];

        return view('admin.attendance.index', $processedData);
    }

    private function getTodayAttendance($startOfDay, $endOfDay)
    {
        return Attendance::with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college', 'email', 'gender')->with('user');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();
    }

 
    private function getTodayBorrowRequests()
    {
        return \App\Models\BorrowedBook::with('book:id,book_code,name')
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->get()
            ->groupBy('student_id');
    }

 
    private function processAttendanceData($todayAttendance, $borrowRequests)
    {
        $stats = $this->calculateAttendanceStats($todayAttendance);

        $collegeStats = $this->calculateCollegeStats($todayAttendance);

        $formattedAttendance = $this->formatAttendanceData($todayAttendance, $borrowRequests);

        return [
            'todayAttendance' => $formattedAttendance,
            'stats' => $stats,
            'collegeStats' => $collegeStats
        ];
    }

   
    private function calculateAttendanceStats($attendance)
    {
        return [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'absent' => $attendance->whereNotNull('logout')->count(),
            'borrowed' => $attendance->where('activity', 'like', '%Borrow%')->count(),
        ];
    }

   
    private function calculateCollegeStats($attendance)
    {
        $colleges = ['CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA'];
        $collegeStats = [];

        foreach ($colleges as $college) {
            $collegeAttendance = $attendance->filter(function($attendance) use ($college) {
                return $attendance->student && $attendance->student->college === $college;
            });

            $collegeStats[$college] = [
                'present' => $collegeAttendance->whereNull('logout')->count(),
                'total' => Student::where('college', $college)->count()
            ];
        }

        return $collegeStats;
    }

    
    private function formatAttendanceData($attendance, $borrowRequests)
    {
        return $attendance->map(function ($attendance) use ($borrowRequests) {
            $studentId = $attendance->student_id;
            $activity = $this->getActivityWithBorrowStatus($attendance, $borrowRequests, $studentId);

            return [
                'id' => $attendance->id,
                'student_id' => $attendance->student->student_id ?? 'N/A',
                'student_name' => ($attendance->student->lname ?? 'N/A') . ', ' . ($attendance->student->fname ?? 'N/A'),
                'profile_picture' => $attendance->student && $attendance->student->user && $attendance->student->user->profile_picture
                    ? asset('storage/profile_pictures/' . $attendance->student->user->profile_picture)
                    : null,
                'college' => $attendance->student->college ?? 'N/A',
                'gender' => $attendance->student->gender ?? 'N/A',
                'activity' => $activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A'
            ];
        });
    }

   
    private function getActivityWithBorrowStatus($attendance, $borrowRequests, $studentId)
    {
        $activity = $attendance->activity;
        $studentBorrowRequests = $borrowRequests->get($studentId, collect());

        $linkedBorrowRequests = $studentBorrowRequests->where('attendance_id', $attendance->id);

        if ($linkedBorrowRequests->isNotEmpty()) {
            $mostRecentRequest = $linkedBorrowRequests->sortByDesc('created_at')->first();

            switch ($mostRecentRequest->status) {
                case 'pending':
                    $activity = 'Wait for approval';
                    break;
                case 'approved':
                    $activity = $mostRecentRequest->book ? 'Borrow:' . $mostRecentRequest->book->book_code : $attendance->activity;
                    break;
                case 'rejected':
                    $activity = 'Borrow book rejected';
                    break;
                case 'returned':
                    $activity = 'Book returned';
                    break;
                default:
                    $activity = $attendance->activity;
            }
        }

        return $activity;
    }

    public function history(Request $request)
    {
        try {
            $query = AttendanceHistory::with('student')
                ->when($request->date_from, function ($q) use ($request) {
                    return $q->whereDate('date', '>=', Carbon::parse($request->date_from));
                })
                ->when($request->date_to, function ($q) use ($request) {
                    return $q->whereDate('date', '<=', Carbon::parse($request->date_to));
                })
                ->when($request->college, function ($q) use ($request) {
                    return $q->whereHas('student', function ($q) use ($request) {
                        $q->where('college', $request->college);
                    });
                })
                ->when($request->activity, function ($q) use ($request) {
                    return $q->where('activity', $request->activity);
                });

            $history = $query->latest()->paginate(10)->withQueryString();
            
            // Get available filters
            $colleges = Student::distinct()->pluck('college');
            $activities = AttendanceHistory::distinct()->pluck('activity');

            return view('admin.attendance.history', compact('history', 'colleges', 'activities'));
        } catch (\Exception $e) {
            Log::error('Error in attendance history: ' . $e->getMessage());
            return view('admin.attendance.history', [
                'history' => collect(),
                'colleges' => collect(),
                'activities' => collect()
            ]);
        }
    }

    public function insights(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(7);
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

            $dailyTrends = collect();
            $collegeDistribution = collect();
            $activityDistribution = collect();

            $dailyTrends = AttendanceHistory::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('date')
                ->get();

            $collegeDistribution = AttendanceHistory::select(
                'students.college',
                DB::raw('COUNT(*) as count')
            )
                ->join('students', 'attendance_histories.student_id', '=', 'students.id')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('students.college')
                ->get();

            $activityDistribution = AttendanceHistory::select(
                'activity',
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('activity')
                ->get();

            $summary = [
                'total_attendance' => 0,
                'average_daily' => 0,
                'most_active_college' => 'N/A',
                'most_common_activity' => 'N/A'
            ];

            // Calculate summary statistics if we have data
            if ($dailyTrends->isNotEmpty()) {
                $summary['total_attendance'] = $dailyTrends->sum('count');
                $summary['average_daily'] = $dailyTrends->avg('count');
            }

            if ($collegeDistribution->isNotEmpty()) {
                $summary['most_active_college'] = $collegeDistribution->sortByDesc('count')->first()->college;
            }

            if ($activityDistribution->isNotEmpty()) {
                $summary['most_common_activity'] = $activityDistribution->sortByDesc('count')->first()->activity;
            }

            return view('admin.attendance.insights', [
                'dailyTrends' => $dailyTrends,
                'collegeDistribution' => $collegeDistribution,
                'activityDistribution' => $activityDistribution,
                'summary' => $summary,
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d')
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in attendance insights: ' . $e->getMessage());
            
            // Return view with empty data
            return view('admin.attendance.insights', [
                'dailyTrends' => collect(),
                'collegeDistribution' => collect(),
                'activityDistribution' => collect(),
                'summary' => [
                    'total_attendance' => 0,
                    'average_daily' => 0,
                    'most_active_college' => 'N/A',
                    'most_common_activity' => 'N/A'
                ],
                'dateFrom' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'dateTo' => Carbon::now()->format('Y-m-d')
            ]);
        }
    }

    public function analytics(Request $request)
    {
        // Dashboard analytics aggregated from attendance_histories
        $days = (int) $request->query('days', 30);
        $days = max(7, min(90, $days));

        $tz = 'Asia/Manila';
        $today = now($tz)->toDateString();

        // Totals
        $totalVisits = DB::table('attendance_histories')->count();
        $uniqueStudents = DB::table('attendance_histories')->distinct('student_id')->count('student_id');
        $todayVisits = DB::table('attendance_histories')
            ->whereDate('date', $today)
            ->count();

        // Average duration in minutes (only rows with time_out)
        $avgDuration = DB::table('attendance_histories')
            ->whereNotNull('time_out')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, time_in, time_out)) as avg_min')
            ->value('avg_min');

        // Activity breakdown
        $activities = DB::table('attendance_histories')
            ->select('activity', DB::raw('COUNT(*) as cnt'))
            ->groupBy('activity')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // Most active colleges (use stored college column)
        $colleges = DB::table('attendance_histories')
            ->select(DB::raw('COALESCE(college, "Unknown") as college'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('college')
            ->orderByDesc('cnt')
            ->get();

        // Daily trend for last N days using the `date` column
        $startDate = now($tz)->subDays($days - 1)->toDateString();
        $endDate = now($tz)->toDateString();
        $trend = DB::table('attendance_histories')
            ->selectRaw('DATE(`date`) as d, COUNT(*) as cnt')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // Normalize trend to include missing dates
        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $d = now($tz)->subDays($days - 1 - $i)->toDateString();
            $series[$d] = 0;
        }
        foreach ($trend as $row) {
            $d = (string) $row->d;
            if (isset($series[$d])) $series[$d] = (int) $row->cnt;
        }

        $stats = [
            'totals' => [
                'visits' => (int) $totalVisits,
                'unique_students' => (int) $uniqueStudents,
                'today' => (int) $todayVisits,
                'avg_duration_min' => $avgDuration ? round($avgDuration, 1) : 0,
            ],
            'activities' => $activities,
            'colleges' => $colleges,
            'trend' => [
                'labels' => array_keys($series),
                'data' => array_values($series),
            ],
        ];

        return view('admin.attendance.analytics', compact('stats'));
    }

    /**
     * Get chart data for analytics dashboard
     */
    public function getChartData(Request $request)
    {
        try {
            $period = $request->query('period', 'last30days');
            $type = $request->query('type', 'student'); // 'student' or 'teacher'

            // Parse period to get days
            $days = 30; // default
            switch ($period) {
                case 'today':
                    $days = 1;
                    break;
                case 'last7days':
                    $days = 7;
                    break;
                case 'last30days':
                    $days = 30;
                    break;
                case 'last90days':
                    $days = 90;
                    break;
                case 'lastyear':
                    $days = 365;
                    break;
                default:
                    // Fallback: try to parse number from period string
                    if (preg_match('/(\d+)/', $period, $matches)) {
                        $days = (int) $matches[1];
                    }
                    break;
            }
            $days = max(1, min(365, $days)); // Limit between 1 and 365 days

            $tz = 'Asia/Manila';
            $startDate = now($tz)->subDays($days - 1)->toDateString();
            $endDate = now($tz)->toDateString();

            // Base query for attendance_histories
            $baseQuery = DB::table('attendance_histories')
                ->whereBetween('date', [$startDate, $endDate]);

            // Filter by user type if specified
            if ($type === 'student') {
                $baseQuery->where('user_type', 'student');
            } elseif ($type === 'teacher') {
                $baseQuery->where('user_type', 'teacher');
            }

            // Get activities data with specific categories
            $activities = (clone $baseQuery)
                ->selectRaw("
                    CASE
                        WHEN activity LIKE '%study%' AND activity NOT LIKE '%borrow%' THEN 'Stay to Study'
                        WHEN activity LIKE '%borrow%' AND activity NOT LIKE '%study%' THEN 'Borrow Books'
                        WHEN activity LIKE '%borrow%' AND activity LIKE '%study%' THEN 'Stay and Borrow Books'
                        ELSE 'Other Activities'
                    END as activity_category,
                    COUNT(*) as cnt
                ")
                ->groupBy('activity_category')
                ->orderByDesc('cnt')
                ->get()
                ->map(function ($item) {
                    return [
                        'activity' => $item->activity_category,
                        'cnt' => $item->cnt
                    ];
                });

            // Ensure all four categories are present with 0 count if missing
            $requiredCategories = ['Stay to Study', 'Borrow Books', 'Stay and Borrow Books', 'Other Activities'];
            $existingCategories = $activities->pluck('activity')->toArray();

            foreach ($requiredCategories as $category) {
                if (!in_array($category, $existingCategories)) {
                    $activities->push([
                        'activity' => $category,
                        'cnt' => 0
                    ]);
                }
            }

            // Sort by count descending (highest first), then by predefined order for same counts
            $activities = $activities->sort(function ($a, $b) use ($requiredCategories) {
                // First sort by count (descending)
                if ($a['cnt'] !== $b['cnt']) {
                    return $b['cnt'] <=> $a['cnt'];
                }
                // If counts are equal, sort by predefined order
                return array_search($a['activity'], $requiredCategories) <=> array_search($b['activity'], $requiredCategories);
            })->values();

            // Get colleges/departments data (for both students and teachers)
            $colleges = (clone $baseQuery)
                ->select(DB::raw('COALESCE(college, department, "Unknown") as college'), DB::raw('COUNT(*) as cnt'))
                ->groupBy(DB::raw('COALESCE(college, department, "Unknown")'))
                ->orderByDesc('cnt')
                ->get();

            return response()->json([
                'activities' => $activities,
                'colleges' => $colleges
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getChartData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAndReset()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        // Get today's attendance for both students and teachers
        $todayStudentAttendance = $this->getTodayStudentAttendance($startOfDay, $endOfDay);
        $todayTeacherAttendance = $this->getTodayTeacherAttendance($startOfDay, $endOfDay);

        // Get borrow requests with optimized query
        $borrowRequests = $this->getTodayBorrowRequests();

        // Create attendance history records for students
        $this->createAttendanceHistoryRecords($todayStudentAttendance, $borrowRequests);

        // Create attendance history records for teachers
        $this->createAttendanceHistoryRecords($todayTeacherAttendance, $borrowRequests);

        // Delete today's attendance records
        $todayStudentAttendance->each->delete();
        $todayTeacherAttendance->each->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance records have been saved and reset successfully.');
    }

    /**
     * Create attendance history records with proper activity status
     */
    private function createAttendanceHistoryRecords($attendance, $borrowRequests)
    {
        $today = Carbon::today()->toDateString();

        foreach ($attendance as $record) {
            $historyData = [
                'user_type' => $record->user_type,
                'activity' => $record->activity,
                'time_in' => $record->login,
                'time_out' => $record->logout,
                'date' => $today
            ];

            if ($record->user_type === 'student') {
                $studentId = $record->student_id;
                $activity = $this->getActivityWithBorrowStatus($record, $borrowRequests, $studentId);

                $historyData['student_id'] = $record->student_id;
                $historyData['college'] = $record->student->college ?? 'N/A';
                $historyData['activity'] = $activity;
            } else {
                // For teachers/visitors
                $historyData['teacher_visitor_id'] = $record->teacher_visitor_id;
                $historyData['department'] = $record->teacherVisitor->department ?? 'N/A';
                $historyData['role'] = $record->teacherVisitor->role ?? 'N/A';
                $historyData['college'] = null; // College field is not applicable for teachers/visitors
            }

            AttendanceHistory::create($historyData);
        }
    }

    public function getHistoryData(Request $request)
    {
        $date = $request->input('date');
        $college = $request->input('college');
        $status = $request->input('status');

        // Get student history records
        $studentQuery = AttendanceHistory::query()
            ->where('user_type', 'student')
            ->join('students', 'attendance_histories.student_id', '=', 'students.student_id')
            ->select('attendance_histories.*', 'students.fname', 'students.lname');

        if ($date) {
            $studentQuery->whereDate('attendance_histories.date', $date);
        }

        if ($college) {
            $studentQuery->where('attendance_histories.college', $college);
        }

        if ($status) {
            if ($status === 'present') {
                $studentQuery->whereNull('attendance_histories.time_out');
            } else if ($status === 'logged_out') {
                $studentQuery->whereNotNull('attendance_histories.time_out');
            }
        }

        $studentHistory = $studentQuery->get()->map(function ($record) {
            return [
                'student_id' => $record->student_id,
                'student_name' => $record->lname . ', ' . $record->fname,
                'college' => $record->college,
                'activity' => $record->activity,
                'time_in' => Carbon::parse($record->time_in)->setTimezone('Asia/Manila')->format('h:i A'),
                'time_out' => $record->time_out ? Carbon::parse($record->time_out)->setTimezone('Asia/Manila')->format('h:i A') : null
            ];
        });

        // Get teacher/visitor history records
        $teacherQuery = AttendanceHistory::query()
            ->where('user_type', 'teacher')
            ->join('teachers_visitors', 'attendance_histories.teacher_visitor_id', '=', 'teachers_visitors.id')
            ->select('attendance_histories.*', 'teachers_visitors.fname', 'teachers_visitors.lname', 'teachers_visitors.department', 'teachers_visitors.role');

        if ($date) {
            $teacherQuery->whereDate('attendance_histories.date', $date);
        }

        if ($college) {
            $teacherQuery->where('attendance_histories.department', $college);
        }

        if ($status) {
            if ($status === 'present') {
                $teacherQuery->whereNull('attendance_histories.time_out');
            } else if ($status === 'logged_out') {
                $teacherQuery->whereNotNull('attendance_histories.time_out');
            }
        }

        $teacherHistory = $teacherQuery->get()->map(function ($record) {
            return [
                'student_id' => ucfirst($record->role ?? 'Staff'),
                'student_name' => $record->lname . ', ' . $record->fname,
                'college' => $record->department,
                'activity' => $record->activity,
                'time_in' => Carbon::parse($record->time_in)->setTimezone('Asia/Manila')->format('h:i A'),
                'time_out' => $record->time_out ? Carbon::parse($record->time_out)->setTimezone('Asia/Manila')->format('h:i A') : null
            ];
        });

        // Combine and sort both student and teacher records
        $combinedHistory = $studentHistory->concat($teacherHistory)->sortByDesc(function ($record) {
            return [$record['time_in'], $record['student_id']];
        })->values();

        return response()->json([
            'history' => $combinedHistory
        ]);
    }

    public function insightsData()
    {
        // Get date range from request or default to last 30 days
        $dateFrom = request('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = request('date_to', now()->format('Y-m-d'));

        // Get attendance records for the date range
        $attendance = AttendanceHistory::whereBetween('date', [$dateFrom, $dateTo])->get();

        // Calculate total attendance
        $totalAttendance = $attendance->count();

        // Calculate average duration
        $avgDuration = $attendance->avg(function($record) {
            if ($record->time_in && $record->time_out) {
                return Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
            }
            return 0;
        }) ?? 0;

        // Get most active college
        $mostActiveCollege = $attendance->groupBy('college')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? 'N/A';

        // Get peak day
        $peakDay = $attendance->groupBy('date')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? 'N/A';

        // Weekly pattern data
        $weeklyPattern = $attendance->groupBy(function($record) {
            return Carbon::parse($record->date)->format('l');
        })->map->count();

        // Duration distribution
        $durationDistribution = [
            'labels' => collect(['< 1 hour', '1-2 hours', '2-3 hours', '3-4 hours', '> 4 hours']),
            'data' => collect([
                $attendance->filter(function($record) {
                    if ($record->time_in && $record->time_out) {
                        $duration = Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
                        return $duration < 1;
                    }
                    return false;
                })->count(),
                $attendance->filter(function($record) {
                    if ($record->time_in && $record->time_out) {
                        $duration = Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
                        return $duration >= 1 && $duration < 2;
                    }
                    return false;
                })->count(),
                $attendance->filter(function($record) {
                    if ($record->time_in && $record->time_out) {
                        $duration = Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
                        return $duration >= 2 && $duration < 3;
                    }
                    return false;
                })->count(),
                $attendance->filter(function($record) {
                    if ($record->time_in && $record->time_out) {
                        $duration = Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
                        return $duration >= 3 && $duration < 4;
                    }
                    return false;
                })->count(),
                $attendance->filter(function($record) {
                    if ($record->time_in && $record->time_out) {
                        $duration = Carbon::parse($record->time_out)->diffInHours(Carbon::parse($record->time_in));
                        return $duration >= 4;
                    }
                    return false;
                })->count(),
            ])
        ];

        // College activity comparison
        $colleges = collect(['CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA']);
        $collegeActivity = [
            'labels' => $colleges,
            'data' => $colleges->map(function($college) use ($attendance) {
                return $attendance->where('college', $college)->count();
            })->values()
        ];

        // Activity trends
        $activities = collect(['Present', 'Absent', 'Borrowed']);
        $activityTrends = [
            'labels' => $attendance->pluck('date')->unique()->values(),
            'datasets' => $activities->map(function($activity) use ($attendance) {
                return [
                    'label' => $activity,
                    'data' => $attendance->pluck('date')->unique()->map(function($date) use ($attendance, $activity) {
                        return $attendance->where('date', $date)
                            ->where('activity', $activity)
                            ->count();
                    })->values(),
                    'color' => $activity === 'Present' ? '#10b981' : 
                              ($activity === 'Absent' ? '#ef4444' : '#8b5cf6')
                ];
            })
        ];

        return response()->json([
            'total_attendance' => $totalAttendance,
            'avg_duration' => round($avgDuration, 1),
            'most_active_college' => $mostActiveCollege,
            'peak_day' => Carbon::parse($peakDay)->format('F j, Y'),
            'weekly_pattern' => [
                'labels' => $weeklyPattern->keys(),
                'data' => $weeklyPattern->values()
            ],
            'duration_distribution' => $durationDistribution,
            'college_activity' => $collegeActivity,
            'activity_trends' => $activityTrends
        ]);
    }

    /**
     * Log attendance action (handles both login and logout) for admin.
     */
    public function log(Request $request)
    {
        try {
            $userType = $request->input('user_type', 'student');
            $identifier = $request->input($userType === 'student' ? 'student_id' : 'identifier');
            $activity = $request->input('activity');

            // Conditional validation
            if ($userType === 'student') {
                $request->validate([
                    'student_id' => 'required|string|exists:students,student_id',
                    'activity' => 'required|string',
                ]);
            } else {
                $request->validate([
                    'user_type' => 'required|in:student,teacher',
                    'identifier' => 'required|integer|exists:teachers_visitors,id',
                    'activity' => 'required|string',
                ]);
            }

            // Use range to enable index usage
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();
            $now = now();

            // Check for active session based on user type
            $attendance = Attendance::where('user_type', $userType)
                ->where(function($q) use ($userType, $identifier) {
                    if ($userType === 'student') {
                        $q->where('student_id', $identifier);
                    } else {
                        $q->where('teacher_visitor_id', $identifier);
                    }
                })
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->whereNull('logout')
                ->first();

            if ($attendance) {
                // Logout
                DB::transaction(function () use ($attendance, $now) {
                    $attendance->logout = $now;
                    $attendance->save();

                    // If the activity was study-related, increment available study slots
                    if (StudyAreaHelper::isStudyActivity($attendance->activity)) {
                        StudyAreaHelper::updateAvailability(null, 'increment', 1);
                    }

                    if (str_contains($attendance->activity, 'Borrow')) {
                        $parts = explode(':', $attendance->activity);
                        if (count($parts) > 1) {
                            $bookCode = trim($parts[1]);
                            \App\Models\BorrowedBook::where('book_id', $bookCode)
                                ->where('status', 'approved')
                                ->update([
                                    'status' => 'returned',
                                    'returned_at' => now()
                                ]);
                        }
                    }
                });

                $duration = $attendance->login->diffForHumans($now, ['parts' => 2]);

                // Get user for email
                $user = $this->getUserByType($userType, $identifier);

                // Send attendance notification email for logout
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->queue(new AttendanceNotification($user, 'logout', $now, $attendance->activity, $duration));
                    } catch (\Exception $e) {
                        Log::error('Failed to send logout email: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'message' => 'Logout time recorded successfully.',
                    'type' => 'logout',
                    'identifier' => $identifier,
                    'user_type' => $userType
                ]);
            } else {
                // If the activity is study-related, decrement available study slots
                if (StudyAreaHelper::isStudyActivity($activity)) {
                    StudyAreaHelper::updateAvailability(null, 'decrement', 1);
                }

                // Login - create new attendance record
                $attendanceData = [
                    'user_type' => $userType,
                    'activity' => $activity,
                    'login' => $now,
                ];

                if ($userType === 'student') {
                    $attendanceData['student_id'] = $identifier;
                } else {
                    $attendanceData['teacher_visitor_id'] = $identifier;
                }

                $attendance = Attendance::create($attendanceData);

                // Get user for email
                $user = $this->getUserByType($userType, $identifier);

                // Send attendance notification email for login
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->queue(new AttendanceNotification($user, 'login', $now, $activity));
                    } catch (\Exception $e) {
                        Log::error('Failed to send login email: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'message' => 'Login time recorded successfully.',
                    'type' => 'login',
                    'identifier' => $identifier,
                    'user_type' => $userType
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
     * Check if user has an active attendance session today (admin).
     */
    public function check(Request $request)
    {
        $userType = $request->query('user_type', 'student');
        $identifier = $request->query($userType === 'student' ? 'student_id' : 'identifier');

        if (!$identifier) {
            return response()->json(['error' => ucfirst($userType) . ' identifier is required'], 400);
        }

        // Use range to enable index usage
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $attendance = Attendance::where('user_type', $userType)
            ->where(function($q) use ($userType, $identifier) {
                if ($userType === 'student') {
                    $q->where('student_id', $identifier);
                } else {
                    $q->where('teacher_visitor_id', $identifier);
                }
            })
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout')
            ->first();

    return response()->json([
        'hasActiveSession' => (bool) $attendance,
        'identifier' => $identifier,
        'user_type' => $userType,
        'activity' => $attendance ? $attendance->activity : null,
        'attendance_id' => $attendance ? $attendance->id : null
    ]);
    }

    /**
     * Get user details for attendance scan (admin).
     */
    public function scan(Request $request)
    {
        $userType = $request->query('user_type', 'student');
        $identifier = $request->query($userType === 'student' ? 'student_id' : 'identifier');

        if (!$identifier) {
            return response()->json(['error' => ucfirst($userType) . ' identifier is required'], 400);
        }

        $user = $this->getUserByType($userType, $identifier);

        if (!$user) {
            return response()->json(['error' => ucfirst($userType) . ' not found'], 404);
        }

        // Get user's full name
        $userName = $user->fname . ' ' . $user->lname;

        // Get profile picture, fallback to generated avatar if not available
        $profilePicture = $this->getUserProfilePicture($userType, $user);
        if (!$profilePicture) {
            $profilePicture = AvatarService::getPlaceholderAvatar($userName, 100);
        }

        return response()->json([
            'user' => $user,
            'profile_picture' => $profilePicture,
            'name' => $userName,
            'user_type' => $userType
        ]);
    }

    /**
     * Get real-time attendance data for AJAX polling
     */
    public function getRealtimeAttendance(Request $request)
    {
        try {
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            // Get today's attendance for both students and teachers
            $todayStudentAttendance = Attendance::with(['student.user'])
                ->where('user_type', 'student')
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->orderBy('created_at', 'desc')
                ->orderBy('login', 'desc')
                ->get();

            $todayTeacherAttendance = Attendance::with(['teacherVisitor.user'])
                ->where('user_type', 'teacher')
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->orderBy('created_at', 'desc')
                ->orderBy('login', 'desc')
                ->get();

            // Get borrow requests with optimized query
            $borrowRequests = $this->getTodayBorrowRequests();

            // Process attendance data with borrow request status
            $studentProcessedData = $this->processStudentAttendanceData($todayStudentAttendance, $borrowRequests);
            $teacherProcessedData = $this->processTeacherAttendanceData($todayTeacherAttendance);

            return response()->json([
                'success' => true,
                'data' => [
                    'studentAttendance' => $studentProcessedData['todayAttendance'],
                    'teacherAttendance' => $teacherProcessedData['todayAttendance'],
                    'overallStats' => [
                        'total' => $studentProcessedData['stats']['total'] + $teacherProcessedData['stats']['total'],
                        'students_present' => $studentProcessedData['stats']['present'],
                        'teachers_present' => $teacherProcessedData['stats']['present']
                    ],
                    'studentStats' => $studentProcessedData['stats'],
                    'teacherStats' => $teacherProcessedData['stats'],
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getRealtimeAttendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch attendance data',
                'message' => $e->getMessage()
            ], 500);
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
     * Get available colleges for book filtering
     */
    public function booksColleges(Request $request)
    {
        try {
            $colleges = Books::active()
                ->whereNotNull('section')
                ->where('section', '!=', '')
                ->distinct()
                ->pluck('section')
                ->sort()
                ->values();

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
     * Get user by type and identifier
     */
    private function getUserByType($userType, $identifier)
    {
        if ($userType === 'student') {
            return Student::with('user')->where('student_id', $identifier)->first();
        } else {
            return \App\Models\TeacherVisitor::with('user')->where('id', $identifier)->first();
        }
    }

    /**
     * Get user profile picture
     */
    private function getUserProfilePicture($userType, $user)
    {
        if ($user->user && $user->user->profile_picture) {
            return asset('storage/profile_pictures/' . $user->user->profile_picture);
        }
        return null;
    }

    /**
     * Get today's student attendance records
     */
    private function getTodayStudentAttendance($startOfDay, $endOfDay)
    {
        return Attendance::with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college', 'email', 'gender')->with('user');
            }])
            ->where('user_type', 'student')
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();
    }

    /**
     * Get today's teacher attendance records
     */
    private function getTodayTeacherAttendance($startOfDay, $endOfDay)
    {
        return Attendance::with(['teacherVisitor.user'])
            ->where('user_type', 'teacher')
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();
    }

    /**
     * Process student attendance data
     */
    private function processStudentAttendanceData($attendance, $borrowRequests)
    {
        $stats = $this->calculateStudentAttendanceStats($attendance);
        $formattedAttendance = $this->formatStudentAttendanceData($attendance, $borrowRequests);

        return [
            'todayAttendance' => $formattedAttendance,
            'stats' => $stats
        ];
    }

    /**
     * Process teacher attendance data
     */
    private function processTeacherAttendanceData($attendance)
    {
        $stats = $this->calculateTeacherAttendanceStats($attendance);
        $formattedAttendance = $this->formatTeacherAttendanceData($attendance);

        return [
            'todayAttendance' => $formattedAttendance,
            'stats' => $stats
        ];
    }

    /**
     * Calculate student attendance statistics
     */
    private function calculateStudentAttendanceStats($attendance)
    {
        return [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'logged_out' => $attendance->whereNotNull('logout')->count(),
        ];
    }

    /**
     * Calculate teacher attendance statistics
     */
    private function calculateTeacherAttendanceStats($attendance)
    {
        return [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'logged_out' => $attendance->whereNotNull('logout')->count(),
        ];
    }

    /**
     * Format student attendance data for display
     */
    private function formatStudentAttendanceData($attendance, $borrowRequests)
    {
        return $attendance->map(function ($attendance) use ($borrowRequests) {
            $studentId = $attendance->student_id;
            $activity = $this->getActivityWithBorrowStatus($attendance, $borrowRequests, $studentId);

            // Determine status
            $status = $attendance->logout ? 'out' : 'in';

            // Build name from available data
            $student = $attendance->student;
            $fname = $student ? ($student->fname ?? null) : null;
            $lname = $student ? ($student->lname ?? null) : null;
            $name = ($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $studentId);

            $profilePicture = $student && $student->user && $student->user->profile_picture
                ? asset('storage/profile_pictures/' . $student->user->profile_picture)
                : null;

            if (!$profilePicture) {
                $profilePicture = AvatarService::getPlaceholderAvatar($name, 100);
            }

            return [
                'id' => $attendance->id,
                'identifier' => $student ? ($student->student_id ?? $studentId) : $studentId,
                'name' => $name,
                'profile_picture' => $profilePicture,
                'college_or_dept' => $student ? ($student->college ?? 'N/A') : 'N/A',
                'gender' => $student ? ($student->gender ?? 'N/A') : 'N/A',
                'activity' => $activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '',
                'status' => $status
            ];
        });
    }

    /**
     * Format teacher attendance data for display
     */
    private function formatTeacherAttendanceData($attendance)
    {
        return $attendance->map(function ($attendance) {
            // Determine status
            $status = $attendance->logout ? 'out' : 'in';

            // Build name from available data
            $teacher = $attendance->teacherVisitor;
            $fname = $teacher ? ($teacher->fname ?? null) : null;
            $lname = $teacher ? ($teacher->lname ?? null) : null;
            $teacherId = $attendance->teacher_visitor_id;
            $name = ($fname && $lname) ? $lname . ', ' . $fname : ($fname ?: $lname ?: $teacherId);

            $profilePicture = $teacher && $teacher->user && $teacher->user->profile_picture
                ? asset('storage/profile_pictures/' . $teacher->user->profile_picture)
                : AvatarService::getPlaceholderAvatar($name, 100);

            return [
                'id' => $attendance->id,
                'identifier' => $attendance->teacher_visitor_id ?? $teacherId,
                'name' => $name,
                'profile_picture' => $profilePicture,
                'college_or_dept' => $teacher ? ($teacher->department ?? 'N/A') : 'N/A',
                'role' => $teacher ? ($teacher->role ?? 'N/A') : 'N/A',
                'gender' => $teacher ? ($teacher->gender ?? 'N/A') : 'N/A',
                'activity' => $attendance->activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '',
                'status' => $status
            ];
        });
    }

    /**
     * Initiate logout process by generating and emailing verification code
     */
    public function initiateLogout(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            // Find student by QR code
            $student = Student::where('qr_code_path', $request->qr_code)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code. Student not found.'
                ], 404);
            }

            // Check if student has an active session today
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            $activeAttendance = Attendance::where('student_id', $student->student_id)
                ->where('user_type', 'student')
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->whereNull('logout')
                ->first();

            if (!$activeAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active attendance session found for today.'
                ], 400);
            }

            // Check if logout code already exists and is not expired
            $cacheKey = 'logout_code_' . $student->id;
            $existingData = Cache::get($cacheKey);

            if ($existingData && ($existingData['attempts'] ?? 0) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'A verification code has already been sent. Please check your email.'
                ], 400);
            }

            // Generate 6-digit code
            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store code in cache with attempts counter (expires in 2 minutes)
            Cache::put($cacheKey, [
                'code' => $code,
                'attempts' => 0,
                'attendance_id' => $activeAttendance->id,
                'student_id' => $student->id
            ], now()->addMinutes(2));

            // Send email using the working pattern similar to Student QR emails
            try {
                if (!$student->email || !filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student does not have a valid email address.'
                    ], 400);
                }

                Mail::to($student->email)->send(new \App\Mail\LogoutVerificationCode($student, $code));

                Log::info('Logout code email sent (AttendanceController)', [
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                ]);
            } catch (\Throwable $mailException) {
                Log::error('Failed to send logout code email (AttendanceController)', [
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'error' => $mailException->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again or contact library staff.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification code has been sent to your email. The code will expire in 2 minutes.',
                'student_name' => $student->fname . ' ' . $student->lname,
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
}
