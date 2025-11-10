<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherVisitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teachers_visitors';

    protected $fillable = [
        'lname',
        'fname',
        'MI',
        'gender',
        'email',
        'department',
        'role',
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

    // Accessors to map database column names to expected attribute names
    public function getLastNameAttribute()
    {
        return $this->lname;
    }

    public function getFirstNameAttribute()
    {
        return $this->fname;
    }

    public function getMiddleNameAttribute()
    {
        return $this->MI;
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

    /**
     * Relationship to User model (if teachers have user accounts)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'teacher_visitor_email', 'email');
    }
}
