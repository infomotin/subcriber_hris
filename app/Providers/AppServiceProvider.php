<?php

namespace App\Providers;

use App\Events\AttendanceReceived;
use App\Listeners\SyncPunchToRawData;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(AttendanceReceived::class, SyncPunchToRawData::class);
    }
}

