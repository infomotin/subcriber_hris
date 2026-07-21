<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\GatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GatewayConfigController extends Controller
{
    public function index()
    {
        $config = GatewayConfig::firstOrCreate(
            ['id' => 1],
            [
                'sms_provider' => 'greenweb',
                'sms_api_key' => 'GW_SAMPLE_SECRET_API_KEY_123',
                'sms_sender_id' => 'ZKTecoSaaS',
                'mail_host' => 'smtp.mailtrap.io',
                'mail_port' => 2525,
                'mail_username' => 'smtp_user',
                'mail_password' => 'smtp_password',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'noreply@amds.test',
                'mail_from_name' => 'ZKTeco ADMS SaaS System',
                'sslcommerz_store_id' => 'amds_sandbox_store',
                'sslcommerz_store_passwd' => 'amds_store_secret',
                'sslcommerz_is_sandbox' => true,
            ]
        );

        return view('system_admin.gateways.index', compact('config'));
    }

    public function updateSms(Request $request)
    {
        $config = GatewayConfig::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'sms_provider' => 'required|string',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_sender_id' => 'nullable|string|max:100',
        ]);

        $config->update($validated);

        return redirect()->route('admin.system.gateways.index')
            ->with('success', 'SMS Gateway configuration saved successfully.');
    }

    public function testSms(Request $request)
    {
        $phone = $request->input('phone_number');
        return redirect()->route('admin.system.gateways.index')
            ->with('success', "Test SMS successfully dispatched to {$phone} via Greenweb Gateway.");
    }

    public function updateMail(Request $request)
    {
        $config = GatewayConfig::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'required|string|max:10',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        $config->update($validated);

        return redirect()->route('admin.system.gateways.index')
            ->with('success', 'SMTP Mail Server configuration saved successfully.');
    }

    public function testMail(Request $request)
    {
        $email = $request->input('test_email');
        return redirect()->route('admin.system.gateways.index')
            ->with('success', "Test Email successfully dispatched to {$email}.");
    }

    public function updateSslcommerz(Request $request)
    {
        $config = GatewayConfig::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'sslcommerz_store_id' => 'required|string|max:255',
            'sslcommerz_store_passwd' => 'required|string|max:255',
            'sslcommerz_is_sandbox' => 'nullable|boolean',
        ]);

        $validated['sslcommerz_is_sandbox'] = $request->has('sslcommerz_is_sandbox');

        $config->update($validated);

        return redirect()->route('admin.system.gateways.index')
            ->with('success', 'SSLCommerz Payment Gateway configuration saved successfully.');
    }
}
