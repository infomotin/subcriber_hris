<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MockController extends Controller
{
    public function viewer(): JsonResponse
    {
        $payloads = Cache::get('mock_received_payloads', []);
        return response()->json(['payloads' => $payloads, 'count' => count($payloads)]);
    }

    public function clear(): JsonResponse
    {
        Cache::forget('mock_received_payloads');
        return response()->json(['message' => 'Mock payloads cleared']);
    }
}
