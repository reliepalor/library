<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_visitor_email',
        'user_type',
        'book_id',
        'status',
        'reserved_at',
        'expires_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function teacherVisitor()
    {
        return $this->belongsTo(TeacherVisitor::class, 'teacher_visitor_email', 'email');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function isExpired()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
    }
}
