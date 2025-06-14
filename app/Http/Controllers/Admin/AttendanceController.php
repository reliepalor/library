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
use Illuminate\Support\Collection;

class AttendanceController extends Controller
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
        try {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(7);
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

            // Get analytics data
            $analytics = [
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d'),
                'data' => $this->getChartData($request)->getData()
            ];

            return view('admin.attendance.analytics', compact('analytics'));
        } catch (\Exception $e) {
            Log::error('Error in attendance analytics: ' . $e->getMessage());
            return view('admin.attendance.analytics', [
                'analytics' => [
                    'dateFrom' => Carbon::now()->subDays(7)->format('Y-m-d'),
                    'dateTo' => Carbon::now()->format('Y-m-d'),
                    'data' => []
                ]
            ]);
        }
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
} 