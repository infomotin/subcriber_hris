@extends('layouts.subscriber')

@section('title', 'Mock Remote Server Log Viewer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-server text-success me-2 font-size-22"></i> Mock Remote Server Payload Inspector</h4>
        <p class="text-muted font-size-13 mb-0">Inspect real-time data payloads received by the mock external endpoints.</p>
    </div>
    <form action="{{ route('subscriber.mock.clear') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Clear received mock payloads log?')">
            <i class="bx bx-trash me-1"></i> Clear Received Payloads Log
        </button>
    </form>
</div>

<!-- Quick Test Endpoints Banner -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-link text-primary me-2"></i> Available Remote Server Test Endpoints</h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="bg-light p-3 rounded border">
                    <span class="badge bg-success font-size-11 mb-1">1. No Auth (Public)</span>
                    <code class="d-block font-size-12 mt-1">http://amds.test/api/mock-remote-server/no-auth</code>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-light p-3 rounded border">
                    <span class="badge bg-primary font-size-11 mb-1">2. Bearer Token Auth</span>
                    <code class="d-block font-size-12 mt-1">http://amds.test/api/mock-remote-server/bearer</code>
                    <small class="text-muted d-block mt-1 font-size-11">Token: <code>sample_bearer_123</code></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-light p-3 rounded border">
                    <span class="badge bg-warning text-dark font-size-11 mb-1">3. API Key Auth</span>
                    <code class="d-block font-size-12 mt-1">http://amds.test/api/mock-remote-server/api-key</code>
                    <small class="text-muted d-block mt-1 font-size-11">Header: <code>X-API-KEY: sample_key_456</code></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-light p-3 rounded border">
                    <span class="badge bg-info font-size-11 mb-1">4. Basic Auth</span>
                    <code class="d-block font-size-12 mt-1">http://amds.test/api/mock-remote-server/basic</code>
                    <small class="text-muted d-block mt-1 font-size-11">User: <code>api_user</code> / Pass: <code>secret123</code></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Received Payloads List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-receipt text-primary me-2"></i> Received Remote Server Payloads Feed</h5>
    </div>
    <div class="card-body p-0">
        @forelse($payloads as $payload)
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-success font-size-13"><i class="bx bx-check-circle me-1"></i> HTTP 200 OK</span>
                    <span class="badge bg-dark">{{ $payload['auth_method'] }}</span>
                    <small class="text-muted">{{ $payload['received_at'] }}</small>
                </div>

                <div class="bg-dark text-success p-3 rounded font-size-12 font-monospace" style="max-height: 200px; overflow-y: auto;">
                    {{ $payload['body'] }}
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bx bx-inbox font-size-48 d-block mb-2 text-secondary"></i>
                No remote server payloads received yet. Configure any of the test URLs above in <a href="{{ route('subscriber.webhook.index') }}" class="fw-bold text-primary">Data Push to Server</a> and click "Test Remote Push Now".
            </div>
        @endforelse
    </div>
</div>
@endsection
