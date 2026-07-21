@extends('layouts.subscriber')

@section('title', 'External Server Data Push & Custom Payload Mapping')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-send text-primary me-2 font-size-22"></i> External Server Data Push & Custom Mapping</h4>
        <p class="text-muted font-size-13 mb-0">Customize date/time formats, field key names, and payload structures for your remote server or ERP.</p>
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

<!-- Last Response Banner -->
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

<form action="{{ route('subscriber.webhook.update') }}" method="POST">
    @csrf
    <div class="row g-4 mb-4">
        <!-- Left Column: Webhook & Format Settings -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-slider-alt text-primary me-2"></i> Webhook & Endpoint Settings</h5>
                </div>
                <div class="card-body p-4">
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
                            <select name="data_format" id="selectDataFormat" class="form-select border-secondary">
                                <option value="json" {{ $setting->data_format === 'json' ? 'selected' : '' }}>JSON (API Standard)</option>
                                <option value="csv" {{ $setting->data_format === 'csv' ? 'selected' : '' }}>CSV / Plain Text Stream</option>
                                <option value="excel" {{ $setting->data_format === 'excel' ? 'selected' : '' }}>Excel Matrix Payload</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Custom Date & Time Format</label>
                        <select name="date_format" id="selectDateFormat" class="form-select border-secondary">
                            <option value="Y-m-d H:i:s" {{ $setting->date_format === 'Y-m-d H:i:s' ? 'selected' : '' }}>Standard SQL (2026-07-21 09:30:00)</option>
                            <option value="Y-m-d\TH:i:sP" {{ $setting->date_format === 'Y-m-d\TH:i:sP' ? 'selected' : '' }}>ISO 8601 (2026-07-21T09:30:00+06:00)</option>
                            <option value="d/m/Y H:i:s" {{ $setting->date_format === 'd/m/Y H:i:s' ? 'selected' : '' }}>UK / Asia (21/07/2026 09:30:00)</option>
                            <option value="d-m-Y h:i:s A" {{ $setting->date_format === 'd-m-Y h:i:s A' ? 'selected' : '' }}>12-Hour AM/PM (21-07-2026 09:30:00 AM)</option>
                            <option value="timestamp" {{ $setting->date_format === 'timestamp' ? 'selected' : '' }}>Unix Timestamp (Epoch Seconds)</option>
                        </select>
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
                </div>
            </div>
        </div>

        <!-- Right Column: Custom Mapping & Live Format Payload Preview -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-edit text-primary me-2"></i> Custom Field Key Mapping</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">User PIN / Employee ID Key</label>
                        <input type="text" name="custom_mapping[key_pin]" id="inputKeyPin" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_pin'] ?? 'pin' }}" placeholder="pin or employee_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">User Name Key</label>
                        <input type="text" name="custom_mapping[key_name]" id="inputKeyName" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_name'] ?? 'user_name' }}" placeholder="user_name or staff_name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Punch Time Key</label>
                        <input type="text" name="custom_mapping[key_time]" id="inputKeyTime" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_time'] ?? 'punched_at' }}" placeholder="punched_at or log_timestamp">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Device Serial Number Key</label>
                        <input type="text" name="custom_mapping[key_device]" id="inputKeyDevice" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_device'] ?? 'device_serial' }}" placeholder="device_serial or terminal_sn">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Check In / Out Status Key</label>
                        <input type="text" name="custom_mapping[key_status]" id="inputKeyStatus" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_status'] ?? 'status_label' }}" placeholder="status_label or check_mode">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Verification Method Key</label>
                        <input type="text" name="custom_mapping[key_verify]" id="inputKeyVerify" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_verify'] ?? 'verify_type_label' }}" placeholder="verify_type_label or auth_mode">
                    </div>
                </div>
            </div>

            <!-- Live Payload Format Code Preview Box -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-code-alt text-primary me-2"></i> Live <span id="previewFormatLabel">JSON</span> Payload Preview</h6>
                    <span class="badge bg-soft-primary text-primary" id="previewDateFormatBadge">Y-m-d H:i:s</span>
                </div>
                <div class="card-body p-3">
                    <pre class="bg-dark text-success p-3 rounded font-size-12 mb-0" style="max-height: 350px; overflow-y: auto;"><code id="previewCodeBox">// Live preview loading...</code></pre>
                </div>
            </div>
        </div>
    </div>
