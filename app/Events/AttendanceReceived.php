<?php

namespace App\Events;

use App\Models\AttendanceLog;
use App\Models\Device;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Device $device,
        public AttendanceLog $attendanceLog
    ) {}
}
