<?php

namespace App\Http\Controllers\Adms;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetRequestController extends Controller
{
    public function __construct(
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request): Response
    {
        $serialNumber = $request->query('SN');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        $device = Device::where('serial_number', $serialNumber)->first();

        if (! $device) {
            return $this->responseBuilder->error();
        }

        $device->markAsOnline();

        // Retrieve pending commands for this device
        $commands = $device->commands()
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($commands as $command) {
            $command->markAsSent();
        }

        return $this->responseBuilder->pendingCommands($commands);
    }
}