</form>

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
        // Elements
        const selectAuth = document.getElementById('selectAuthType');
        const boxBearer = document.getElementById('authBearerBox');
        const boxApiKey = document.getElementById('authApiKeyBox');
        const boxBasic = document.getElementById('authBasicBox');

        const selectDataFormat = document.getElementById('selectDataFormat');
        const selectDateFormat = document.getElementById('selectDateFormat');
        
        const inputKeyPin = document.getElementById('inputKeyPin');
        const inputKeyName = document.getElementById('inputKeyName');
        const inputKeyTime = document.getElementById('inputKeyTime');
        const inputKeyDevice = document.getElementById('inputKeyDevice');
        const inputKeyStatus = document.getElementById('inputKeyStatus');
        const inputKeyVerify = document.getElementById('inputKeyVerify');

        const previewFormatLabel = document.getElementById('previewFormatLabel');
        const previewDateFormatBadge = document.getElementById('previewDateFormatBadge');
        const previewCodeBox = document.getElementById('previewCodeBox');

        const tenantToken = "{{ $tenant->tenant_token }}";

        function toggleAuthBoxes() {
            if (!selectAuth) return;
            const val = selectAuth.value;
            boxBearer.style.display = (val === 'bearer') ? 'block' : 'none';
            boxApiKey.style.display = (val === 'api_key') ? 'block' : 'none';
            boxBasic.style.display = (val === 'basic') ? 'block' : 'none';
        }

        function getSampleTimestamp(format) {
            switch (format) {
                case 'Y-m-d\\TH:i:sP': return '2026-07-21T09:30:00+06:00';
                case 'd/m/Y H:i:s': return '21/07/2026 09:30:00';
                case 'd-m-Y h:i:s A': return '21-07-2026 09:30:00 AM';
                case 'timestamp': return '1784635800';
                case 'Y-m-d H:i:s':
                default: return '2026-07-21 09:30:00';
            }
        }

        function updateLivePayloadPreview() {
            const format = selectDataFormat ? selectDataFormat.value : 'json';
            const dateFormat = selectDateFormat ? selectDateFormat.value : 'Y-m-d H:i:s';
            const sampleTime = getSampleTimestamp(dateFormat);

            const keyPin = (inputKeyPin && inputKeyPin.value.trim()) ? inputKeyPin.value.trim() : 'pin';
            const keyName = (inputKeyName && inputKeyName.value.trim()) ? inputKeyName.value.trim() : 'user_name';
            const keyTime = (inputKeyTime && inputKeyTime.value.trim()) ? inputKeyTime.value.trim() : 'punched_at';
            const keyDevice = (inputKeyDevice && inputKeyDevice.value.trim()) ? inputKeyDevice.value.trim() : 'device_serial';
            const keyStatus = (inputKeyStatus && inputKeyStatus.value.trim()) ? inputKeyStatus.value.trim() : 'status_label';
            const keyVerify = (inputKeyVerify && inputKeyVerify.value.trim()) ? inputKeyVerify.value.trim() : 'verify_type_label';

            if (previewFormatLabel) previewFormatLabel.textContent = format.toUpperCase();
            if (previewDateFormatBadge) previewDateFormatBadge.textContent = dateFormat;

            if (format === 'csv') {
                const csvHeader = `${keyPin},${keyName},${keyDevice},${keyTime},${keyStatus},${keyVerify}\n`;
                const csvRow1 = `"1001","Rahim Ahmed","ZKT-ACME-001","${sampleTime}","Check In","Fingerprint"\n`;
                const csvRow2 = `"1002","Karim Hasan","ZKT-ACME-002","${sampleTime}","Check Out","Face"`;
                previewCodeBox.textContent = csvHeader + csvRow1 + csvRow2;
            } else if (format === 'excel') {
                const excelObj = {
                    "sheet_name": "Attendance_Logs",
                    "tenant_token": tenantToken,
                    "generated_at": sampleTime,
                    "columns": [keyPin, keyName, keyDevice, keyTime, keyStatus, keyVerify],
                    "rows": [
                        {
                            [keyPin]: "1001",
                            [keyName]: "Rahim Ahmed",
                            [keyDevice]: "ZKT-ACME-001",
                            [keyTime]: sampleTime,
                            [keyStatus]: "Check In",
                            [keyVerify]: "Fingerprint"
                        },
                        {
                            [keyPin]: "1002",
                            [keyName]: "Karim Hasan",
                            [keyDevice]: "ZKT-ACME-002",
                            [keyTime]: sampleTime,
                            [keyStatus]: "Check Out",
                            [keyVerify]: "Face"
                        }
                    ]
                };
                previewCodeBox.textContent = JSON.stringify(excelObj, null, 2);
            } else {
                // Default JSON
                const jsonObj = {
                    "event": "attendance.push",
                    "tenant_token": tenantToken,
                    "pushed_at": sampleTime,
                    "count": 2,
                    "data": [
                        {
                            "id": 101,
                            [keyPin]: "1001",
                            [keyName]: "Rahim Ahmed",
                            [keyDevice]: "ZKT-ACME-001",
                            [keyTime]: sampleTime,
                            "raw_status_code": 0,
                            [keyStatus]: "Check In",
                            "raw_verify_code": 1,
                            [keyVerify]: "Fingerprint"
                        },
                        {
                            "id": 102,
                            [keyPin]: "1002",
                            [keyName]: "Karim Hasan",
                            [keyDevice]: "ZKT-ACME-002",
                            [keyTime]: sampleTime,
                            "raw_status_code": 1,
                            [keyStatus]: "Check Out",
                            "raw_verify_code": 15,
                            [keyVerify]: "Face"
                        }
                    ]
                };
                previewCodeBox.textContent = JSON.stringify(jsonObj, null, 2);
            }
        }

        // Attach Event Listeners
        selectAuth?.addEventListener('change', toggleAuthBoxes);
        selectDataFormat?.addEventListener('change', updateLivePayloadPreview);
        selectDateFormat?.addEventListener('change', updateLivePayloadPreview);

        document.querySelectorAll('.mapping-input').forEach(input => {
            input.addEventListener('input', updateLivePayloadPreview);
        });

        toggleAuthBoxes();
        updateLivePayloadPreview();
    });
</script>
@endpush
