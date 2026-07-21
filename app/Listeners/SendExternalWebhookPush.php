<?php

namespace App\Listeners;

use App\Events\AttendanceReceived;
use App\Models\TenantWebhookSetting;
use App\Services\ExternalPushService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class SendExternalWebhookPush implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected ExternalPushService $pushService
    ) {}

    public function handle(AttendanceReceived $event): void
    {
        $tenantId = $event->device->tenant_id;

        if (! $tenantId) {
            return;
        }

        $setting = TenantWebhookSetting::where('tenant_id', $tenantId)
            ->where('is_enabled', true)
            ->where('push_schedule', 'realtime')
            ->first();

        if ($setting && ! empty($setting->endpoint_url)) {
            $logs = collect([$event->attendanceLog]);
            $this->pushService->dispatchPush($setting, $logs);
        }
    }
}
