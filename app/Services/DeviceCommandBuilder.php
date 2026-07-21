<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\ZktecoUser;

class DeviceCommandBuilder
{
    public function info(Device $device): DeviceCommand
    {
        return $this->createCommand($device, 'INFO', 'INFO');
    }

    public function reboot(Device $device): DeviceCommand
    {
        return $this->createCommand($device, 'REBOOT', 'REBOOT');
    }

    public function clearAttendanceLogs(Device $device): DeviceCommand
    {
        return $this->createCommand($device, 'CLEAR LOG', 'CLEAR');
    }

    public function clearAllData(Device $device): DeviceCommand
    {
        return $this->createCommand($device, 'CLEAR DATA', 'CLEAR');
    }

    public function syncUser(Device $device, ZktecoUser $user): DeviceCommand
    {
        $command = sprintf(
            'DATA UPDATE USERINFO PIN=%s\tName=%s\tPri=%d\tPasswd=%s\tCard=%s',
            $user->pin,
            $user->name ?? '',
            $user->privilege,
            $user->password ?? '',
            $user->card_number ?? ''
        );

        return $this->createCommand($device, $command, 'SYNC_USER');
    }

    public function deleteUser(Device $device, string $pin): DeviceCommand
    {
        $command = "DATA DELETE USERINFO PIN={$pin}";

        return $this->createCommand($device, $command, 'DELETE_USER');
    }

    protected function createCommand(Device $device, string $command, string $type): DeviceCommand
    {
        return $device->commands()->create([
            'command' => $command,
            'type' => $type,
            'status' => 'pending',
        ]);
    }
}
