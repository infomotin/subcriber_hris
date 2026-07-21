@extends('layouts.app')

@section('title', 'Network & System Settings')

@section('content')
<div class="page-title-box">
    <h4>Network & System Configuration</h4>
</div>

<div class="row">
    <!-- Network Settings Form -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-slider-alt me-1 text-primary"></i> Server Network & ADMS Protocol Config</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ADMS Listen IP Address</label>
                            <input type="text" name="server_ip" class="form-control" value="{{ old('server_ip', $settings['server_ip']) }}" required>
                            <small class="text-muted">Use <code>0.0.0.0</code> to listen on all interfaces or server IP.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ADMS Communication Port</label>
                            <input type="number" name="server_port" class="form-control" value="{{ old('server_port', $settings['server_port']) }}" required>
                            <small class="text-muted">Port configured on ZKTeco ADMS cloud server menu.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Heartbeat Timeout (s)</label>
                            <input type="number" name="heartbeat_timeout" class="form-control" value="{{ old('heartbeat_timeout', $settings['heartbeat_timeout']) }}" required>
                            <small class="text-muted">Mark device offline after elapsed timeout.</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Response Delay (s)</label>
                            <input type="number" name="response_delay" class="form-control" value="{{ old('response_delay', $settings['response_delay']) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Error Retry Delay (s)</label>
                            <input type="number" name="error_delay" class="form-control" value="{{ old('error_delay', $settings['error_delay']) }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Network Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Diagnostic & Port Test Tool -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bx bx-pulse me-1 text-primary"></i> Network Diagnostic & Connectivity Tester
            </div>
            <div class="card-body">
                <p class="text-muted font-size-13">Use this diagnostic utility to verify if a ZKTeco terminal IP or listening port is reachable through local firewall and routing policies.</p>

                <form id="diagnostic-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target IP / Hostname</label>
                        <input type="text" id="test_host" class="form-control" placeholder="192.168.1.100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Port</label>
                        <input type="number" id="test_port" class="form-control" value="8000" required>
                    </div>

                    <button type="button" id="btn-run-test" class="btn btn-info w-100"><i class="bx bx-broadcast me-1"></i> Run Connectivity Diagnostic Test</button>
                </form>

                <div id="diagnostic-result" class="mt-3 d-none"></div>
            </div>
        </div>

        <!-- ZKTeco Terminal Setup Quick Guide -->
        <div class="card">
            <div class="card-header bg-soft-primary text-primary font-size-14 fw-bold">
                <i class="bx bx-terminal me-1"></i> ZKTeco Terminal Setup Instructions
            </div>
            <div class="card-body font-size-13">
                <ol class="ps-3 mb-0">
                    <li class="mb-2">On your ZKTeco Terminal, go to: <strong>Menu &gt; COMM. &gt; Cloud Server Setting (ADMS)</strong>.</li>
                    <li class="mb-2">Set <strong>Server Address</strong>: Enter your Server Public IP or Domain (e.g. <code>192.168.1.50</code>).</li>
                    <li class="mb-2">Set <strong>Server Port</strong>: Enter <code>{{ $settings['server_port'] }}</code>.</li>
                    <li class="mb-2">Enable <strong>Enable Cloud Server</strong> checkbox and Save.</li>
                    <li>The device will automatically perform handshake and register under <strong>Biometric Devices</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btn-run-test').addEventListener('click', async function() {
        const host = document.getElementById('test_host').value;
        const port = document.getElementById('test_port').value;
        const resultDiv = document.getElementById('diagnostic-result');

        if (!host || !port) {
            alert('Please enter host and port!');
            return;
        }

        resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
        resultDiv.classList.add('alert', 'alert-info');
        resultDiv.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i> Testing connection to ' + host + ':' + port + '...';

        try {
            const response = await fetch("{{ route('admin.settings.test-connection') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ host, port })
            });

            const data = await response.json();
            resultDiv.classList.remove('alert-info');

            if (response.ok && data.success) {
                resultDiv.classList.add('alert-success');
                resultDiv.innerHTML = '<i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>' + data.message;
            } else {
                resultDiv.classList.add('alert-danger');
                resultDiv.innerHTML = '<i class="bx bx-x-circle me-2 font-size-18 align-middle"></i>' + (data.message || 'Connection failed.');
            }
        } catch (err) {
            resultDiv.classList.remove('alert-info');
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = '<i class="bx bx-x-circle me-2 font-size-18 align-middle"></i> Diagnostic request failed: ' + err.message;
        }
    });
</script>
@endpush
