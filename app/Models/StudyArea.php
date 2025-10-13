<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StudyArea extends Model
{
    protected $fillable = [
        'name',
        'max_capacity',
        'available_slots'
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('study_area_availability');
        });
    }

    public static function getAvailableSlots()
    {
        return Cache::remember('study_area_availability', now()->addSeconds(5), function () {
            $studyArea = self::firstOrCreate(
                ['name' => 'Main Study Area'],
                ['max_capacity' => 30, 'available_slots' => 30]
            );

            // Recalculate available slots based on active study sessions
            $activeStudySessions = \App\Models\Attendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%');
                })
                ->count();

            $studyArea->available_slots = max(0, $studyArea->max_capacity - $activeStudySessions);
            $studyArea->save();

            return $studyArea;
        });
    }

    public function getStatusColorAttribute()
    {
        if ($this->available_slots <= 5) {
            return 'danger';
        } elseif ($this->available_slots <= 10) {
            return 'warning';
        }
        return 'success';
    }
}
