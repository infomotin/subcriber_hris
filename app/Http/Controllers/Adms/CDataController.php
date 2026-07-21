<?php

namespace App\Http\Controllers\Adms;

use App\Events\AttendanceReceived;
use App\Events\DeviceConnected;
use App\Events\UserSynced;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\ZktecoUser;
use App\Services\AdmsRequestParser;
use App\Services\AdmsResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CDataController extends Controller
{
    public function __construct(
        protected AdmsRequestParser $parser,
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request): Response
    {
        $serialNumber = $request->query('SN');
        $table = $request->query('table');
        $options = $request->query('options');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        $device = $this->findOrCreateDevice($serialNumber, $request);

        if (! $device) {
            return $this->responseBuilder->error();
        }

        $device->markAsOnline();

        // GET request with options=all is device handshake/config request
        if ($request->isMethod('GET') && $options === 'all') {
            return $this->handleOptionsRequest($device, $request);
        }

        // POST request with table parameter is data submission
        if ($request->isMethod('POST') && $table) {
            return $this->handleDataSubmission($device, $table, $request);
        }

        return $this->responseBuilder->ok();
    }

    protected function findOrCreateDevice(string $serialNumber, Request $request): ?Device
    {
        $device = Device::firstOrCreate(
            ['serial_number' => $serialNumber],
            [
                'name' => 'Device ' . $serialNumber,
                'ip_address' => $request->ip(),
                'status' => 'online',
                'last_heartbeat' => now(),
            ]
        );

        if ($device->ip_address !== $request->ip()) {
            $device->update(['ip_address' => $request->ip()]);
        }

        return $device;
    }

    protected function handleOptionsRequest(Device $device, Request $request): Response
    {
        $updateData = [];

        if ($pushVersion = $request->query('pushver')) {
            $updateData['push_version'] = $pushVersion;
        }

        if ($firmware = $request->query('language')) {
            $updateData['firmware_version'] = $firmware;
        }

        if (! empty($updateData)) {
            $device->update($updateData);
        }

        event(new DeviceConnected($device));

        return $this->responseBuilder->deviceOptions($device);
    }

    protected function handleDataSubmission(Device $device, string $table, Request $request): Response
    {
        $body = $request->getContent();

        if (empty(trim($body))) {
            return $this->responseBuilder->ok();
        }

        switch (strtoupper($table)) {
            case 'ATTLOG':
                $this->processAttendanceLogs($device, $body);
                break;
            case 'USER':
            case 'USERINFO':
                $this->processUsers($device, $body);
                break;
        }

        return $this->responseBuilder->ok();
    }

    protected function processAttendanceLogs(Device $device, string $body): void
    {
        $logs = $this->parser->parseAttendanceLogs($body);
        $count = 0;

        foreach ($logs as $logData) {
            if (empty($logData['pin']) || empty($logData['punched_at'])) {
                continue;
            }

            $log = AttendanceLog::firstOrCreate(
                [
                    'device_id' => $device->id,
                    'pin' => $logData['pin'],
                    'punched_at' => $logData['punched_at'],
                ],
                [
                    'status' => $logData['status'],
                    'verify_type' => $logData['verify_type'],
                    'work_code' => $logData['work_code'],
                    'reserved_1' => $logData['reserved_1'],
                    'reserved_2' => $logData['reserved_2'],
                    'raw_data' => $logData['raw_data'],
                ]
            );

            if ($log->wasRecentlyCreated) {
                $count++;
                event(new AttendanceReceived($device, $log));
            }
        }

        if ($count > 0) {
            $device->increment('att_count', $count);
        }
    }

    protected function processUsers(Device $device, string $body): void
    {
        $users = $this->parser->parseUsers($body);
        $count = 0;

        foreach ($users as $userData) {
            if (empty($userData['pin'])) {
                continue;
            }

            $user = ZktecoUser::updateOrCreate(
                ['pin' => $userData['pin']],
                [
                    'device_id' => $device->id,
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'card_number' => $userData['card_number'],
                    'privilege' => $userData['privilege'],
                    'is_synced' => true,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $count++;
            }

            event(new UserSynced($device, $user));
        }

        if ($count > 0) {
            $device->increment('user_count', $count);
        }
    }
}
