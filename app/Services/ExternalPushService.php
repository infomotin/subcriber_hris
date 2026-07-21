<?php

namespace App\Services;

use App\Http\Controllers\Api\MockRemoteServerController;
use App\Models\AttendanceLog;
use App\Models\TenantPushLog;
use App\Models\TenantWebhookSetting;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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

        // Check if endpoint is local mock server or local domain to prevent single-worker FastCGI deadlock & session corruption
        $parsedUrl = parse_url($setting->endpoint_url);
        $host = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '/';

        $isLocalEndpoint = in_array($host, ['amds.test', 'localhost', '127.0.0.1'])
            || str_contains($setting->endpoint_url, 'api/mock-remote-server');

        if ($isLocalEndpoint) {
            return $this->dispatchInternalMockPush($setting, $path, $formattedData, $logs);
        }

        // External HTTP cURL dispatch
        try {
            $httpClient = Http::timeout(10);

            // Configure Authentication Headers
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

    protected function dispatchInternalMockPush(TenantWebhookSetting $setting, string $path, mixed $formattedData, Collection $logs): array
    {
        $content = is_string($formattedData) ? $formattedData : json_encode($formattedData);
        $contentType = (is_string($formattedData) && ($setting->data_format === 'csv' || $setting->data_format === 'text'))
            ? 'text/plain'
            : 'application/json';

        $server = [
            'CONTENT_TYPE' => $contentType,
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_HOST' => 'amds.test',
        ];

        // Attach Auth Headers according to setting configuration
        switch ($setting->auth_type) {
            case 'bearer':
                if ($setting->auth_token) {
                    $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $setting->auth_token;
                }
                break;

            case 'api_key':
                if ($setting->auth_header_name && $setting->auth_token) {
                    $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $setting->auth_header_name));
                    $server[$headerKey] = $setting->auth_token;
                }
                break;

            case 'basic':
                if ($setting->auth_username) {
                    $server['PHP_AUTH_USER'] = $setting->auth_username;
                    $server['PHP_AUTH_PW'] = $setting->auth_password ?? '';
                }
                break;
        }

        $internalRequest = LaravelRequest::create($path, 'POST', [], [], [], $server, $content);

        // Execute target controller method directly to avoid session middleware pipeline overwriting user session
        $controller = app()->make(MockRemoteServerController::class);

        $cleanPath = rtrim($path, '/');

        if (str_contains($cleanPath, 'no-auth')) {
            $response = $controller->receiveNoAuth($internalRequest);
        } elseif (str_contains($cleanPath, 'bearer')) {
            $response = $controller->receiveBearer($internalRequest);
        } elseif (str_contains($cleanPath, 'api-key')) {
            $response = $controller->receiveApiKey($internalRequest);
        } elseif (str_contains($cleanPath, 'basic')) {
            $response = $controller->receiveBasic($internalRequest);
        } else {
            $response = $controller->receiveNoAuth($internalRequest);
        }

        $statusCode = $response->getStatusCode();
        $responseBody = mb_strimwidth($response->getContent(), 0, 1000, '...');
        $isSuccess = $response->isSuccessful();

        $setting->update([
            'last_pushed_at' => now(),
            'last_status_code' => $statusCode,
            'last_response_body' => $responseBody,
        ]);

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
