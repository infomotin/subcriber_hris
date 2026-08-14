<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SysSecurityController extends Controller
{
    public function index(): JsonResponse
    {
        $blockedIps = Cache::get('blocked_ips', []);
        $securitySettings = [
            'two_factor_enabled' => NetworkSetting::get('two_factor_enabled', false),
            'captcha_enabled' => NetworkSetting::get('captcha_enabled', false),
            'max_login_attempts' => NetworkSetting::get('max_login_attempts', 5),
            'session_timeout' => NetworkSetting::get('session_timeout', 120),
        ];

        return response()->json([
            'blocked_ips' => $blockedIps,
            'settings' => $securitySettings,
        ]);
    }

    public function blockIp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ip' => 'required|ip',
            'reason' => 'nullable|string',
        ]);

        $blocked = Cache::get('blocked_ips', []);
        $blocked[$validated['ip']] = [
            'reason' => $validated['reason'] ?? 'Manual block',
            'blocked_at' => now()->toIso8601String(),
            'blocked_by' => $request->user()->id,
        ];
        Cache::forever('blocked_ips', $blocked);

        return response()->json(['message' => "IP {$validated['ip']} blocked", 'blocked_ips' => $blocked]);
    }

    public function unblockIp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ip' => 'required|ip',
        ]);

        $blocked = Cache::get('blocked_ips', []);
        unset($blocked[$validated['ip']]);
        Cache::forever('blocked_ips', $blocked);

        return response()->json(['message' => "IP {$validated['ip']} unblocked", 'blocked_ips' => $blocked]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'two_factor_enabled' => 'required|boolean',
            'captcha_enabled' => 'required|boolean',
            'max_login_attempts' => 'required|integer|min:1|max:50',
            'session_timeout' => 'required|integer|min:5|max:1440',
        ]);

        foreach ($validated as $key => $value) {
            NetworkSetting::set($key, $value, 'Security setting');
        }

        return response()->json(['message' => 'Security settings updated', 'settings' => $validated]);
    }
}
