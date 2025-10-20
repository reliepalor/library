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

            // Recalculate available slots based on active study sessions from both tables
            $studentStudySessions = \App\Models\Attendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%')
                          ->orWhere('activity', 'like', '%reading%')
                          ->orWhere('activity', 'like', '%research%')
                          ->orWhere('activity', 'like', '%group study%')
                          ->orWhere('activity', 'like', '%computer use%')
                          ->orWhere('activity', 'like', '%meeting%');
                })
                ->count();

            $teacherVisitorStudySessions = \App\Models\TeachersVisitorsAttendance::whereNull('logout')
                ->where(function($query) {
                    $query->where('activity', 'like', '%study%')
                          ->orWhere('activity', 'like', '%stay%')
                          ->orWhere('activity', 'like', '%read%')
                          ->orWhere('activity', 'like', '%reading%')
                          ->orWhere('activity', 'like', '%research%')
                          ->orWhere('activity', 'like', '%group study%')
                          ->orWhere('activity', 'like', '%computer use%')
                          ->orWhere('activity', 'like', '%meeting%');
                })
                ->count();

            $totalActiveStudySessions = $studentStudySessions + $teacherVisitorStudySessions;

            $studyArea->available_slots = max(0, $studyArea->max_capacity - $totalActiveStudySessions);
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
