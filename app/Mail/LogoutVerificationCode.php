<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogoutVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $code;

    public function __construct($student, $code)
    {
        $this->student = $student;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your Logout Verification Code - ' . config('app.name'))
                   ->view('emails.logout-verification-code')
                   ->with([
                       'student' => $this->student,
                       'code' => $this->code
                   ]);
    }
}
