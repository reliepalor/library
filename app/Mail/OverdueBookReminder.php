<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BorrowedBook;

class OverdueBookReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $borrowedBook;
    public $daysOverdue;

    public function __construct(BorrowedBook $borrowedBook, $daysOverdue)
    {
        $this->borrowedBook = $borrowedBook;
        $this->daysOverdue = $daysOverdue;
    }

    public function build()
    {
        return $this->subject('Overdue Book Reminder')
                    ->markdown('emails.overdue-book-reminder');
    }
} 