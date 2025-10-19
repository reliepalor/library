<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
use App\Models\OverdueReminderLog;
use App\Mail\OverdueBookReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OverdueBookController extends Controller
{
    public function sendReminders(Request $request)
    {
        Log::info('Starting to send overdue book reminders');

        // Get current time in Asia/Manila timezone
        $now = Carbon::now('Asia/Manila');
        $fivePM = $now->copy()->setTime(17, 0, 0); // 5:00 PM reference

        // Allow sending reminders at any time, but filter overdue books based on 5 PM logic
        $isAfterFivePM = $now->gte($fivePM);

        // Candidates: all approved, not yet returned
        $candidates = BorrowedBook::whereNull('returned_at')
            ->with(['student', 'book'])
            ->get();

        // Determine overdue: only send reminders after 5 PM for books that haven't been returned/logged out
        $overdueBooks = $candidates->filter(function ($borrow) use ($now, $isAfterFivePM) {
            // Only send reminders if it's 5 PM or later
            if (!$isAfterFivePM) {
                return false;
            }

            $borrowedAt = $borrow->borrowed_at ?? $borrow->created_at;
            $borrowedAt = $borrowedAt ? Carbon::parse($borrowedAt, 'Asia/Manila') : Carbon::now('Asia/Manila');

            // Books are due by 5 PM the next day
            $dueAt = $borrowedAt->copy()->addDay()->setTime(17, 0, 0); // 5 PM next day
            return $now->greaterThanOrEqualTo($dueAt);
        });

        // Group by student
        $studentsWithOverdueBooks = $overdueBooks->groupBy('student_id')->filter(function ($books) {
            $student = $books->first()->student;
            // Check if student already received reminder today (by email or student_code)
            if (!$student) return false;

            $query = OverdueReminderLog::query();
            $query->where('student_email', $student->email);
            if (Schema::hasColumn('overdue_reminder_logs', 'student_code')) {
                $query->orWhere('student_code', $student->student_id);
            }
            $query->whereDate('reminder_sent_at', Carbon::today('Asia/Manila'));
            return !$query->exists();
        });

        Log::info('Found ' . $studentsWithOverdueBooks->count() . ' students with overdue books to remind');

        $sentCount = 0;
        $sentEmails = [];

        foreach ($studentsWithOverdueBooks as $studentId => $books) {
            $student = $books->first()->student;

            if ($student && $student->email) {
                try {
                    Log::info("Attempting to send overdue reminder email to student {$student->email} for " . $books->count() . " books");

                    // Send email with all overdue books
                    Mail::to($student->email)
                        ->send(new OverdueBookReminder($student, $books));

                    // Log the reminder
                    $logData = [
                        // Keep numeric placeholder due to column type; preserve true code separately when available
                        'student_id' => 0,
                        'student_name' => ($student->fname ?? '') . ' ' . ($student->lname ?? ''),
                        'student_email' => $student->email,
                        'college' => $student->college,
                        'books' => $books->map(function ($borrow) use ($now) {
                            $borrowedAt = $borrow->borrowed_at ?? $borrow->created_at;
                            $borrowedAt = $borrowedAt ? Carbon::parse($borrowedAt, 'Asia/Manila') : Carbon::now('Asia/Manila');
                            $dueAt = $borrowedAt->copy()->addDay()->setTime(17, 0, 0);
                            return [
                                'book_id' => $borrow->book_id,
                                'name' => optional($borrow->book)->name,
                                'borrowed_at' => $borrowedAt->toDateTimeString(),
                                'due_at' => $dueAt->toDateTimeString(),
                                'days_overdue' => max(0, $dueAt->diffInDays($now)),
                            ];
                        })->values()->toArray(),
                        'reminder_sent_at' => $now,
                    ];
                    if (Schema::hasColumn('overdue_reminder_logs', 'student_code')) {
                        $logData['student_code'] = $student->student_id;
                    }
                    OverdueReminderLog::create($logData);

                    $sentCount++;

                    // Track which students received emails
                    $sentEmails[] = [
                        'student_id' => $student->student_id,
                        'name' => trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')),
                        'email' => $student->email,
                        'book' => $books->map(fn($b) => optional($b->book)->name)->filter()->implode(', '),
                        'college' => $student->college
                    ];

                    Log::info("Successfully sent overdue reminder email to student {$student->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send overdue reminder email to student {$student->email}: " . $e->getMessage());
                }
            } else {
                Log::warning("Student {$studentId} has no email or does not exist.");
            }
        }

        $message = "Sent {$sentCount} overdue book reminders.";
        Log::info($message);

        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
                'sent_count' => $sentCount,
                'sent_emails' => $sentEmails
            ]);
        }

        return back()->with('success', $message);
    }

    public function getOverdueBooks()
    {
        try {
            Log::info('Fetching overdue reminder logs');

            $reminderLogs = OverdueReminderLog::with(['student', 'student.user'])
                ->orderBy('reminder_sent_at', 'desc')
                ->get();

            Log::info('Found ' . $reminderLogs->count() . ' reminder logs');

            $formattedLogs = $reminderLogs->map(function ($log) {
                $student = $log->student;
                $fname = $student->fname ?? ($log->student_name ? explode(' ', $log->student_name)[0] : '');
                $lname = $student->lname ?? ($log->student_name ? explode(' ', $log->student_name)[1] ?? '' : '');
                // Determine avatar URL from linked user profile or fallback
                $profilePicture = $student && $student->user ? $student->user->profile_picture : null;
                $avatarUrl = $profilePicture
                    ? (str_starts_with($profilePicture, 'http') || str_starts_with($profilePicture, '/')
                        ? $profilePicture
                        : asset('storage/' . ltrim($profilePicture, '/')))
                    : asset('images/default-profile.png');

                return [
                    'student' => [
                        'fname' => $fname,
                        'lname' => $lname,
                        'student_id' => $student->student_id ?? $log->student_code ?? $log->student_id,
                        'email' => $student->email ?? $log->student_email,
                        'college' => $student->college ?? $log->college,
                        'avatar_url' => $avatarUrl,
                    ],
                    'books' => $log->books,
                    'total_books' => is_array($log->books) ? count($log->books) : 0,
                    'email_sent' => true,
                    'reminder_sent_at' => $log->reminder_sent_at,
                ];
            });

            Log::info('Successfully formatted ' . $formattedLogs->count() . ' reminder logs');

            return response()->json($formattedLogs);
        } catch (\Exception $e) {
            Log::error('Error in getOverdueBooks: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch overdue books'], 500);
        }
    }

    public function checkOverdueEmails()
    {
        $overdueBooks = BorrowedBook::whereNull('returned_at')
            ->with('student')
            ->get()
            ->map(function ($book) {
                return [
                    'student_id' => $book->student_id,
                    'email' => optional($book->student)->email ?? 'NO EMAIL'
                ];
            });

        return response()->json($overdueBooks);
    }
} 