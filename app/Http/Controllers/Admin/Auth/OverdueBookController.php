<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
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
        
        // Get all approved books that haven't been returned
        $overdueBooks = BorrowedBook::where('status', 'approved')
            ->whereNull('returned_at')
            ->with(['student', 'book'])
            ->get();

        Log::info('Found ' . $overdueBooks->count() . ' books to check');

        $sentCount = 0;
        $sentEmails = [];

        foreach ($overdueBooks as $book) {
            $student = \App\Models\Student::where('student_id', $book->student_id)->first();
            
            if ($student && $student->email) {
                try {
                    Log::info("Attempting to send overdue reminder email to student {$student->email} for book {$book->book_id}");
                    Mail::to($student->email)
                        ->send(new OverdueBookReminder($book, 1));
                    
                    $book->email_sent_at = now();
                    $book->save();
                    $sentCount++;
                    
                    // Track which students received emails
                    $sentEmails[] = [
                        'student_id' => $student->student_id,
                        'name' => $student->fname . ' ' . $student->lname,
                        'email' => $student->email,
                        'book' => $book->book_id,
                        'college' => $student->college
                    ];
                    
                    Log::info("Successfully sent overdue reminder email to student {$student->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send overdue reminder email to student {$student->email}: " . $e->getMessage());
                }
            } else {
                Log::warning("Student {$book->student_id} has no email or does not exist.");
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
            Log::info('Fetching overdue books');
            
            $overdueBooks = BorrowedBook::where('status', 'approved')
                ->whereNull('returned_at')
                ->where('created_at', '<', Carbon::now()->subDay())
                ->with(['student', 'book'])
                ->get();

            Log::info('Found ' . $overdueBooks->count() . ' overdue books');

            $formattedBooks = $overdueBooks->map(function ($book) {
                try {
                    $daysOverdue = Carbon::parse($book->created_at)->diffInDays(Carbon::now());
                    $emailSent = !is_null($book->email_sent_at);

                    return [
                        'id' => $book->id,
                        'student' => [
                            'fname' => $book->student->fname,
                            'lname' => $book->student->lname,
                            'student_id' => $book->student->student_id,
                            'email' => $book->student->email,
                            'college' => $book->student->college
                        ],
                        'book' => [
                            'name' => $book->book->name,
                            'book_id' => $book->book->book_id
                        ],
                        'created_at' => $book->created_at,
                        'days_overdue' => $daysOverdue,
                        'email_sent' => $emailSent
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting book data: ' . $e->getMessage(), [
                        'book_id' => $book->id,
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
            })->filter(); // Remove any null entries from failed formatting

            Log::info('Successfully formatted ' . $formattedBooks->count() . ' books');
            
            return response()->json($formattedBooks);
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