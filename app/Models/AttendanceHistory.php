<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'student_id',
        'teacher_visitor_id',
        'college',
        'department',
        'role',
        'activity',
        'time_in',
        'time_out',
        'date'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'date' => 'date'
    ];

    /**
     * Relationship to Student model
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship to TeacherVisitor model
     */
    public function teacherVisitor(): BelongsTo
    {
        return $this->belongsTo(TeacherVisitor::class, 'teacher_visitor_id', 'id');
    }

    /**
     * Get the attendee (student or teacher) dynamically
     */
    public function attendee()
    {
        if ($this->user_type === 'student') {
            return $this->student;
        }
        return $this->teacherVisitor;
    }

    /**
     * Scope to filter only student history
     */
    public function scopeStudents($query)
    {
        return $query->where('user_type', 'student');
    }

    /**
     * Scope to filter only teacher history
     */
    public function scopeTeachers($query)
    {
        return $query->where('user_type', 'teacher');
    }

    /**
     * Check if this is a student history
     */
    public function isStudent()
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if this is a teacher history
     */
    public function isTeacher()
    {
        return $this->user_type === 'teacher';
    }

    /**
     * Get the attendee's full name
     */
    public function getAttendeeName()
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->lname . ', ' . $this->student->fname;
        }
        if ($this->isTeacher() && $this->teacherVisitor) {
            return $this->teacherVisitor->lname . ', ' . $this->teacherVisitor->fname;
        }
        return 'N/A';
    }
} 