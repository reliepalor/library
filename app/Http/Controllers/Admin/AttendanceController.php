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
use App\Mail\AttendanceNotification;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Collection;
use App\Models\Books;

class AttendanceController extends Controller
{
    public function index()
    {
        // Use range to enable index usage on datetime
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();
        
        // Get today's attendance with proper time tracking and student relationship
        $todayAttendance = Attendance::with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college', 'email')->with('user');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('login', 'desc')
            ->get();

        // Calculate accurate statistics
        $stats = [
            'total' => $todayAttendance->count(), // Total students who have logged in/out today
            'present' => $todayAttendance->whereNull('logout')->count(), // Students currently in library
            'absent' => $todayAttendance->whereNotNull('logout')->count(), // Students who have logged out
            'borrowed' => $todayAttendance->where('activity', 'like', '%Borrow%')->count(), // Books borrowed today
        ];

        // Get accurate college-wise statistics
        $collegeStats = [];
        $colleges = ['CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA'];
        
        foreach ($colleges as $college) {
            $collegeAttendance = $todayAttendance->filter(function($attendance) use ($college) {
                return $attendance->student && $attendance->student->college === $college;
            });

            $collegeStats[$college] = [
                'present' => $collegeAttendance->whereNull('logout')->count(),
                'total' => Student::where('college', $college)->count()
            ];
        }

        // Format attendance data for display with proper time formatting
        $formattedAttendance = $todayAttendance->map(function ($attendance) {
            return [
                'student_id' => $attendance->student->student_id ?? 'N/A',
                'student_name' => ($attendance->student->lname ?? 'N/A') . ', ' . ($attendance->student->fname ?? 'N/A'),
                'profile_picture' => $attendance->student && $attendance->student->user && $attendance->student->user->profile_picture
                    ? $attendance->student->user->profile_picture
                    : null,
                'college' => $attendance->student->college ?? 'N/A',
                'activity' => $attendance->activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : 'N/A'
            ];
        });

        return view('admin.attendance.index', [
            'todayAttendance' => $formattedAttendance,
            'stats' => $stats,
            'collegeStats' => $collegeStats
        ]);
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
                LogFacade::error('Error in attendance history: ' . $e->getMessage());
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

            // Initialize all data collections with empty collections
            $dailyTrends = collect();
            $collegeDistribution = collect();
            $activityDistribution = collect();

            // Get daily trends
            $dailyTrends = AttendanceHistory::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('date')
                ->get();

            // Get college-wise distribution
            $collegeDistribution = AttendanceHistory::select(
                'students.college',
                DB::raw('COUNT(*) as count')
            )
                ->join('students', 'attendance_histories.student_id', '=', 'students.id')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('students.college')
                ->get();

            // Get activity distribution
            $activityDistribution = AttendanceHistory::select(
                'activity',
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('activity')
                ->get();

            // Initialize summary with default values
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
            \Log::error('Error in attendance insights: ' . $e->getMessage());
            
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

    public function getChartData(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(7);
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

            // Get dates for the range
            $dates = collect();
            $currentDate = $dateFrom->copy();
            while ($currentDate <= $dateTo) {
                $dates->push($currentDate->format('Y-m-d'));
                $currentDate->addDay();
            }

            // Get attendance counts for each date
            $attendanceCounts = AttendanceHistory::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();

            // Get college distribution
            $collegeData = AttendanceHistory::select(
                'college',
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('college')
                ->get();

            // Get activity distribution
            $activityData = AttendanceHistory::select(
                'activity',
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('activity')
                ->get();

            // Get peak hours data
            $hours = collect(range(8, 20))->map(function($hour) {
                return sprintf('%02d:00', $hour);
            });

            $hourlyCounts = AttendanceHistory::select(
                DB::raw('HOUR(time_in) as hour'),
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();

            // Format data for charts
            $data = [
                'dates' => $dates->toArray(),
                'attendance_counts' => $dates->map(function ($date) use ($attendanceCounts) {
                    return $attendanceCounts[$date] ?? 0;
                })->toArray(),
                'colleges' => $collegeData->pluck('college')->toArray(),
                'college_counts' => $collegeData->pluck('count')->toArray(),
                'activities' => $activityData->pluck('activity')->toArray(),
                'activity_counts' => $activityData->pluck('count')->toArray(),
                'hours' => $hours->toArray(),
                'hourly_counts' => $hours->map(function($hour) use ($hourlyCounts) {
                    $hourNum = (int)substr($hour, 0, 2);
                    return $hourlyCounts[$hourNum] ?? 0;
                })->toArray()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getChartData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch chart data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAndReset()
    {
        // Get today's attendance records
        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::with(['student' => function($query) {
                $query->select('student_id', 'lname', 'fname', 'college');
            }])
            ->whereDate('login', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Create attendance history records
        foreach ($todayAttendance as $attendance) {
            AttendanceHistory::create([
                'student_id' => $attendance->student_id,
                'college' => $attendance->student->college,
                'activity' => $attendance->activity,
                'time_in' => $attendance->login,
                'time_out' => $attendance->logout,
                'date' => $today
            ]);
        }

        // Delete today's attendance records
        $todayAttendance->each->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance records have been saved and reset successfully.');
    }

    public function getHistoryData(Request $request)
    {
        $date = $request->input('date');
        $college = $request->input('college');
        $status = $request->input('status');

        $query = AttendanceHistory::query()
            ->join('students', 'attendance_histories.student_id', '=', 'students.student_id')
            ->select('attendance_histories.*', 'students.fname', 'students.lname');

        if ($date) {
            $query->whereDate('date', $date);
        }

        if ($college) {
            $query->where('attendance_histories.college', $college);
        }

        if ($status) {
            if ($status === 'present') {
                $query->whereNull('time_out');
            } else if ($status === 'logged_out') {
                $query->whereNotNull('time_out');
            }
        }

        // Get history records
        $history = $query->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'student_id' => $record->student_id,
                    'student_name' => $record->lname . ', ' . $record->fname,
                    'college' => $record->college,
                    'activity' => $record->activity,
                    'time_in' => Carbon::parse($record->time_in)->setTimezone('Asia/Manila')->format('h:i A'),
                    'time_out' => $record->time_out ? Carbon::parse($record->time_out)->setTimezone('Asia/Manila')->format('h:i A') : null
                ];
            });

        return response()->json([
            'history' => $history
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
            $request->validate([
                'student_id' => 'required|string|exists:students,student_id',
                'activity' => 'required|string',
            ]);

            $studentId = $request->student_id;
            $activity = $request->activity;
            // Use range to enable index usage
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();
            $now = now();

            $attendance = Attendance::where('student_id', $studentId)
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->whereNull('logout')
                ->first();

            if ($attendance) {
                // Ensure atomicity for logout and any related updates
                DB::transaction(function () use ($attendance, $now) {
                    $attendance->logout = $now;
                    $attendance->save();

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

                $student = Student::where('student_id', $studentId)->first();

                // Send attendance notification email for logout
                try {
                    Mail::to($student->email)->queue(new AttendanceNotification($student, 'logout', $now, $attendance->activity, $duration));
                } catch (\Exception $e) {
                    Log::error('Failed to send logout email: ' . $e->getMessage());
                }

                return response()->json([
                    'message' => 'Logout time recorded successfully.',
                    'type' => 'logout',
                    'student_id' => $studentId
                ]);
            } else {
                $attendance = Attendance::create([
                    'student_id' => $studentId,
                    'activity' => $activity,
                    'login' => $now,
                ]);

                $student = Student::where('student_id', $studentId)->first();

                // Send attendance notification email for login
                try {
                    Mail::to($student->email)->queue(new AttendanceNotification($student, 'login', $now, $activity));
                } catch (\Exception $e) {
                    Log::error('Failed to send login email: ' . $e->getMessage());
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
     * Check if student has an active attendance session today (admin).
     */
    public function check(Request $request)
    {
        $studentId = $request->query('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        // Use range to enable index usage
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();
        $attendance = Attendance::where('student_id', $studentId)
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout')
            ->first();

        return response()->json([
            'hasActiveSession' => (bool) $attendance,
            'student_id' => $studentId,
            'activity' => $attendance ? $attendance->activity : null
        ]);
    }

    /**
     * Show the attendance scan page (admin).
     */
    public function scan(Request $request)
    {
        $studentId = $request->query('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        $student = Student::with('user')->where('student_id', $studentId)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json([
            'students' => $student,
            'profile_picture' => $student->user && $student->user->profile_picture 
                ? $student->user->profile_picture 
                : null
        ]);
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
}