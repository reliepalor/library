<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeachersVisitorsAttendance extends Model
{
    use HasFactory;

    protected $table = 'teachers_visitors_attendance';

    protected $fillable = [
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
    ];

    protected $dates = [
        'login',
        'logout',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone('Asia/Manila')->format('Y-m-d H:i:s');
    }

    public function teacherVisitor()
    {
        return $this->belongsTo(TeacherVisitor::class, 'teacher_visitor_id', 'id');
    }
}
