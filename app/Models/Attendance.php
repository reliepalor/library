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
        'student_id',
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

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
