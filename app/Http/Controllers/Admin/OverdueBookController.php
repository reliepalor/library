<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
use App\Models\OverdueReminderLog;
use App\Mail\OverdueBookReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OverdueBookController extends Controller
{
    public function getOverdueBooks()
    {
        // Get students who have received overdue reminders
        $reminders = OverdueReminderLog::with('student')
            ->latest('reminder_sent_at')
            ->get()
            ->map(function ($reminder) {
                $student = $reminder->student;
                // Fetch book details with images for each book in the reminder
                $booksWithImages = collect($reminder->books)->map(function ($bookData) {
                    $book = \App\Models\Books::where('book_code', $bookData['book_id'])->first();
                    return [
                        'name' => $bookData['name'],
                        'book_id' => $bookData['book_id'],
                        'image1' => $book ? $book->image1 : null,
                        'author' => $book ? $book->author : null,
                        'description' => $book ? $book->description : null,
                        'section' => $book ? $book->section : null,
                    ];
                });

                return [
                    'student' => $student,
                    'books' => $booksWithImages,
                    'total_books' => count($reminder->books),
                    'email_sent' => true,
                    'reminder_sent_at' => $reminder->reminder_sent_at,
                ];
            });

        return response()->json($reminders);
    }

    public function sendReminders(Request $request)
    {
        // Define overdue condition: books borrowed today, not returned, and not already emailed (for 5-6 PM reminders)
        $overdueBooks = BorrowedBook::with(['student', 'book'])
            ->whereDate('borrowed_at', Carbon::today())
            ->whereNull('returned_at')
            ->whereNull('email_sent_at') // Not already sent
            ->get();

        if ($overdueBooks->isEmpty()) {
            return response()->json(['message' => 'No overdue books to send reminders for.'], 200);
        }

        $sentEmails = [];
        $groupedByStudent = $overdueBooks->groupBy('student_id');

        foreach ($groupedByStudent as $studentId => $books) {
            $student = $books->first()->student;
            if (!$student || !$student->email) continue;

            $booksData = $books->map(function ($borrow) {
                return [
                    'name' => $borrow->book->name,
                    'book_id' => $borrow->book->book_id,
                    'image1' => $borrow->book->image1,
                ];
            });

            // Calculate days overdue (approximate)
            $borrowedAt = $books->first()->borrowed_at;
            $dueDate = $borrowedAt->copy()->addDay()->setTime(18, 0, 0);
            $daysOverdue = Carbon::now()->diffInDays($dueDate);

            // Send email
            try {
                Mail::to($student->email)->send(new OverdueBookReminder($books->first(), $daysOverdue));
                $sentEmails[] = [
                    'name' => $student->fname . ' ' . $student->lname,
                    'student_id' => $student->student_id,
                    'college' => $student->college,
                    'book' => $booksData->pluck('name')->join(', '),
                ];

                // Log the reminder
                OverdueReminderLog::create([
                    'student_id' => $student->student_id,
                    'student_name' => $student->fname . ' ' . $student->lname,
                    'student_email' => $student->email,
                    'college' => $student->college,
                    'books' => $booksData,
                    'reminder_sent_at' => now(),
                ]);

                // Update email_sent_at for each book
                $books->each(function ($borrow) {
                    $borrow->update(['email_sent_at' => now()]);
                });
            } catch (\Exception $e) {
                // Log error if needed
                continue;
            }
        }

        return response()->json([
            'message' => 'Reminders sent successfully to ' . count($sentEmails) . ' students.',
            'sent_emails' => $sentEmails,
        ]);
    }
}
