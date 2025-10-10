<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class OverdueReminderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_code',
        'student_name',
        'student_email',
        'college',
        'books',
        'reminder_sent_at',
    ];

    protected $casts = [
        'books' => 'array',
        'reminder_sent_at' => 'datetime',
    ];

    public function student()
    {
        // Link logs to Student via student_code (string) -> students.student_id
        return $this->belongsTo(Student::class, 'student_code', 'student_id');
    }
}
