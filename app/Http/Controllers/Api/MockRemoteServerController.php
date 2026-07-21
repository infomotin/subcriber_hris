<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MockRemoteServerController extends Controller
{
    public function receiveNoAuth(Request $request)
    {
        return $this->processIncomingPayload($request, 'No Auth (Public)');
    }

    public function receiveBearer(Request $request)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['status' => 'error', 'message' => 'Missing Authorization Bearer token header'], 401);
        }

        return $this->processIncomingPayload($request, "Bearer Auth (Token: {$token})");
    }

    public function receiveApiKey(Request $request)
    {
        $headerName = $request->header('X-API-KEY') ?? $request->header('x-api-key');

        if (! $headerName) {
            return response()->json(['status' => 'error', 'message' => 'Missing X-API-KEY authentication header'], 401);
        }

        return $this->processIncomingPayload($request, "API Key Auth (Key: {$headerName})");
    }

    public function receiveBasic(Request $request)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if (! $username) {
            return response()->json(['status' => 'error', 'message' => 'Missing Basic Authentication credentials'], 401);
        }

        return $this->processIncomingPayload($request, "Basic Auth (User: {$username})");
    }

    protected function processIncomingPayload(Request $request, string $authMethod)
    {
        $content = $request->getContent();
        $contentType = $request->header('Content-Type');

        $receivedEntry = [
            'id' => uniqid(),
            'auth_method' => $authMethod,
            'content_type' => $contentType,
            'headers' => $request->headers->all(),
            'body' => $content,
            'received_at' => now()->toIso8601String(),
        ];

        $existing = Cache::get('mock_received_payloads', []);
        array_unshift($existing, $receivedEntry);
        $existing = array_slice($existing, 0, 50); // Keep last 50 payloads
        Cache::put('mock_received_payloads', $existing, now()->addDays(1));

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance webhook payload received and verified successfully by mock server.',
            'auth_method' => $authMethod,
            'received_bytes' => strlen($content),
            'received_at' => now()->toIso8601String(),
        ], 200);
    }

    public function viewReceivedPayloads()
    {
        $payloads = Cache::get('mock_received_payloads', []);
        return view('subscriber.mock_viewer', compact('payloads'));
    }

    public function clearReceivedPayloads()
    {
        Cache::forget('mock_received_payloads');
        return back()->with('success', 'Mock remote server received payloads log cleared.');
    }
}
