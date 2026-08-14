<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TenantWebhookSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $setting = TenantWebhookSetting::where('tenant_id', $request->user()->tenant_id)->first();
        if (!$setting) {
            return response()->json(['message' => 'No webhook setting found'], 404);
        }
        return response()->json(['setting' => $setting]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint_url' => 'required|url',
            'push_schedule' => 'nullable|string|in:manual,hourly,daily,weekly',
            'scheduled_time' => 'nullable|string',
            'data_format' => 'nullable|string',
            'date_format' => 'nullable|string',
            'custom_mapping' => 'nullable|array',
            'auth_type' => 'nullable|string',
            'auth_header_name' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'auth_username' => 'nullable|string',
            'auth_password' => 'nullable|string',
            'is_enabled' => 'boolean',
        ]);

        $setting = TenantWebhookSetting::updateOrCreate(
            ['tenant_id' => $request->user()->tenant_id],
            $validated
        );

        return response()->json(['message' => 'Webhook settings updated', 'setting' => $setting]);
    }

    public function testPush(Request $request): JsonResponse
    {
        $setting = TenantWebhookSetting::where('tenant_id', $request->user()->tenant_id)->first();
        if (!$setting || !$setting->endpoint_url) {
            return response()->json(['message' => 'No webhook endpoint configured'], 404);
        }

        $payload = ['test' => true, 'message' => 'Test push from AMDS', 'timestamp' => now()->toIso8601String()];

        try {
            $response = Http::timeout(10)->post($setting->endpoint_url, $payload);
            $setting->update([
                'last_pushed_at' => now(),
                'last_status_code' => $response->status(),
                'last_response_body' => substr($response->body(), 0, 1000),
            ]);
            return response()->json([
                'message' => 'Test push sent',
                'status_code' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            $setting->update([
                'last_pushed_at' => now(),
                'last_status_code' => 0,
                'last_response_body' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Test push failed', 'error' => $e->getMessage()], 500);
        }
    }
}
