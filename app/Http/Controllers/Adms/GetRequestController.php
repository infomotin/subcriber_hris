<?php

namespace App\Http\Controllers\Adms;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\Tenant;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetRequestController extends Controller
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

        // Resolve tenant context (same as TenantCDataController)
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

        // Auto-mark old "sent" commands as executed — device polled again meaning it processed previous commands
        $device->commands()
            ->withoutGlobalScopes()
            ->where('status', 'sent')
            ->where('updated_at', '<', now()->subSeconds(10))
            ->update(['status' => 'executed', 'return_code' => 0, 'executed_at' => now()]);

        // Retrieve pending commands for this device
        $commands = $device->commands()
            ->withoutGlobalScopes()
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($commands as $command) {
            $command->markAsSent();
        }

        return $this->responseBuilder->pendingCommands($commands);
    }
}
