<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityAuditController extends Controller
{
    public function index()
    {
        $securityLogs = SystemLog::whereIn('level', ['warning', 'error'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $blockedIps = Cache::get('security_blocked_ips', []);

        $activeSessionsCount = 1; // Current active session

        return view('system_admin.security.index', compact('securityLogs', 'blockedIps', 'activeSessionsCount'));
    }

    public function blockIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255',
        ]);

        $blockedIps = Cache::get('security_blocked_ips', []);
        $blockedIps[] = [
            'ip' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? 'Manual IP Security Lockout',
            'blocked_at' => now()->toDateTimeString(),
        ];

        Cache::put('security_blocked_ips', $blockedIps);

        return redirect()->route('admin.system.security.index')
            ->with('success', "IP Address {$validated['ip_address']} has been blocked successfully.");
    }

    public function unblockIp(Request $request)
    {
        $ipToUnblock = $request->input('ip_address');

        $blockedIps = Cache::get('security_blocked_ips', []);
        $filtered = array_filter($blockedIps, fn ($item) => $item['ip'] !== $ipToUnblock);

        Cache::put('security_blocked_ips', array_values($filtered));

        return redirect()->route('admin.system.security.index')
            ->with('success', "IP Address {$ipToUnblock} unblocked successfully.");
    }
}
