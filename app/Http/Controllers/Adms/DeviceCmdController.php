<?php

namespace App\Http\Controllers\Adms;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceCmdController extends Controller
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

        // Process device command return payload
        // Format of POST body: ID=1&Return=0&CMD=...
        $body = $request->getContent();
        if ($body) {
            $params = [];
            parse_str(str_replace('&', '&', $body), $params);

            if (isset($params['ID'])) {
                $commandId = (int) $params['ID'];
                $returnCode = isset($params['Return']) ? (int) $params['Return'] : 0;

                $command = DeviceCommand::where('id', $commandId)
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
