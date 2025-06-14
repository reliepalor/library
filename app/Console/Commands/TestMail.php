<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceNotification;
use App\Models\Student;
use Carbon\Carbon;

class TestMail extends Command
{
    protected $signature = 'test:mail {email}';
    protected $description = 'Send a test email to verify mail configuration';

    public function handle()
    {
        $email = $this->argument('email');

        // Create a dummy student object for testing
        $student = new Student();
        $student->fname = 'Test';
        $student->lname = 'User';
        $student->student_id = 'TEST123';
        $student->email = $email;
        $student->college = 'Test College';
        $student->year = '1';

        $time = Carbon::now();

        try {
            Mail::to($email)->send(new AttendanceNotification($student, 'login', $time, 'Test Activity'));
            $this->info("Test email sent successfully to {$email}");
        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
        }

        return 0;
    }
}
