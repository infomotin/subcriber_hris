<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCommandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $commands = DeviceCommand::with(['device', 'device.tenant'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 30);

        $commands->getCollection()->transform(function ($cmd) {
            return [
                'id' => $cmd->id,
                'tenant_id' => $cmd->tenant_id,
                'tenant_name' => $cmd->device?->tenant?->name ?? 'Unknown',
                'device_id' => $cmd->device_id,
                'device_name' => $cmd->device?->name ?? 'Unknown',
                'command' => $cmd->command,
                'type' => $cmd->type,
                'status' => $cmd->status,
                'created_at' => $cmd->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($commands);
    }

    public function destroy($id): JsonResponse
    {
        $command = DeviceCommand::findOrFail($id);
        $command->delete();
        return response()->json(['message' => 'Command cancelled']);
    }
}
