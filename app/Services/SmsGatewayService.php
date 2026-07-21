<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\Tenant;

class SmsGatewayService
{
    public function sendSms(string $recipientPhone, string $message, ?Tenant $tenant = null): SmsLog
    {
        // Simulated SMS Dispatch via Gateway API
        $status = 'sent';
        $gatewayResponse = json_encode([
            'status_code' => 200,
            'message_id' => 'SMS_' . uniqid(),
            'sent_at' => now()->toIso8601String(),
        ]);

        return SmsLog::create([
            'tenant_id' => $tenant?->id,
            'recipient_phone' => $recipientPhone,
            'message' => $message,
            'status' => $status,
            'gateway_response' => $gatewayResponse,
        ]);
    }
}
