<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverdueReminderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
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
}
