<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowedBook extends Model
{
    protected $fillable = [
        'student_id',
        'book_id',
        'status',
        'rejection_reason',
        'returned_at',
    ];

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'book_code');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
