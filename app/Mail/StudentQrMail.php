<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentQrMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $student;
    public $qrCodeBase64;
    public function __construct($student, $qrCodeBase64)
    {
        $this->student = $student;
        $this->qrCodeBase64 = $qrCodeBase64;
    }
        public function build()
    {
        return $this->subject('Your Student QR Code')
                    ->view('emails.student_qr')
                    ->with([
                        'studentName' => $this->student->lname . ', ' . $this->student->fname,
                        'studentId'   => $this->student->student_id,
                    ])
                    ->attachData(base64_decode($this->qrCodeBase64), 'student_qr.png', [
                        'mime' => 'image/png',
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Qr Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.student_qr',
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
