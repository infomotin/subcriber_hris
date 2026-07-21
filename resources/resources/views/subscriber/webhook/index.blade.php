@extends('layouts.subscriber')

@section('title', 'External Server Data Push & Webhook Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-send text-primary me-2 font-size-22"></i> External Server Data Push & Webhooks</h4>
        <p class="text-muted font-size-13 mb-0">Push attendance records from this ZKTeco cloud engine to your remote ERP, Server, or API endpoint.</p>
    </div>
    @if(!empty($setting->endpoint_url))
        <form action="{{ route('subscriber.webhook.test') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bx bx-paper-plane me-1"></i> Test Remote Push Now
            </button>
        </form>
    @endif
</div>

<!-- Last Response Banner if available -->
@if($setting->last_pushed_at)
    <div class="alert {{ $setting->last_status_code >= 200 && $setting->last_status_code < 300 ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1">
                    <i class="bx {{ $setting->last_status_code >= 200 && $setting->last_status_code < 300 ? 'bx-check-circle' : 'bx-error-circle' }} me-1"></i>
                    Last Remote Server Response: [HTTP {{ $setting->last_status_code }}]
                </h6>
                <small class="d-block font-monospace bg-white p-2 rounded text-dark mt-1" style="max-height: 80px; overflow-y: auto;">
                    {{ $setting->last_response_body ?? 'No response content' }}
                </small>
            </div>
            <div class="text-end">
                <span class="badge bg-dark font-size-12">Pushed: {{ $setting->last_pushed_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Webhook Configuration Form -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-cog text-primary me-2"></i> Webhook & Endpoint Settings</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('subscriber.webhook.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Remote Server Endpoint URL</label>
                        <input type="url" name="endpoint_url" class="form-control" placeholder="https://my-erp.company.com/api/v1/attendance-webhook" value="{{ old('endpoint_url', $setting->endpoint_url) }}">
                        <small class="text-muted">Enter the HTTPS or HTTP URL of your remote server where data should be pushed.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark font-size-13">Push Schedule Mode</label>
                            <select name="push_schedule" class="form-select border-secondary">
                                <option value="realtime" {{ $setting->push_schedule === 'realtime' ? 'selected' : '' }}>Realtime (Instant on Punch)</option>
                                <option value="hourly" {{ $setting->push_schedule === 'hourly' ? 'selected' : '' }}>Hourly Batch Push</option>
                                <option value="daily" {{ $setting->push_schedule === 'daily' ? 'selected' : '' }}>Daily Summary Push</option>
                                <option value="manual" {{ $setting->push_schedule === 'manual' ? 'selected' : '' }}>Manual Push Only</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark font-size-13">Data Payload Format</label>
                            <select name="data_format" class="form-select border-secondary">
                                <option value="json" {{ $setting->data_format === 'json' ? 'selected' : '' }}>JSON (API Standard)</option>
                                <option value="csv" {{ $setting->data_format === 'csv' ? 'selected' : '' }}>CSV / Plain Text Stream</option>
                                <option value="excel" {{ $setting->data_format === 'excel' ? 'selected' : '' }}>Excel Matrix Payload</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-lock-alt text-primary me-2"></i> API Authentication & Headers</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Authentication Method</label>
                        <select name="auth_type" id="selectAuthType" class="form-select border-secondary">
                            <option value="none" {{ $setting->auth_type === 'none' ? 'selected' : '' }}>None (Public Endpoint)</option>
                            <option value="bearer" {{ $setting->auth_type === 'bearer' ? 'selected' : '' }}>Bearer Token</option>
                            <option value="api_key" {{ $setting->auth_type === 'api_key' ? 'selected' : '' }}>API Key / Custom Header</option>
                            <option value="basic" {{ $setting->auth_type === 'basic' ? 'selected' : '' }}>Basic Auth (User & Password)</option>
                        </select>
                    </div>

                    <div id="authBearerBox" class="auth-subbox mb-3" style="display: none;">
                        <label class="form-label fw-bold font-size-13">Bearer Token</label>
                        <input type="text" name="auth_token" class="form-control" placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6..." value="{{ old('auth_token', $setting->auth_token) }}">
                    </div>

                    <div id="authApiKeyBox" class="auth-subbox mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Header Name</label>
                                <input type="text" name="auth_header_name" class="form-control" placeholder="X-API-KEY" value="{{ old('auth_header_name', $setting->auth_header_name) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Header Secret Value</label>
                                <input type="text" name="auth_token" class="form-control" placeholder="secret_key_12345" value="{{ old('auth_token', $setting->auth_token) }}">
                            </div>
                        </div>
                    </div>

                    <div id="authBasicBox" class="auth-subbox mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Basic Auth Username</label>
                                <input type="text" name="auth_username" class="form-control" placeholder="api_user" value="{{ old('auth_username', $setting->auth_username) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Basic Auth Password</label>
                                <input type="password" name="auth_password" class="form-control" placeholder="••••••••" value="{{ old('auth_password', $setting->auth_password) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 form-check form-switch bg-light p-3 rounded border">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="is_enabled" id="switchIsEnabled" value="1" {{ $setting->is_enabled ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="switchIsEnabled">
                            Enable Remote Data Push Service
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bx bx-save me-1"></i> Save Configuration
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Documentation & Preview Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-code-alt text-primary me-2"></i> Sample Payload Preview</h5>
            </div>
            <div class="card-body p-4">
                <p class="font-size-13 text-muted">When a ZKTeco biometric machine pushes an attendance punch, your remote endpoint receives a POST request structured as follows:</p>

                <h6 class="font-size-12 fw-bold text-uppercase text-primary mt-3">JSON Standard Payload Format:</h6>
                <pre class="bg-dark text-success p-3 rounded font-size-12" style="max-height: 320px;"><code>{
  "event": "attendance.push",
  "tenant_token": "{{ $tenant->tenant_token }}",
  "pushed_at": "2026-07-21T12:00:00Z",
  "count": 1,
  "data": [
    {
      "id": 42,
      "pin": "1001",
      "user_name": "Rahim Ahmed",
      "device_serial": "ZKT-ACME-001",
      "punched_at": "2026-07-21 09:00:00",
      "status": 0,
      "status_label": "Check In",
      "verify_type": 1,
      "verify_type_label": "Fingerprint"
    }
  ]
}</code></pre>
            </div>
        </div>
    </div>
</div>

<!-- Execution Push History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-primary me-2"></i> Push Execution Audit Logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Target Endpoint URL</th>
                        <th>Format</th>
                        <th>Records</th>
                        <th>HTTP Status</th>
                        <th>Remote Server Response</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pushLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                            <td><code class="text-dark">{{ mb_strimwidth($log->endpoint_url, 0, 40, '...') }}</code></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($log->data_format) }}</span></td>
                            <td><span class="badge bg-info">{{ $log->records_count }} Records</span></td>
                            <td>
                                <span class="badge {{ $log->is_success ? 'bg-success' : 'bg-danger' }}">
                                    HTTP {{ $log->status_code ?? 'ERR' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                    {{ $log->response_body }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No push execution logs recorded yet. Click "Test Remote Push Now" above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAuth = document.getElementById('selectAuthType');
        const boxBearer = document.getElementById('authBearerBox');
        const boxApiKey = document.getElementById('authApiKeyBox');
        const boxBasic = document.getElementById('authBasicBox');

        function toggleAuthBoxes() {
            if (!selectAuth) return;
            const val = selectAuth.value;
            
            boxBearer.style.display = (val === 'bearer') ? 'block' : 'none';
            boxApiKey.style.display = (val === 'api_key') ? 'block' : 'none';
            boxBasic.style.display = (val === 'basic') ? 'block' : 'none';
        }

        selectAuth?.addEventListener('change', toggleAuthBoxes);
        toggleAuthBoxes();
    });
</script>
@endpush
