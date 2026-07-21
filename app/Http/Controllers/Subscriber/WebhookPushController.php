<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantPushLog;
use App\Models\TenantWebhookSetting;
use App\Services\ExternalPushService;
use Illuminate\Http\Request;

class WebhookPushController extends Controller
{
    public function __construct(
        protected ExternalPushService $pushService
    ) {}

    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $setting = TenantWebhookSetting::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'push_schedule' => 'realtime',
                'scheduled_time' => '23:00',
                'data_format' => 'json',
                'date_format' => 'Y-m-d H:i:s',
                'custom_mapping' => [
                    'key_pin' => 'pin',
                    'key_name' => 'user_name',
                    'key_time' => 'punched_at',
                    'key_device' => 'device_serial',
                    'key_status' => 'status_label',
                    'key_verify' => 'verify_type_label',
                ],
                'auth_type' => 'none',
                'is_enabled' => false,
            ]
        );

        $pushLogs = TenantPushLog::where('tenant_id', $tenant->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('subscriber.webhook.index', compact('setting', 'pushLogs', 'tenant'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $setting = TenantWebhookSetting::firstOrCreate(['tenant_id' => $tenant->id]);

        $validated = $request->validate([
            'endpoint_url' => 'nullable|url|max:500',
            'push_schedule' => 'required|in:realtime,hourly,daily,manual',
            'scheduled_time' => 'nullable|string|max:5',
            'data_format' => 'required|in:json,csv,excel',
            'date_format' => 'required|string|max:50',
            'custom_mapping' => 'nullable|array',
            'auth_type' => 'required|in:none,bearer,api_key,basic',
            'auth_header_name' => 'nullable|string|max:255',
            'auth_token' => 'nullable|string',
            'auth_username' => 'nullable|string|max:255',
            'auth_password' => 'nullable|string|max:255',
            'is_enabled' => 'nullable|boolean',
        ]);

        $validated['is_enabled'] = $request->has('is_enabled');

        $setting->update($validated);

        return redirect()->route('subscriber.webhook.index')
            ->with('success', 'External Server Data Push & Schedule settings updated successfully.');
    }

    public function testPush(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $setting = TenantWebhookSetting::where('tenant_id', $tenant->id)->first();

        if (! $setting || empty($setting->endpoint_url)) {
            return back()->with('error', 'Please configure a valid Remote Server Webhook Endpoint URL first.');
        }

        $result = $this->pushService->dispatchPush($setting);

        if ($result['success']) {
            return back()->with('success', "Data Push Successful! Server Response: [HTTP {$result['status_code']}] {$result['response_body']}");
        } else {
            return back()->with('error', "Data Push Failed! Server Response: [HTTP {$result['status_code']}] {$result['response_body']}");
        }
    }
}
