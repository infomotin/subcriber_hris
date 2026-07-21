<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\TenantPushLog;
use App\Models\TenantWebhookSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalPushService
{
    public function dispatchPush(TenantWebhookSetting $setting, ?Collection $logs = null): array
    {
        if (empty($setting->endpoint_url)) {
            return [
                'success' => false,
                'status_code' => 400,
                'response_body' => 'Endpoint URL is empty.',
            ];
        }

        if (! $logs || $logs->isEmpty()) {
            app()->instance('current_tenant_id', $setting->tenant_id);
            $logs = AttendanceLog::with(['device', 'zktecoUser'])
                ->orderBy('punched_at', 'desc')
                ->take(20)
                ->get();
        }

        $formattedData = $this->formatPayload($setting, $logs);

        try {
            $httpClient = Http::timeout(15);

            // Configure Authentication
            switch ($setting->auth_type) {
                case 'bearer':
                    if ($setting->auth_token) {
                        $httpClient = $httpClient->withToken($setting->auth_token);
                    }
                    break;

                case 'api_key':
                    if ($setting->auth_header_name && $setting->auth_token) {
                        $httpClient = $httpClient->withHeaders([
                            $setting->auth_header_name => $setting->auth_token,
                        ]);
                    }
                    break;

                case 'basic':
                    if ($setting->auth_username) {
                        $httpClient = $httpClient->withBasicAuth($setting->auth_username, $setting->auth_password ?? '');
                    }
                    break;
            }

            // Send POST request
            if ($setting->data_format === 'csv' || $setting->data_format === 'text') {
                $response = $httpClient->withBody($formattedData, 'text/plain')
                    ->post($setting->endpoint_url);
            } else {
                $response = $httpClient->post($setting->endpoint_url, $formattedData);
            }

            $statusCode = $response->status();
            $responseBody = mb_strimwidth($response->body(), 0, 1000, '...');
            $isSuccess = $response->successful();

        } catch (\Throwable $e) {
            $statusCode = 500;
            $responseBody = 'HTTP Request Error: ' . $e->getMessage();
            $isSuccess = false;
        }

        // Save execution details to settings
        $setting->update([
            'last_pushed_at' => now(),
            'last_status_code' => $statusCode,
            'last_response_body' => $responseBody,
        ]);

        // Save log entry
        TenantPushLog::create([
            'tenant_id' => $setting->tenant_id,
            'endpoint_url' => $setting->endpoint_url,
            'data_format' => $setting->data_format,
            'records_count' => $logs->count(),
            'status_code' => $statusCode,
            'response_body' => $responseBody,
            'is_success' => $isSuccess,
        ]);

        return [
            'success' => $isSuccess,
            'status_code' => $statusCode,
            'response_body' => $responseBody,
            'records_count' => $logs->count(),
        ];
    }

    protected function formatPayload(TenantWebhookSetting $setting, Collection $logs): mixed
    {
        $mapping = array_merge([
            'key_pin' => 'pin',
            'key_name' => 'user_name',
            'key_time' => 'punched_at',
            'key_device' => 'device_serial',
            'key_status' => 'status_label',
            'key_verify' => 'verify_type_label',
        ], $setting->custom_mapping ?? []);

        $dateFormat = $setting->date_format ?? 'Y-m-d H:i:s';
        $formatTime = function ($dateTime) use ($dateFormat) {
            if (! $dateTime) return null;
            if ($dateFormat === 'timestamp') {
                return $dateTime->timestamp;
            }
            return $dateTime->format($dateFormat);
        };

        switch ($setting->data_format) {
            case 'csv':
                $output = "{$mapping['key_pin']},{$mapping['key_name']},{$mapping['key_device']},{$mapping['key_time']},{$mapping['key_status']},{$mapping['key_verify']}\n";
                foreach ($logs as $log) {
                    $userName = str_replace('"', '""', $log->zktecoUser->name ?? 'User #' . $log->pin);
                    $deviceSn = $log->device->serial_number ?? 'N/A';
                    $punchedAt = $formatTime($log->punched_at) ?? '';
                    $output .= "\"{$log->pin}\",\"{$userName}\",\"{$deviceSn}\",\"{$punchedAt}\",\"{$log->status_label}\",\"{$log->verify_type_label}\"\n";
                }
                return $output;

            case 'excel':
                return [
                    'sheet_name' => 'Attendance_Logs',
                    'tenant_token' => $setting->tenant->tenant_token ?? '',
                    'generated_at' => $formatTime(now()),
                    'columns' => [
                        $mapping['key_pin'],
                        $mapping['key_name'],
                        $mapping['key_device'],
                        $mapping['key_time'],
                        $mapping['key_status'],
                        $mapping['key_verify'],
                    ],
                    'rows' => $logs->map(fn ($log) => [
                        $mapping['key_pin'] => $log->pin,
                        $mapping['key_name'] => $log->zktecoUser->name ?? 'User #' . $log->pin,
                        $mapping['key_device'] => $log->device->serial_number ?? 'N/A',
                        $mapping['key_time'] => $formatTime($log->punched_at),
                        $mapping['key_status'] => $log->status_label,
                        $mapping['key_verify'] => $log->verify_type_label,
                    ])->toArray(),
                ];

            case 'json':
            default:
                return [
                    'event' => 'attendance.push',
                    'tenant_token' => $setting->tenant->tenant_token ?? '',
                    'pushed_at' => $formatTime(now()),
                    'count' => $logs->count(),
                    'data' => $logs->map(fn ($log) => [
                        'id' => $log->id,
                        $mapping['key_pin'] => $log->pin,
                        $mapping['key_name'] => $log->zktecoUser->name ?? null,
                        $mapping['key_device'] => $log->device->serial_number ?? null,
                        $mapping['key_time'] => $formatTime($log->punched_at),
                        'raw_status_code' => $log->status,
                        $mapping['key_status'] => $log->status_label,
                        'raw_verify_code' => $log->verify_type,
                        $mapping['key_verify'] => $log->verify_type_label,
                    ])->toArray(),
                ];
        }
    }
}
