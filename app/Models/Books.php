<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_code',
        'name',
        'author',
        'author_number1',
        'author_number2',
        'author_number3',
        'author_number4',
        'author_number5',
        'description',
        'section',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'archived',
        'archived_at',
    ];

    protected $casts = [
        'archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function isBorrowed()
    {
        return \App\Models\BorrowedBook::where('book_id', $this->book_code)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->exists();
    }

    public function borrowedBy()
    {
        return \App\Models\BorrowedBook::where('book_id', $this->book_code)
            ->whereIn('status', ['pending', 'approved'])
            ->whereNull('returned_at')
            ->with('student')
            ->latest()
            ->first();
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
}
