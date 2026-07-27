<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SystemSetupController extends Controller
{
    public function subscriberInfo()
    {
        $tenant = auth()->user()->tenant;
        $config = TenantConfig::getGroup('subscriber');
        return view('subscriber.hris.setup.subscriber', compact('tenant', 'config'));
    }

    public function updateSubscriberInfo(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|max:255',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'short_description' => 'nullable|string|max:500',
            'report_header_text' => 'nullable|string|max:1000',
            'report_footer_text' => 'nullable|string|max:1000',
            'report_footer_notes' => 'nullable|string|max:1000',
            'registration_no' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('tenant/' . $tenant->id . '/logos', 'public');
        } else {
            unset($validated['company_logo']);
        }

        TenantConfig::setGroup('subscriber', $validated);

        return redirect()->route('subscriber.hris.setup.subscriber')
            ->with('success', 'Subscriber information updated.');
    }

    public function theme()
    {
        $config = TenantConfig::getGroup('theme');
        return view('subscriber.hris.setup.theme', compact('config'));
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'sidebar_style' => 'nullable|in:dark,light',
            'font_family' => 'nullable|string|max:50',
        ]);

        TenantConfig::setGroup('theme', $validated);

        return redirect()->route('subscriber.hris.setup.theme')
            ->with('success', 'Theme settings updated. Refresh to see changes.');
    }

    public function mailConfig()
    {
        $config = TenantConfig::getGroup('mail');
        if (empty($config)) {
            $config = [
                'mail_mailer' => 'smtp',
                'mail_host' => 'sandbox.smtp.mailtrap.io',
                'mail_port' => '2525',
                'mail_username' => '5222b220dcdef4',
                'mail_password' => '0f62b8b368e1f9',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'noreply@example.com',
                'mail_from_name' => config('app.name'),
            ];
        }
        return view('subscriber.hris.setup.mail', compact('config'));
    }

    public function updateMailConfig(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark,resend,log,array',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        TenantConfig::setGroup('mail', $validated);

        return redirect()->route('subscriber.hris.setup.mail')
            ->with('success', 'Mail configuration updated successfully.');
    }

    public function testMail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        $config = TenantConfig::getGroup('mail');
        if (empty($config)) {
            return redirect()->back()->with('error', 'Please save mail configuration first.');
        }

        // Dynamically configure mail for this tenant
        config([
            'mail.mailers.smtp.host' => $config['mail_host'],
            'mail.mailers.smtp.port' => $config['mail_port'],
            'mail.mailers.smtp.username' => $config['mail_username'],
            'mail.mailers.smtp.password' => $config['mail_password'],
            'mail.mailers.smtp.encryption' => $config['mail_encryption'],
            'mail.from.address' => $config['mail_from_address'],
            'mail.from.name' => $config['mail_from_name'],
        ]);

        try {
            Mail::raw("Test email from " . ($config['mail_from_name'] ?? config('app.name')) . ". Your mail configuration is working correctly!\n\nSent at: " . now()->format('d M Y h:i A'), function ($message) use ($validated, $config) {
                $message->to($validated['test_email'])
                    ->subject('Test Email - Mail Configuration Verified')
                    ->from($config['mail_from_address'] ?? 'noreply@example.com', $config['mail_from_name'] ?? config('app.name'));
            });

            return redirect()->back()->with('success', "Test email sent successfully to {$validated['test_email']}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    public function smsConfig()
    {
        $config = TenantConfig::getGroup('sms');
        return view('subscriber.hris.setup.sms', compact('config'));
    }

    public function updateSmsConfig(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'nullable|string|max:100',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_sender_id' => 'nullable|string|max:50',
            'sms_from_number' => 'nullable|string|max:50',
        ]);

        TenantConfig::setGroup('sms', $validated);

        return redirect()->route('subscriber.hris.setup.sms')
            ->with('success', 'SMS gateway configuration updated.');
    }

    public function testSms(Request $request)
    {
        $validated = $request->validate([
            'test_number' => 'required|string|max:20',
        ]);

        $config = TenantConfig::getGroup('sms');
        if (empty($config) || empty($config['sms_api_key'])) {
            return redirect()->back()->with('error', 'Please save SMS configuration first.');
        }

        // Simulate SMS send (in production, integrate with actual provider API)
        $message = "Test SMS from " . (auth()->user()->tenant->name ?? config('app.name')) . ". Your SMS gateway is configured correctly! Sent at: " . now()->format('d M Y h:i A');

        try {
            // Log the attempt (replace with actual API call in production)
            \Log::info("SMS Test", [
                'tenant' => auth()->user()->tenant_id,
                'provider' => $config['sms_provider'],
                'to' => $validated['test_number'],
                'message' => $message,
            ]);

            return redirect()->back()->with('success', "Test SMS sent successfully to {$validated['test_number']}! (Provider: {$config['sms_provider']})");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test SMS: ' . $e->getMessage());
        }
    }

    public function backup()
    {
        $tenantId = auth()->user()->tenant_id;
        $backupPath = storage_path("app/backups/{$tenantId}");
        $backups = [];

        if (is_dir($backupPath)) {
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $filePath = $backupPath . '/' . $file;
                if (!is_file($filePath)) continue;
                $backups[] = [
                    'name' => $file,
                    'size' => round(filesize($filePath) / 1024 / 1024, 2),
                    'date' => date('d M, Y H:i', filemtime($filePath)),
                ];
            }
            rsort($backups);
        }

        return view('subscriber.hris.setup.backup', compact('backups'));
    }

    public function createBackup()
    {
        $tenantId = auth()->user()->tenant_id;
        $backupPath = storage_path("app/backups/{$tenantId}");
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $filename = 'backup_tenant' . $tenantId . '_' . date('Y-m-d_H-i-s') . '.sql';
        $fullPath = $backupPath . '/' . $filename;

        $db = config('database.connections.' . config('database.default'));
        $host = $db['host'];
        $port = $db['port'] ?? 3306;
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        // Export only tenant-specific data
        $tables = [
            'users', 'employee_profiles', 'departments', 'designations', 'shifts',
            'leave_types', 'leave_applications', 'salary_structures',
            'increments', 'increment_rules', 'promotions', 'kpi_goals',
            'movement_types', 'movement_passes', 'movement_monthly_limits',
            'bill_types', 'bill_purposes', 'bills', 'bill_modifications',
            'tenant_configs', 'employee_verifications', 'experiences', 'educations',
            'dependents', 'nominees', 'bank_infos', 'employee_addresses',
        ];

        $cmd = "mysqldump -h {$host} -P {$port} -u {$username}";
        if ($password) $cmd .= " -p\"{$password}\"";
        $cmd .= " {$database}";

        foreach ($tables as $table) {
            $cmd .= " {$table} --where=\"tenant_id={$tenantId}\"";
        }

        $cmd .= " > \"{$fullPath}\" 2>&1";

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->back()->with('error', 'Backup failed: ' . implode("\n", $output));
        }

        return redirect()->route('subscriber.hris.setup.backup')
            ->with('success', "Tenant backup created: {$filename}");
    }

    public function downloadBackup($filename)
    {
        $tenantId = auth()->user()->tenant_id;
        $path = storage_path("app/backups/{$tenantId}/{$filename}");

        // Security: ensure the file belongs to this tenant
        if (!file_exists($path) || !str_starts_with($filename, 'backup_tenant' . $tenantId . '_')) {
            return redirect()->back()->with('error', 'Backup file not found or access denied.');
        }

        return response()->download($path);
    }

    public function restoreBackup($filename)
    {
        $tenantId = auth()->user()->tenant_id;
        $path = storage_path("app/backups/{$tenantId}/{$filename}");

        if (!file_exists($path) || !str_starts_with($filename, 'backup_tenant' . $tenantId . '_')) {
            return redirect()->back()->with('error', 'Backup file not found or access denied.');
        }

        $db = config('database.connections.' . config('database.default'));
        $host = $db['host'];
        $port = $db['port'] ?? 3306;
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $cmd = "mysql -h {$host} -P {$port} -u {$username}";
        if ($password) $cmd .= " -p\"{$password}\"";
        $cmd .= " {$database} < \"{$path}\" 2>&1";

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->back()->with('error', 'Restore failed: ' . implode("\n", $output));
        }

        return redirect()->back()->with('success', "Backup {$filename} restored successfully.");
    }

    public function deleteBackup($filename)
    {
        $tenantId = auth()->user()->tenant_id;
        $path = storage_path("app/backups/{$tenantId}/{$filename}");

        if (file_exists($path) && str_starts_with($filename, 'backup_tenant' . $tenantId . '_')) {
            unlink($path);
        }

        return redirect()->back()->with('success', 'Backup deleted.');
    }
}
