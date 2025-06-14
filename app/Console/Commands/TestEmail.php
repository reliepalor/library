<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceNotification;
use App\Models\Student;

class TestEmail extends Command
{
    protected $signature = 'email:test';
    protected $description = 'Test the email configuration';

    public function handle()
    {
        $this->info('Testing email configuration...');

        try {
            // Get a test student
            $student = Student::first();
            
            if (!$student) {
                $this->error('No students found in the database. Please add a student first.');
                return;
            }

            // Send a test email
            Mail::to($student->email)->send(new AttendanceNotification(
                $student,
                'login',
                now(),
                'Test Activity'
            ));

            $this->info('Test email sent successfully to: ' . $student->email);
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
        }
    }
} 