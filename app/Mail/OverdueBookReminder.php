<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\Student;

class OverdueBookReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $overdueBooks; // Collection|array of overdue borrowed entries with related book

    public function __construct(Student $student, $overdueBooks)
    {
        $this->student = $student;
        // Ensure it's a collection for easier iteration in the view
        $this->overdueBooks = $overdueBooks instanceof Collection ? $overdueBooks : collect($overdueBooks);
    }

    public function build()
    {
        return $this->subject('Overdue Book Reminder')
                    ->markdown('emails.overdue-book-reminder');
    }
}