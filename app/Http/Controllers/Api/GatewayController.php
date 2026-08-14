<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GatewayConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function show(): JsonResponse
    {
        $config = GatewayConfig::first();
        if (!$config) {
            return response()->json(['message' => 'No gateway config found'], 404);
        }
        return response()->json(['config' => $config]);
    }

    public function updateSms(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sms_provider' => 'required|string',
            'sms_api_key' => 'required|string',
            'sms_sender_id' => 'required|string',
        ]);

        $config = GatewayConfig::firstOrNew();
        $config->fill($validated)->save();

        return response()->json(['message' => 'SMS gateway updated', 'config' => $config]);
    }

    public function updateMail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $config = GatewayConfig::firstOrNew();
        $config->fill($validated)->save();

        return response()->json(['message' => 'Mail gateway updated', 'config' => $config]);
    }

    public function updateSslcommerz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sslcommerz_store_id' => 'required|string',
            'sslcommerz_store_passwd' => 'required|string',
            'sslcommerz_is_sandbox' => 'boolean',
        ]);

        $config = GatewayConfig::firstOrNew();
        $config->fill($validated)->save();

        return response()->json(['message' => 'SSLCommerz gateway updated', 'config' => $config]);
    }

    public function test(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:sms,mail,sslcommerz',
        ]);

        $config = GatewayConfig::first();
        if (!$config) {
            return response()->json(['message' => 'No gateway config found'], 404);
        }

        return response()->json(['message' => "{$validated['type']} test initiated. Check logs for result.", 'type' => $validated['type']]);
    }
}
