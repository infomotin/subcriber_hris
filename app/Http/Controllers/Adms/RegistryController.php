<?php

namespace App\Http\Controllers\Adms;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Tenant;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegistryController extends Controller
{
    public function __construct(
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request, ?string $token = null): Response
    {
        $serialNumber = $request->query('SN');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        // Resolve tenant context (same as GetRequestController)
        $tenant = null;
        if ($token) {
            $tenant = Tenant::where('tenant_token', $token)->first();
        }
        if (! $tenant) {
            $deviceLookup = Device::withoutGlobalScopes()->where('serial_number', $serialNumber)->first();
            if ($deviceLookup && $deviceLookup->tenant) {
                $tenant = $deviceLookup->tenant;
            }
        }
        if (! $tenant) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => 'default'],
                [
                    'name' => 'Default Organization',
                    'tenant_token' => 'DEFAULT1234567890TOKEN',
                    'status' => 'active',
                    'max_devices' => 10,
                ]
            );
        }

        app()->instance('current_tenant_id', $tenant->id);

        $device = Device::withoutGlobalScopes()->where('serial_number', $serialNumber)->first();

        if (! $device) {
            return $this->responseBuilder->error();
        }

        $device->markAsOnline($request->ip());

        // Newer firmware registers capabilities with a key=value,key=value body
        $body = trim($request->getContent());
        $updateData = [];

        if ($body) {
            foreach (explode(',', $body) as $pair) {
                $pair = trim($pair);
                if (! str_contains($pair, '=')) {
                    continue;
                }
                [$key, $value] = explode('=', $pair, 2);
                $key = trim($key);
                $value = trim($value);

                if ($key === 'pushver') {
                    $updateData['push_version'] = $value;
                } elseif (in_array($key, ['language', 'FWVersion'], true)) {
                    $updateData['firmware_version'] = $value;
                } elseif (in_array($key, ['Firmware', 'Platform', 'SN'], true)) {
                    $updateData['firmware_version'] = $value;
                }
            }
        }

        if (! empty($updateData)) {
            $device->update($updateData);
        }

        \Illuminate\Support\Facades\Log::info("ADMS registry received", [
            'sn' => $serialNumber,
            'device_id' => $device->id,
            'ip' => $request->ip(),
            'body' => $body,
        ]);

        return $this->responseBuilder->ok();
    }
}
