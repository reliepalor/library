<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lname',
        'fname',
        'MI',
        'college',
        'year',
        'email',
        'qr_code_path',
        'archived',
        'archived_at',
    ];

    protected $casts = [
        'archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->lname}, {$this->fname}" . ($this->MI ? " {$this->MI}." : '');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    public function archive()
    {
        $this->update([
            'archived' => true,
            'archived_at' => now(),
        ]);
    }

    public function unarchive()
    {
        $this->update([
            'archived' => false,
            'archived_at' => null,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('archived', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function attendanceHistories()
    {
        return $this->hasMany(\App\Models\AttendanceHistory::class, 'student_id', 'student_id');
    }
}
