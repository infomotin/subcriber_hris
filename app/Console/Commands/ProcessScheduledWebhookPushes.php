<?php

namespace App\Console\Commands;

use App\Models\TenantWebhookSetting;
use App\Services\ExternalPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessScheduledWebhookPushes extends Command
{
    protected $signature = 'webhook:push-scheduled';

    protected $description = 'Process scheduled background data pushes to subscriber remote server endpoints';

    public function handle(ExternalPushService $pushService): int
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $this->info("Checking scheduled webhook pushes at [{$now->toDateTimeString()}]...");

        $settings = TenantWebhookSetting::where('is_enabled', true)
            ->whereIn('push_schedule', ['hourly', 'daily'])
            ->get();

        $executed = 0;

        foreach ($settings as $setting) {
            $shouldPush = false;

            if ($setting->push_schedule === 'hourly') {
                // Run if last pushed over 50 minutes ago
                if (! $setting->last_pushed_at || $setting->last_pushed_at->diffInMinutes($now) >= 50) {
                    $shouldPush = true;
                }
            } elseif ($setting->push_schedule === 'daily') {
                // Check if current hour:minute matches scheduled_time or last pushed over 23 hours ago
                $scheduledTime = $setting->scheduled_time ?? '23:00';
                if ($currentTime === $scheduledTime || (! $setting->last_pushed_at || $setting->last_pushed_at->diffInHours($now) >= 23)) {
                    $shouldPush = true;
                }
            }

            if ($shouldPush) {
                $this->info("Dispatching scheduled push for Tenant ID: {$setting->tenant_id} [Mode: {$setting->push_schedule}]");
                $result = $pushService->dispatchPush($setting);
                $this->info("Push Result: Status {$result['status_code']}");
                $executed++;
            }
        }

        $this->info("Completed scheduled webhook processing. Dispatched {$executed} push jobs.");
        return Command::SUCCESS;
    }
}
