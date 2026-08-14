<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SysNetworkController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = NetworkSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        NetworkSetting::set($validated['key'], $validated['value'], $validated['description'] ?? null);

        return response()->json(['message' => 'Network setting updated', 'key' => $validated['key'], 'value' => $validated['value']]);
    }
}
