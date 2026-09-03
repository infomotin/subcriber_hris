@extends('layouts.subscriber')

@section('title', 'External Server Data Push & Scheduled Automation')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-send text-primary me-2"></i> Data Push & Scheduled Automation</h4>
        <p class="text-muted font-size-13 mb-0">Configure automatic real-time or scheduled pushes to your remote server/ERP endpoint.</p>
    </div>
    @if(!empty($setting->endpoint_url))
        <form action="{{ route('subscriber.webhook.test') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bx bx-paper-plane me-1"></i> Test Push Now
            </button>
        </form>
    @endif
</div>

@if($setting->last_pushed_at)
    <div class="alert {{ $setting->last_status_code >= 200 && $setting->last_status_code < 300 ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1 font-size-13">
                    <i class="bx {{ $setting->last_status_code >= 200 && $setting->last_status_code < 300 ? 'bx-check-circle' : 'bx-error-circle' }} me-1"></i>
                    Last Response: [HTTP {{ $setting->last_status_code }}]
                </h6>
                <small class="d-block font-monospace bg-white p-2 rounded text-dark mt-1" style="max-height: 60px; overflow-y: auto; font-size: 0.65rem;">
                    {{ $setting->last_response_body ?? 'No response content' }}
                </small>
            </div>
            <span class="badge bg-dark font-size-11">{{ $setting->last_pushed_at->diffForHumans() }}</span>
        </div>
    </div>
@endif

