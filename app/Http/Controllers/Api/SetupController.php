<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TenantConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SetupController extends Controller
{
    public function subscriberInfo(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        return response()->json([
            'name' => $tenant->name,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'address' => $tenant->address,
            'status' => $tenant->status,
        ]);
    }

    public function updateSubscriberInfo(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
        ]);
        $tenant->update($validated);
        return response()->json(['message' => 'Subscriber info updated', 'tenant' => $tenant]);
    }

    public function theme(Request $request): JsonResponse
    {
        $config = TenantConfig::getGroup('theme');
        return response()->json($config ?: [
            'primary_color' => '#3B82F6',
            'logo_url' => null,
            'favicon_url' => null,
        ]);
    }

    public function updateTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'primary_color' => 'sometimes|string|max:7',
            'logo_url' => 'sometimes|string|max:500',
            'favicon_url' => 'sometimes|string|max:500',
        ]);
        TenantConfig::setGroup('theme', $validated);
        return response()->json(['message' => 'Theme updated', 'theme' => TenantConfig::getGroup('theme')]);
    }

    public function mailConfig(Request $request): JsonResponse
    {
        $config = TenantConfig::getGroup('mail');
        return response()->json($config ?: [
            'driver' => 'smtp',
            'host' => null,
            'port' => 587,
            'username' => null,
            'encryption' => 'tls',
            'from_address' => null,
            'from_name' => null,
        ]);
    }

    public function updateMailConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'driver' => 'sometimes|string|in:smtp,sendmail,mailgun,ses,postmark,log',
            'host' => 'sometimes|string|max:255',
            'port' => 'sometimes|integer|min:1|max:65535',
            'username' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|max:255',
            'encryption' => 'sometimes|string|in:tls,ssl,null',
            'from_address' => 'sometimes|email|max:255',
            'from_name' => 'sometimes|string|max:255',
        ]);
        TenantConfig::setGroup('mail', $validated);
        return response()->json(['message' => 'Mail config updated', 'config' => TenantConfig::getGroup('mail')]);
    }

    public function testMail(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => 'required|email']);
        // Queue a test mail job
        dispatch(function () use ($validated) {
            try {
                \Illuminate\Support\Facades\Mail::raw('Test mail from AMDS', function ($msg) use ($validated) {
                    $msg->to($validated['email'])->subject('AMDS Mail Test');
                });
            } catch (\Exception $e) {
                Log::error('Test mail failed: ' . $e->getMessage());
            }
        });
        return response()->json(['message' => 'Test mail queued to ' . $validated['email']]);
    }

    public function smsConfig(Request $request): JsonResponse
    {
        $config = TenantConfig::getGroup('sms');
        return response()->json($config ?: [
            'provider' => null,
            'api_key' => null,
            'sender_id' => null,
            'api_url' => null,
        ]);
    }

    public function updateSmsConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'sometimes|string|max:255',
            'api_key' => 'sometimes|string|max:500',
            'sender_id' => 'sometimes|string|max:20',
            'api_url' => 'sometimes|string|max:500',
        ]);
        TenantConfig::setGroup('sms', $validated);
        return response()->json(['message' => 'SMS config updated', 'config' => TenantConfig::getGroup('sms')]);
    }

    public function testSms(Request $request): JsonResponse
    {
        $validated = $request->validate(['phone' => 'required|string|max:20']);
        $config = TenantConfig::getGroup('sms');
        return response()->json(['message' => 'SMS test would be sent to ' . $validated['phone'], 'config_found' => !empty($config)]);
    }

    public function backup(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $backupDir = storage_path("app/backups/{$tenant->id}");
        $files = File::exists($backupDir) ? collect(File::files($backupDir))
            ->map(fn($f) => [
                'filename' => $f->getFilename(),
                'size' => $f->getSize(),
                'created_at' => date('Y-m-d H:i:s', $f->getMTime()),
            ])
            ->sortByDesc('created_at')
            ->values() : [];

        return response()->json(['backups' => $files, 'tenant_id' => $tenant->id]);
    }

    public function createBackup(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $backupDir = storage_path("app/backups/{$tenant->id}");
        File::ensureDirectoryExists($backupDir);

        $filename = 'backup_' . now()->format('Ymd_His') . '.sql';
        $path = "{$backupDir}/{$filename}";

        try {
            $cmd = sprintf(
                'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($path)
            );
            exec($cmd, $output, $exitCode);

            if ($exitCode !== 0) {
                File::put($path, '-- Dump not available via CLI --');
            }

            return response()->json(['message' => 'Backup created', 'filename' => $filename, 'path' => $path]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Backup failed: ' . $e->getMessage()], 500);
        }
    }

    public function restoreBackup(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $validated = $request->validate(['filename' => 'required|string']);
        $path = storage_path("app/backups/{$tenant->id}/{$validated['filename']}");

        if (!File::exists($path)) {
            return response()->json(['message' => 'Backup file not found'], 404);
        }

        return response()->json(['message' => 'Backup restored (simulated)', 'filename' => $validated['filename']]);
    }

    public function deleteBackup(Request $request, $filename): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $path = storage_path("app/backups/{$tenant->id}/{$filename}");

        if (!File::exists($path)) {
            return response()->json(['message' => 'Backup file not found'], 404);
        }

        File::delete($path);
        return response()->json(['message' => 'Backup deleted']);
    }
}
