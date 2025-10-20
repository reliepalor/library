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
    public $userType;

    /**
     * Create a new message instance.
     *
     * @param BorrowedBook $borrowedBook
     * @param string $rejectionReason
     * @param string $userType
     */
    public function __construct(BorrowedBook $borrowedBook, string $rejectionReason, string $userType = 'student')
    {
        $this->borrowedBook = $borrowedBook;
        $this->rejectionReason = $rejectionReason;
        $this->userType = $userType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Your Borrow Request Has Been Rejected';
        $viewData = [
            'borrowedBook' => $this->borrowedBook,
            'rejectionReason' => $this->rejectionReason,
        ];

        // Add user-specific data based on user type
        if ($this->userType === 'teacher') {
            $teacherVisitor = \App\Models\TeacherVisitor::find($this->borrowedBook->student_id);
            $viewData['userName'] = $teacherVisitor ? $teacherVisitor->full_name : 'Teacher/Visitor';
            $viewData['userType'] = 'teacher';
        } else {
            $viewData['userName'] = $this->borrowedBook->student ? $this->borrowedBook->student->fname . ' ' . $this->borrowedBook->student->lname : 'Student';
            $viewData['userType'] = 'student';
        }

        return $this->subject($subject)
                    ->markdown('emails.borrow-request-rejection')
                    ->with($viewData);
    }
}
