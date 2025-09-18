<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachersVisitorsAttendance;
use App\Models\TeacherVisitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeachersVisitorsAttendanceController extends Controller
{
    public function index()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $todayAttendance = $this->getTodayAttendance($startOfDay, $endOfDay);

        $processedData = $this->processAttendanceData($todayAttendance);

        return view('admin.TeachersVisitorsAttendance.index', $processedData);
    }

    private function getTodayAttendance($startOfDay, $endOfDay)
    {
        return TeachersVisitorsAttendance::with(['teacherVisitor' => function($query) {
                $query->select('id', 'lname', 'fname', 'department', 'role', 'email');
            }])
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->orderBy('login', 'desc')
            ->get();
    }

    private function processAttendanceData($todayAttendance)
    {
        $stats = $this->calculateAttendanceStats($todayAttendance);

        $formattedAttendance = $this->formatAttendanceData($todayAttendance);

        return [
            'todayAttendance' => $formattedAttendance,
            'stats' => $stats,
        ];
    }

    private function calculateAttendanceStats($attendance)
    {
        return [
            'total' => $attendance->count(),
            'present' => $attendance->whereNull('logout')->count(),
            'absent' => $attendance->whereNotNull('logout')->count(),
        ];
    }

    private function formatAttendanceData($attendance)
    {
        return $attendance->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'teacher_visitor_id' => $attendance->teacher_visitor_id,
                'name' => ($attendance->teacherVisitor->lname ?? 'N/A') . ', ' . ($attendance->teacherVisitor->fname ?? 'N/A'),
                'profile_picture' => null, // Add if profile picture exists
                'department' => $attendance->teacherVisitor->department ?? 'N/A',
                'role' => $attendance->teacherVisitor->role ?? 'N/A',
                'activity' => $attendance->activity,
                'time_in' => $attendance->login ? Carbon::parse($attendance->login)->format('h:i A') : 'N/A',
                'time_out' => $attendance->logout ? Carbon::parse($attendance->logout)->format('h:i A') : 'N/A'
            ];
        });
    }

    public function log(Request $request)
    {
        try {
            $request->validate([
                'teacher_visitor_id' => 'required|exists:teachers_visitors,id',
                'activity' => 'required|string',
            ]);

            $teacherVisitorId = $request->teacher_visitor_id;
            $activity = $request->activity;
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();
            $now = now();

            $attendance = TeachersVisitorsAttendance::where('teacher_visitor_id', $teacherVisitorId)
                ->whereBetween('login', [$startOfDay, $endOfDay])
                ->whereNull('logout')
                ->first();

            if ($attendance) {
                DB::transaction(function () use ($attendance, $now) {
                    $attendance->logout = $now;
                    $attendance->save();
                });

                return response()->json([
                    'message' => 'Logout time recorded successfully.',
                    'type' => 'logout',
                    'teacher_visitor_id' => $teacherVisitorId
                ]);
            } else {
                $attendance = TeachersVisitorsAttendance::create([
                    'teacher_visitor_id' => $teacherVisitorId,
                    'activity' => $activity,
                    'login' => $now,
                ]);

                return response()->json([
                    'message' => 'Login time recorded successfully.',
                    'type' => 'login',
                    'teacher_visitor_id' => $teacherVisitorId
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('TeachersVisitorsAttendance logging error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function check(Request $request)
    {
        $teacherVisitorId = $request->query('teacher_visitor_id');

        if (!$teacherVisitorId) {
            return response()->json(['error' => 'Teacher/Visitor ID is required'], 400);
        }

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();
        $attendance = TeachersVisitorsAttendance::where('teacher_visitor_id', $teacherVisitorId)
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout')
            ->first();

        return response()->json([
            'hasActiveSession' => (bool) $attendance,
            'teacher_visitor_id' => $teacherVisitorId,
            'activity' => $attendance ? $attendance->activity : null
        ]);
    }

    public function scan(Request $request)
    {
        $teacherVisitorId = $request->query('teacher_visitor_id');

        Log::info('TeachersVisitorsAttendance scan called', [
            'teacher_visitor_id' => $teacherVisitorId,
            'user_id' => auth()->id(),
            'is_admin' => auth()->user() ? auth()->user()->usertype === 'admin' : false
        ]);

        if (!$teacherVisitorId) {
            Log::warning('TeachersVisitorsAttendance scan: Teacher/Visitor ID is required');
            return response()->json(['error' => 'Teacher/Visitor ID is required'], 400);
        }

        $teacherVisitor = TeacherVisitor::find($teacherVisitorId);

        if (!$teacherVisitor) {
            Log::warning('TeachersVisitorsAttendance scan: Teacher/Visitor not found', [
                'teacher_visitor_id' => $teacherVisitorId
            ]);
            return response()->json(['error' => 'Teacher/Visitor not found'], 404);
        }

        $name = $teacherVisitor->fname . ' ' . $teacherVisitor->lname;

        Log::info('TeachersVisitorsAttendance scan: Success', [
            'teacher_visitor_id' => $teacherVisitorId,
            'name' => $name
        ]);

        return response()->json([
            'teacherVisitor' => $teacherVisitor,
            'name' => $name
        ]);
    }

    public function getRealtimeAttendance(Request $request)
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $todayAttendance = $this->getTodayAttendance($startOfDay, $endOfDay);
        $processedData = $this->processAttendanceData($todayAttendance);

        return response()->json([
            'success' => true,
            'data' => [
                'todayAttendance' => $processedData['todayAttendance'],
                'last_updated' => now()->toISOString(),
            ]
        ]);
    }
}
