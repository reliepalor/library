<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowedBook extends Model
{
    protected $fillable = [
        'student_id',
        'user_type',
        'book_id',
        'status',
        'rejection_reason',
        'returned_at',
        'attendance_id',
        'email_sent_at',
        'original_activity',
    ];

    protected $casts = [
        'user_type' => 'string',
    ];

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'book_code');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function teacherVisitor()
    {
        return $this->belongsTo(TeacherVisitor::class, 'student_id', 'email');
    }
}
