<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class AdmsResponseBuilder
{
    public function ok(): Response
    {
        return response('OK', 200, ['Content-Type' => 'text/plain']);
    }

    public function error(): Response
    {
        return response('ERROR', 200, ['Content-Type' => 'text/plain']);
    }

    public function deviceOptions(Device $device): Response
    {
        $config = config('zkteco-adms.response', [
            'error_delay' => 60,
            'delay' => 30,
            'realtime' => 1,
            'trans_times' => '00:00;14:00',
            'trans_interval' => 1,
            'trans_flag' => '1111000000',
            'time_zone' => 'UTC',
        ]);

        $options = [
            'GET OPTION FROM: ' . $device->serial_number,
            'Stamp=0',
            'OpStamp=0',
            'ErrorDelay=' . ($device->error_delay ?? $config['error_delay']),
            'Delay=' . ($device->delay ?? $config['delay']),
            'Realtime=' . ($device->realtime ? 1 : 0),
            'TransTimes=' . ($device->trans_times ?? $config['trans_times']),
            'TransInterval=' . ($device->trans_interval ?? $config['trans_interval']),
            'TransFlag=' . ($device->trans_flag ?? $config['trans_flag']),
            'TimeZone=' . ($device->timezone ?? $config['time_zone']),
        ];

        return response(implode("\n", $options), 200, ['Content-Type' => 'text/plain']);
    }

    public function pendingCommands(Collection $commands): Response
    {
        if ($commands->isEmpty()) {
            return $this->ok();
        }

        $lines = $commands->map(fn ($cmd) => $cmd->formatted_command)->toArray();

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
