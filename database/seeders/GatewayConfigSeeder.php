<?php

namespace Database\Seeders;

use App\Models\GatewayConfig;
use Illuminate\Database\Seeder;

class GatewayConfigSeeder extends Seeder
{
    public function run(): void
    {
        GatewayConfig::firstOrCreate(
            ['id' => 1],
            [
                'sms_provider' => 'greenweb',
                'sms_api_key' => 'GW_SAMPLE_SECRET_API_KEY_123',
                'sms_sender_id' => 'ZKTecoSaaS',
                'mail_host' => 'sandbox.smtp.mailtrap.io',
                'mail_port' => 2525,
                'mail_username' => '5222b220dcdef4',
                'mail_password' => '0f62b8b368e1f9',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'noreply@amds.test',
                'mail_from_name' => 'ZKTeco ADMS SaaS System',
                'sslcommerz_store_id' => 'arobw6a3cf7767fa7c',
                'sslcommerz_store_passwd' => 'arobw6a3cf7767fa7c@ssl',
                'sslcommerz_is_sandbox' => true,
                'two_factor_enabled' => false,
                'captcha_enabled' => false,
                'captcha_site_key' => '',
                'captcha_secret_key' => '',
                'honeypot_enabled' => false,
            ]
        );
    }
}