<form action="{{ route('subscriber.webhook.update') }}" method="POST">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-2 px-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-slider-alt text-primary me-2"></i> Endpoint & Schedule</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Remote Server Endpoint URL</label>
                        <input type="url" name="endpoint_url" class="form-control border-secondary" placeholder="https://my-erp.company.com/api/v1/attendance-webhook" value="{{ old('endpoint_url', $setting->endpoint_url) }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark font-size-13">Push Schedule Mode</label>
                            <select name="push_schedule" id="selectPushSchedule" class="form-select border-secondary">
                                <option value="realtime" {{ $setting->push_schedule === 'realtime' ? 'selected' : '' }}>Realtime (Instant)</option>
                                <option value="hourly" {{ $setting->push_schedule === 'hourly' ? 'selected' : '' }}>Hourly Batch</option>
                                <option value="daily" {{ $setting->push_schedule === 'daily' ? 'selected' : '' }}>Daily Scheduled</option>
                                <option value="manual" {{ $setting->push_schedule === 'manual' ? 'selected' : '' }}>Manual Only</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="boxScheduledTime" style="display: {{ $setting->push_schedule === 'daily' ? 'block' : 'none' }};">
                            <label class="form-label fw-bold text-dark font-size-13">Daily Push Time</label>
                            <input type="time" name="scheduled_time" class="form-control border-secondary" value="{{ old('scheduled_time', $setting->scheduled_time ?? '23:00') }}">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark font-size-13">Data Format</label>
                            <select name="data_format" id="selectDataFormat" class="form-select border-secondary">
                                <option value="json" {{ $setting->data_format === 'json' ? 'selected' : '' }}>JSON</option>
                                <option value="csv" {{ $setting->data_format === 'csv' ? 'selected' : '' }}>CSV</option>
                                <option value="excel" {{ $setting->data_format === 'excel' ? 'selected' : '' }}>Excel</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark font-size-13">Date Format</label>
                            <select name="date_format" id="selectDateFormat" class="form-select border-secondary">
                                <option value="Y-m-d H:i:s" {{ $setting->date_format === 'Y-m-d H:i:s' ? 'selected' : '' }}>SQL (2026-07-21 09:30:00)</option>
                                <option value="Y-m-d\TH:i:sP" {{ $setting->date_format === 'Y-m-d\TH:i:sP' ? 'selected' : '' }}>ISO 8601</option>
                                <option value="d/m/Y H:i:s" {{ $setting->date_format === 'd/m/Y H:i:s' ? 'selected' : '' }}>UK/Asia (21/07/2026)</option>
                                <option value="d-m-Y h:i:s A" {{ $setting->date_format === 'd-m-Y h:i:s A' ? 'selected' : '' }}>12-Hour AM/PM</option>
                                <option value="timestamp" {{ $setting->date_format === 'timestamp' ? 'selected' : '' }}>Unix Timestamp</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-bold text-dark mb-2 font-size-13"><i class="bx bx-lock-alt text-primary me-2"></i> API Authentication</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Auth Method</label>
                        <select name="auth_type" id="selectAuthType" class="form-select border-secondary">
                            <option value="none" {{ $setting->auth_type === 'none' ? 'selected' : '' }}>None (Public)</option>
                            <option value="bearer" {{ $setting->auth_type === 'bearer' ? 'selected' : '' }}>Bearer Token</option>
                            <option value="api_key" {{ $setting->auth_type === 'api_key' ? 'selected' : '' }}>API Key / Custom Header</option>
                            <option value="basic" {{ $setting->auth_type === 'basic' ? 'selected' : '' }}>Basic Auth</option>
                        </select>
                    </div>

                    <div id="authBearerBox" class="auth-subbox mb-3" style="display: none;">
                        <label class="form-label fw-bold font-size-13">Bearer Token</label>
                        <input type="text" name="auth_token" class="form-control" placeholder="eyJhbGciOiJIUzI1NiIs..." value="{{ old('auth_token', $setting->auth_token) }}">
                    </div>

                    <div id="authApiKeyBox" class="auth-subbox mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Header Name</label>
                                <input type="text" name="auth_header_name" class="form-control" placeholder="X-API-KEY" value="{{ old('auth_header_name', $setting->auth_header_name) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Header Value</label>
                                <input type="text" name="auth_token" class="form-control" placeholder="secret_key_12345" value="{{ old('auth_token', $setting->auth_token) }}">
                            </div>
                        </div>
                    </div>

                    <div id="authBasicBox" class="auth-subbox mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Username</label>
                                <input type="text" name="auth_username" class="form-control" placeholder="api_user" value="{{ old('auth_username', $setting->auth_username) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold font-size-13">Password</label>
                                <input type="password" name="auth_password" class="form-control" placeholder="••••••••" value="{{ old('auth_password', $setting->auth_password) }}">
                            </div>
                        </div>
                    </div>

                    <div class="p-2 bg-light rounded border mb-3">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_enabled" id="switchIsEnabled" value="1" {{ $setting->is_enabled ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark cursor-pointer font-size-13" for="switchIsEnabled">Enable Remote Data Push</label>
                        </div>
                        <p class="font-size-11 text-muted mb-0">ON: Auto push active. OFF: Paused, manual test still works.</p>
                    </div>

                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bx bx-save me-1"></i> Save Configuration
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-2 px-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-edit text-primary me-2"></i> Custom Field Mapping</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">User PIN Key</label>
                        <input type="text" name="custom_mapping[key_pin]" id="inputKeyPin" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_pin'] ?? 'pin' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">User Name Key</label>
                        <input type="text" name="custom_mapping[key_name]" id="inputKeyName" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_name'] ?? 'user_name' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">Punch Time Key</label>
                        <input type="text" name="custom_mapping[key_time]" id="inputKeyTime" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_time'] ?? 'punched_at' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">Device Serial Key</label>
                        <input type="text" name="custom_mapping[key_device]" id="inputKeyDevice" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_device'] ?? 'device_serial' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">Status Key</label>
                        <input type="text" name="custom_mapping[key_status]" id="inputKeyStatus" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_status'] ?? 'status_label' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-size-12">Verify Key</label>
                        <input type="text" name="custom_mapping[key_verify]" id="inputKeyVerify" class="form-control border-secondary mapping-input" value="{{ $setting->custom_mapping['key_verify'] ?? 'verify_type_label' }}">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-code-alt text-primary me-2"></i> <span id="previewFormatLabel">JSON</span> Preview</h6>
                    <span class="badge bg-soft-primary text-primary font-size-10" id="previewDateFormatBadge">Y-m-d H:i:s</span>
                </div>
                <div class="card-body p-2">
                    <pre class="bg-dark text-success p-2 rounded font-size-11 mb-0" style="max-height: 250px; overflow-y: auto;"><code id="previewCodeBox">// Loading...</code></pre>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-primary me-2"></i> Push Execution Logs</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Endpoint</th>
                        <th>Format</th>
                        <th>Records</th>
                        <th>Status</th>
                        <th>Response</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pushLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                            <td><code class="text-dark">{{ mb_strimwidth($log->endpoint_url, 0, 35, '...') }}</code></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($log->data_format) }}</span></td>
                            <td><span class="badge bg-info">{{ $log->records_count }}</span></td>
                            <td><span class="badge {{ $log->is_success ? 'bg-success' : 'bg-danger' }}">HTTP {{ $log->status_code ?? 'ERR' }}</span></td>
                            <td><small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $log->response_body }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3"><i class="bx bx-info-circle me-1"></i> No push logs yet. Click "Test Push Now".</td></tr>
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
        const selectPushSchedule = document.getElementById('selectPushSchedule');
        const boxScheduledTime = document.getElementById('boxScheduledTime');
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

        function toggleScheduleBox() {
            if (!selectPushSchedule) return;
            boxScheduledTime.style.display = (selectPushSchedule.value === 'daily') ? 'block' : 'none';
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
                previewCodeBox.textContent = `${keyPin},${keyName},${keyDevice},${keyTime},${keyStatus},${keyVerify}\n"1001","Rahim Ahmed","ZKT-ACME-001","${sampleTime}","Check In","Fingerprint"\n"1002","Karim Hasan","ZKT-ACME-002","${sampleTime}","Check Out","Face"`;
            } else if (format === 'excel') {
                previewCodeBox.textContent = JSON.stringify({ sheet_name: "Attendance_Logs", tenant_token: tenantToken, generated_at: sampleTime, columns: [keyPin, keyName, keyDevice, keyTime, keyStatus, keyVerify], rows: [{ [keyPin]: "1001", [keyName]: "Rahim Ahmed", [keyDevice]: "ZKT-ACME-001", [keyTime]: sampleTime, [keyStatus]: "Check In", [keyVerify]: "Fingerprint" }, { [keyPin]: "1002", [keyName]: "Karim Hasan", [keyDevice]: "ZKT-ACME-002", [keyTime]: sampleTime, [keyStatus]: "Check Out", [keyVerify]: "Face" }] }, null, 2);
            } else {
                previewCodeBox.textContent = JSON.stringify({ event: "attendance.push", tenant_token: tenantToken, pushed_at: sampleTime, count: 2, data: [{ id: 101, [keyPin]: "1001", [keyName]: "Rahim Ahmed", [keyDevice]: "ZKT-ACME-001", [keyTime]: sampleTime, [keyStatus]: "Check In", [keyVerify]: "Fingerprint" }, { id: 102, [keyPin]: "1002", [keyName]: "Karim Hasan", [keyDevice]: "ZKT-ACME-002", [keyTime]: sampleTime, [keyStatus]: "Check Out", [keyVerify]: "Face" }] }, null, 2);
            }
        }

        selectAuth?.addEventListener('change', toggleAuthBoxes);
        selectPushSchedule?.addEventListener('change', toggleScheduleBox);
        selectDataFormat?.addEventListener('change', updateLivePayloadPreview);
        selectDateFormat?.addEventListener('change', updateLivePayloadPreview);
        document.querySelectorAll('.mapping-input').forEach(input => { input.addEventListener('input', updateLivePayloadPreview); });
        toggleAuthBoxes();
        toggleScheduleBox();
        updateLivePayloadPreview();
    });
</script>
@endpush
