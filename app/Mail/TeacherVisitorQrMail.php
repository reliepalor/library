<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherVisitorQrMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $teacherVisitor;
    public $qrCodeBase64;
    public function __construct($teacherVisitor, $qrCodeBase64)
    {
        $this->teacherVisitor = $teacherVisitor;
        $this->qrCodeBase64 = $qrCodeBase64;
    }
        public function build()
    {
        return $this->subject('Your Teacher/Visitor QR Code')
                    ->view('emails.teacher_visitor_qr')
                    ->with([
                        'name' => $this->teacherVisitor->lname . ', ' . $this->teacherVisitor->fname,
                        'role' => $this->teacherVisitor->role,
                        'department' => $this->teacherVisitor->department,
                    ])
                    ->attachData(base64_decode($this->qrCodeBase64), 'teacher_visitor_qr.png', [
                        'mime' => 'image/png',
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Teacher Visitor Qr Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.teacher_visitor_qr',
        );
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