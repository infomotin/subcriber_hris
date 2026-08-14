<?php

namespace App\Http\Controllers\Adms;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\Tenant;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceCmdController extends Controller
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

        // Resolve tenant context
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

        $device->markAsOnline();

        // Log the raw request for debugging
        \Illuminate\Support\Facades\Log::info("ADMS devicecmd received", [
            'sn' => $serialNumber,
            'device_id' => $device->id,
            'query' => $request->query(),
            'body' => $request->getContent(),
        ]);

        // Process device command return payload
        $body = $request->getContent();
        if ($body) {
            $params = [];
            parse_str(str_replace('&', '&', $body), $params);

            if (isset($params['ID'])) {
                $commandId = (int) $params['ID'];
                $returnCode = isset($params['Return']) ? (int) $params['Return'] : 0;

                $command = DeviceCommand::withoutGlobalScopes()
                    ->where('id', $commandId)
                    ->where('device_id', $device->id)
                    ->first();

                if ($command) {
                    $command->markAsExecuted($returnCode, $body);
                }
            }
        }

        return $this->responseBuilder->ok();
    }
}
