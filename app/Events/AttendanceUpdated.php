<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $record;
    public $userType; // 'student' or 'teacher'

    public function __construct($record, $userType)
    {
        $this->record = $record;
        $this->userType = $userType;
    }

    public function broadcastOn()
    {
        return new Channel('attendance-updates');
    }

    public function broadcastAs()
    {
        return 'attendance.updated';
    }
}
