<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'user_type',
        'student_id',
        'teacher_visitor_id',
        'activity',
        'login',
        'logout',
        'system_logout',
    ];

    protected $casts = [
        'login' => 'datetime:Y-m-d H:i:s',
        'logout' => 'datetime:Y-m-d H:i:s',
        'system_logout' => 'boolean',
        'archived_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone('Asia/Manila')->format('Y-m-d H:i:s');
    }

    /**
     * Relationship to Student model
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship to TeacherVisitor model
     */
    public function teacherVisitor()
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
     * Scope to filter only student attendance
     */
    public function scopeStudents($query)
    {
        return $query->where('user_type', 'student');
    }

    /**
     * Scope to filter only teacher attendance
     */
    public function scopeTeachers($query)
    {
        return $query->where('user_type', 'teacher');
    }

    /**
     * Scope to get active sessions (not logged out yet)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logout');
    }

    /**
     * Scope to get completed sessions (logged out)
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('logout');
    }

    /**
     * Check if this is a student attendance
     */
    public function isStudent()
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if this is a teacher attendance
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

    /**
     * Get the attendee's email
     */
    public function getAttendeeEmail()
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->email;
        }
        if ($this->isTeacher() && $this->teacherVisitor) {
            return $this->teacherVisitor->email;
        }
        return null;
    }
}
