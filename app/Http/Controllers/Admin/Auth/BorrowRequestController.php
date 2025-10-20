<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
use Illuminate\Http\Request;
use App\Mail\BorrowRequestRejection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BorrowRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'book_id' => 'required|string',
        ]);

        // For now, assume student, but store user_type if provided
        $userType = $request->input('user_type', 'student');
        $identifier = $request->student_id;

        // Convert book_id to uppercase for validation
        $bookCode = strtoupper($request->book_id);

        // Check if a pending borrow request for the same user and book already exists
        $existingRequest = \App\Models\BorrowedBook::where('student_id', $identifier)
            ->where('book_id', $bookCode)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'A borrow request for this book is already pending.'], 422);
            }
            return back()->withErrors(['book_id' => 'A borrow request for this book is already pending.']);
        }

        // Validate book code exists
        $book = \App\Models\Books::where('book_code', $bookCode)->first();
        if (!$book) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid book code.'], 422);
            }
            return back()->withErrors(['book_id' => 'Invalid book code.']);
        }

        // Check if book is already borrowed
        $isBorrowed = \App\Models\BorrowedBook::where('book_id', $bookCode)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->exists();

        if ($isBorrowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This book is already borrowed by another user.'], 422);
            }
            return back()->withErrors(['book_id' => 'This book is already borrowed by another user.']);
        }

        // Find or create attendance session for this user today
        $startOfDay = \Carbon\Carbon::today()->startOfDay();
        $endOfDay = \Carbon\Carbon::today()->endOfDay();

        $currentAttendance = \App\Models\Attendance::where(function($q) use ($userType, $identifier) {
            if ($userType === 'student') {
                $q->where('student_id', $identifier);
            } else {
                $q->where('teacher_visitor_id', $identifier);
            }
        })
            ->whereBetween('login', [$startOfDay, $endOfDay])
            ->whereNull('logout')
            ->first();

        // If no active attendance session exists, ALWAYS create a new attendance row for visibility
        if (!$currentAttendance) {
            $attendanceData = [
                'user_type' => $userType,
                'activity' => 'Wait for approval',
                'login' => now()->setTimezone('Asia/Manila'),
            ];
            if ($userType === 'student') {
                $attendanceData['student_id'] = $identifier;
            } else {
                $attendanceData['teacher_visitor_id'] = $identifier;
            }
            $currentAttendance = \App\Models\Attendance::create($attendanceData);
            Log::info("Created new attendance record for {$userType} {$identifier} when creating borrow request (no active session)");
        }

        $borrow = \App\Models\BorrowedBook::create([
            'student_id' => $identifier,
            'user_type' => $userType,
            'book_id' => $bookCode, // Use the uppercase version
            'status' => 'pending',
            'attendance_id' => $currentAttendance->id,
            'original_activity' => $request->input('activity', 'Borrow'), // Store the original activity type
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Borrow request submitted and waiting for admin approval.']);
        }
        return redirect()->route('admin.attendance.index')->with('success', 'Borrow request submitted and waiting for admin approval.');
    }

    public function index()
    {
        $requests = \App\Models\BorrowedBook::where('status', 'pending')->with(['student', 'book'])->latest()->get();
        $borrowedBooks = \App\Models\BorrowedBook::whereIn('status', ['approved', 'rejected'])->with(['student', 'book'])->latest()->get();
        return view('admin.borrow_requests.index', compact('requests', 'borrowedBooks'));
    }

    public function approve(Request $request, $id)
    {
        try {
            $borrowRequest = \App\Models\BorrowedBook::findOrFail($id);
            $attendanceId = $borrowRequest->attendance_id;
            $userType = $borrowRequest->user_type;
            $identifier = $borrowRequest->student_id;
            $bookCode = $borrowRequest->book_id;

            // Update the borrow request status
            $borrowRequest->status = 'approved';
            $borrowRequest->save();

            // Find or create attendance record for this user today
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            $attendance = null;
            if ($attendanceId) {
                // Use existing attendance record
                $attendance = \App\Models\Attendance::find($attendanceId);
            }

            if (!$attendance) {
                // Create a new attendance record to reflect approval state clearly
                // Use the original activity stored in the borrow request
                $originalActivity = $borrowRequest->original_activity ?? 'Borrow';
                $activityPrefix = ($originalActivity === 'Stay&Borrow') ? 'Stay&Borrow:' : 'Borrow:';

                $attendanceData = [
                    'user_type' => $userType,
                    'activity' => $activityPrefix . $bookCode,
                    'login' => now()->setTimezone('Asia/Manila'),
                ];
                if ($userType === 'student') {
                    $attendanceData['student_id'] = $identifier;
                } else {
                    $attendanceData['teacher_visitor_id'] = $identifier;
                }
                $attendance = \App\Models\Attendance::create($attendanceData);
                Log::info("Created new attendance record for {$userType} {$identifier} when approving borrow request (no linked attendance)");
            } else {
                // Update the existing attendance activity without touching logout
                // Use the original activity stored in the borrow request
                $originalActivity = $borrowRequest->original_activity ?? 'Borrow';
                $activityPrefix = ($originalActivity === 'Stay&Borrow') ? 'Stay&Borrow:' : 'Borrow:';
                $attendance->activity = $activityPrefix . $bookCode;
                $attendance->save();
            }

            // Send notification email to user if they have an email
            if ($borrowRequest->student && $borrowRequest->student->email) {
                try {
                    // You can add a BorrowRequestApproval mail class here if needed
                    // For now, we'll just log the approval
                    Log::info("Borrow request approved for {$userType} {$identifier} - Book: {$bookCode}");
                } catch (\Exception $e) {
                    Log::error('Failed to send approval notification: ' . $e->getMessage());
                }
            }

            // Return appropriate response based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Borrow request approved and attendance record updated.'
                ]);
            }

            return redirect()->back()->with('success', 'Borrow request approved and attendance record updated.');
        } catch (\Exception $e) {
            Log::error('Error approving borrow request: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve borrow request: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to approve borrow request.');
        }
    }

    public function reject(Request $request, $id)
    {
        $borrow = \App\Models\BorrowedBook::findOrFail($id);
        $attendanceId = $borrow->attendance_id;
        $userType = $borrow->user_type;
        $identifier = $borrow->student_id;

        // Update the borrow request status
        $borrow->status = 'rejected';
        $borrow->rejection_reason = $request->input('rejection_reason');
        $borrow->save();

        // Find or create attendance record for this user today to ensure proper tracking
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $attendance = null;
        if ($attendanceId) {
            $attendance = \App\Models\Attendance::find($attendanceId);
        }

        if (!$attendance) {
            // Create a new attendance record to reflect rejection state clearly
            $attendanceData = [
                'user_type' => $userType,
                'activity' => 'Borrow book rejected',
                'login' => now()->setTimezone('Asia/Manila'),
            ];
            if ($userType === 'student') {
                $attendanceData['student_id'] = $identifier;
            } else {
                $attendanceData['teacher_visitor_id'] = $identifier;
            }
            $attendance = \App\Models\Attendance::create($attendanceData);
            Log::info("Created new attendance record for {$userType} {$identifier} when rejecting borrow request (no linked attendance)");

            // Link the borrow request to the new attendance record
            $borrow->update(['attendance_id' => $attendance->id]);
        } else {
            $attendance->activity = 'Borrow book rejected';
            $attendance->save();
        }

        // Check if there are any other active (pending or approved) borrow requests for this user today
        $otherActiveRequests = \App\Models\BorrowedBook::where('student_id', $identifier)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'approved'])
            ->where('id', '!=', $id)
            ->exists();

        if (!$otherActiveRequests) {
            // If no other active requests, we still keep the attendance record but update activity
            Log::info("Borrow request rejected for {$userType} {$identifier} - attendance record updated for visibility");
        }

        // Send email to user if they have an email
        // Determine user type dynamically based on the identifier
        $student = \App\Models\Student::where('student_id', $borrow->student_id)->first();
        $teacherVisitor = \App\Models\TeacherVisitor::find($borrow->student_id);

        if ($student && $student->email) {
            // Send rejection email to student
            try {
                Mail::to($student->email)->send(new BorrowRequestRejection(
                    $borrow,
                    $request->input('rejection_reason'),
                    'student'
                ));
            } catch (\Exception $e) {
                // Log the error but don't fail the rejection
                Log::error('Failed to send rejection email to student: ' . $e->getMessage());
            }
        } elseif ($teacherVisitor && $teacherVisitor->email) {
            // Send rejection email to teacher/visitor
            try {
                Mail::to($teacherVisitor->email)->send(new BorrowRequestRejection(
                    $borrow,
                    $request->input('rejection_reason'),
                    'teacher'
                ));
            } catch (\Exception $e) {
                // Log the error but don't fail the rejection
                Log::error('Failed to send rejection email to teacher/visitor: ' . $e->getMessage());
            }
        } else {
            // Log that no email was found for the user
            Log::warning('No email found for borrow request rejection', [
                'borrow_id' => $borrow->id,
                'student_id' => $borrow->student_id,
                'user_type' => $borrow->user_type
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Borrow request rejected. The attendance record is kept for visibility.'
            ]);
        }

        return redirect()->back()->with('success', 'Borrow request rejected. The attendance record is kept for visibility.');
    }

    public function updateStatus(Request $request, BorrowedBook $borrowRequest)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,returned'
            ]);

            $borrowRequest->update([
                'status' => $validated['status'],
                'rejection_reason' => $validated['status'] === 'rejected' ? 'Rejected by admin' : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark a borrowed book as returned
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReturned($id)
    {
        try {
            $borrowRequest = BorrowedBook::findOrFail($id);
            $attendanceId = $borrowRequest->attendance_id;
            $studentId = $borrowRequest->student_id;

            $borrowRequest->update([
                'status' => 'returned',
                'returned_at' => now()
            ]);

            // Update attendance activity if there's an associated attendance record
            if ($attendanceId) {
                $attendance = \App\Models\Attendance::find($attendanceId);
                if ($attendance) {
                    // Check if there are other approved borrow requests for this attendance
                    $otherApprovedRequests = \App\Models\BorrowedBook::where('student_id', $studentId)
                        ->where('attendance_id', $attendanceId)
                        ->where('status', 'approved')
                        ->where('id', '!=', $id)
                        ->exists();

                    if (!$otherApprovedRequests) {
                        // If no other approved requests, update activity to show book was returned
                        $attendance->activity = 'Book returned';
                        $attendance->save(); // Use save() to avoid mass assignment issues
                        Log::info("Updated attendance activity to 'Book returned' for student {$studentId}");
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Book marked as returned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark book as returned: ' . $e->getMessage()
            ], 500);
        }
    }
}
