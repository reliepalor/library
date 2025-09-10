<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BorrowedBook;

class BorrowRequestRejection extends Mailable
{
    use Queueable, SerializesModels;

    public $borrowedBook;
    public $rejectionReason;

    /**
     * Create a new message instance.
     *
     * @param BorrowedBook $borrowedBook
     * @param string $rejectionReason
     */
    public function __construct(BorrowedBook $borrowedBook, string $rejectionReason)
    {
        $this->borrowedBook = $borrowedBook;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Borrow Request Has Been Rejected')
                    ->markdown('emails.borrow-request-rejection')
                    ->with([
                        'borrowedBook' => $this->borrowedBook,
                        'rejectionReason' => $this->rejectionReason,
                    ]);
    }
}
