<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
use App\Models\OverdueReminderLog;
use App\Mail\OverdueBookReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OverdueBookController extends Controller
{
    public function sendReminders(Request $request)
    {
        Log::info('Starting to send overdue book reminders');
        
        // Get current time in Asia/Manila timezone
        $now = Carbon::now('Asia/Manila');
        $startTime = $now->copy()->setTime(17, 0, 0); // 5:00 PM
        $endTime = $now->copy()->setTime(18, 0, 0);   // 6:00 PM

        // Only send reminders if current time is between 5-6 PM
        // Temporarily disabled for testing
        // if ($now->lt($startTime) || $now->gt($endTime)) {
        //     $message = "Reminders can only be sent between 5:00 PM and 6:00 PM.";
        //     Log::info($message);
        //     if ($request->ajax()) {
        //         return response()->json(['message' => $message], 400);
        //     }
        //     return back()->with('error', $message);
        // }

        // Get all approved books that haven't been returned and are overdue (borrowed more than 1 day ago)
        $overdueBooks = BorrowedBook::where('status', 'approved')
            ->whereNull('returned_at')
            ->where('created_at', '<', Carbon::now()->subDays(1))
            ->with(['student', 'book'])
            ->get();

        // Group by student
        $studentsWithOverdueBooks = $overdueBooks->groupBy('student_id')->filter(function ($books) {
            $student = $books->first()->student;
            // Check if student already received reminder today
            return $student && !OverdueReminderLog::where('student_id', $student->student_id)
                ->whereDate('reminder_sent_at', Carbon::today())
                ->exists();
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
                    OverdueReminderLog::create([
                        'student_id' => $student->student_id,
                        'student_name' => $student->fname . ' ' . $student->lname,
                        'student_email' => $student->email,
                        'college' => $student->college,
                        'books' => $books->map(function ($book) {
                            return [
                                'book_id' => $book->book_id,
                                'name' => $book->book->name,
                                'borrowed_at' => $book->created_at,
                                'days_overdue' => Carbon::parse($book->created_at)->diffInDays(Carbon::now())
                            ];
                        })->toArray(),
                        'reminder_sent_at' => $now,
                    ]);

                    $sentCount++;

                    // Track which students received emails
                    $sentEmails[] = [
                        'student_id' => $student->student_id,
                        'name' => $student->student_name,
                        'email' => $student->student_email,
                        'book' => $books->pluck('book.name')->join(', '),
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

            $reminderLogs = OverdueReminderLog::orderBy('reminder_sent_at', 'desc')->get();

            Log::info('Found ' . $reminderLogs->count() . ' reminder logs');

            $formattedLogs = $reminderLogs->map(function ($log) {
                return [
                    'student' => [
                        'fname' => explode(' ', $log->student_name)[0] ?? '',
                        'lname' => explode(' ', $log->student_name)[1] ?? '',
                        'student_id' => $log->student_id,
                        'email' => $log->student_email,
                        'college' => $log->college
                    ],
                    'books' => $log->books,
                    'total_books' => count($log->books),
                    'email_sent' => true,
                    'reminder_sent_at' => $log->reminder_sent_at
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
        $overdueBooks = BorrowedBook::where('status', 'approved')
            ->whereNull('returned_at')
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