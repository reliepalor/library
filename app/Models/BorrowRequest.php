<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $table = 'borrowed_books';

    protected $fillable = [
        'student_id',
        'book_id',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id', 'book_code');
    }
}
