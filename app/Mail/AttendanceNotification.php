<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class AttendanceNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $type;
    public $time;
    public $activity;
    public $duration;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, string $type, Carbon $time, string $activity = null, string $duration = null)
    {
        $this->student = $student;
        $this->type = $type;
        $this->time = $time;
        $this->activity = $activity;
        $this->duration = $duration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->type === 'login' ? 'Welcome to CSU Library' : 'Thank You for Using CSU Library';
        $viewName = $this->type === 'login' ? 'emails.attendance_login' : 'emails.attendance_logout';

        return $this->subject($subject)
                    ->markdown($viewName)
                    ->with([
                        'student' => $this->student,
                        'loginTime' => $this->type === 'login' ? $this->time->setTimezone('Asia/Manila')->format('F j, Y g:i A') : null,
                        'logoutTime' => $this->type === 'logout' ? $this->time->setTimezone('Asia/Manila')->format('F j, Y g:i A') : null,
                        'activity' => $this->activity,
                        'duration' => $this->duration,
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 