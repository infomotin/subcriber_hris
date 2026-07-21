<?php

namespace App\Events;

use App\Models\Device;
use App\Models\ZktecoUser;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSynced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Device $device,
        public ZktecoUser $user
    ) {}
}
