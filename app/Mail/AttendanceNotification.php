<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use App\Models\TeacherVisitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class AttendanceNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $teacherVisitor;
    public $userType;
    public $type;
    public $time;
    public $activity;
    public $duration;

    /**
     * Create a new message instance.
     */
    public function __construct($user, string $userType, string $type, Carbon $time, string $activity = null, string $duration = null)
    {
        if ($userType === 'student') {
            $this->student = $user;
            $this->teacherVisitor = null;
        } else {
            $this->teacherVisitor = $user;
            $this->student = null;
        }

        $this->userType = $userType;
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
                        'teacherVisitor' => $this->teacherVisitor,
                        'userType' => $this->userType,
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