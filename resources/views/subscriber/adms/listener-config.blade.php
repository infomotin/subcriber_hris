@extends('layouts.subscriber')

@section('title', 'ADMS Listener & Server Configuration')

@section('content')
<style>
    .config-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        transition: all 0.2s;
    }
    .config-card:hover {
        border-color: #c7d2fe;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.06);
    }
    .form-label-custom {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .help-text {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }
    .toggle-switch {
        width: 48px;
        height: 26px;
        cursor: pointer;
    }
    .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
</style>

<div class="page-title-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-server me-2 text-primary align-middle"></i> Listener & Server Configuration
        </h4>
    </div>
    <span class="badge bg-soft-primary text-primary font-size-11 px-3 py-2 rounded-pill">
        <i class="bx bx-key me-1"></i> Token: <strong>{{ $tenant->tenant_token }}</strong>
    </span>
</div>

<!-- Info Alert -->
<div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-start gap-3" style="background-color: #eff6ff !important; border-left: 4px solid #3b82f6 !important; border-radius: 12px !important;">
    <i class="bx bx-info-circle font-size-22 text-primary flex-shrink-0 mt-0.5"></i>
    <div>
        <strong class="d-block mb-1">ZKTeco ADMS Communication Gateway</strong>
        <p class="mb-0 font-size-13 text-slate-600">
            Configure how your ZKTeco biometric machines connect to this server. These settings define the network parameters
            that devices use to communicate with the AMDS cloud gateway. Update these values and configure them on your
            ZKTeco device's <strong>COMM &gt; ADMS Cloud Server</strong> menu.
        </p>
    </div>
</div>

<form method="POST" action="{{ route('subscriber.adms.listener-config.update') }}">
    @csrf

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- ADMS Server Port -->
            <div class="card config-card border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-box flex-shrink-0" style="background: #eef2ff; color: #6366f1;">
                            <i class="bx bx-plug"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label for="listener_port" class="form-label-custom">ADMS Server Port</label>
                            <div class="mb-1">
                                <input type="number" name="listener_port" id="listener_port"
                                    class="form-control @error('listener_port') is-invalid @enderror"
                                    value="{{ old('listener_port', $listener_port) }}"
                                    min="1" max="65535" required>
                                @error('listener_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="help-text mb-0">
                                <i class="bx bx-info-circle me-1 align-middle"></i>
                                Port ZKTeco devices connect to for sending attendance data. <strong>Default: 80 (HTTP)</strong>.
                                If using a reverse proxy like Nginx, keep port 80. Change only if running on a custom port.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Gateway Host / IP -->
            <div class="card config-card border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-box flex-shrink-0" style="background: #fef2f2; color: #ef4444;">
                            <i class="bx bx-globe"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label for="server_gateway" class="form-label-custom">Server Gateway Host / IP</label>
                            <div class="mb-1">
                                <input type="text" name="server_gateway" id="server_gateway"
                                    class="form-control @error('server_gateway') is-invalid @enderror"
                                    value="{{ old('server_gateway', $server_gateway) }}"
                                    placeholder="e.g. 15.235.229.40 or hr.nexogiant.com" required>
                                @error('server_gateway')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="help-text mb-0">
                                <i class="bx bx-info-circle me-1 align-middle"></i>
                                The public IP or domain ZKTeco machines use to reach this server.
                                <strong>Current server IP: 15.235.229.40</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-6">
            <!-- Device Heartbeat Interval -->
            <div class="card config-card border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-box flex-shrink-0" style="background: #f0fdf4; color: #22c55e;">
                            <i class="bx bx-pulse"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label for="heartbeat_interval" class="form-label-custom">Device Heartbeat Interval (Seconds)</label>
                            <div class="mb-1">
                                <input type="number" name="heartbeat_interval" id="heartbeat_interval"
                                    class="form-control @error('heartbeat_interval') is-invalid @enderror"
                                    value="{{ old('heartbeat_interval', $heartbeat_interval) }}"
                                    min="5" max="3600" required>
                                @error('heartbeat_interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="help-text mb-0">
                                <i class="bx bx-info-circle me-1 align-middle"></i>
                                How often (in seconds) ZKTeco devices ping the server to confirm connectivity.
                                <strong>Default: 30 seconds</strong>. Recommended: 30-120 seconds.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enable ZKTeco ADMS Communication Gateway -->
            <div class="card config-card border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-box flex-shrink-0" style="background: #fffbeb; color: #f59e0b;">
                            <i class="bx bx-shield-quarter"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label-custom">ZKTeco ADMS Communication Gateway</label>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div>
                                    <p class="mb-1 fw-medium text-slate-700">Enable ADMS Gateway</p>
                                    <p class="help-text mb-0">
                                        <i class="bx bx-info-circle me-1 align-middle"></i>
                                        Toggle to enable or disable the ZKTeco ADMS communication gateway.
                                        When disabled, devices will receive an <code>ERROR</code>
                                        response and cannot push attendance data.
                                    </p>
                                </div>
                                <div class="form-check form-switch ms-3 flex-shrink-0">
                                    <input class="form-check-input toggle-switch" type="checkbox" name="gateway_enabled"
                                        id="gateway_enabled" value="1"
                                        {{ old('gateway_enabled', $gateway_enabled) == '1' ? 'checked' : '' }}
                                        style="border-color: #cbd5e1; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Summary -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                <i class="bx bx-copy-alt me-2 text-primary"></i> Device Configuration Summary
            </h6>
            <p class="text-muted font-size-12 mb-3">
                Configure these values on your ZKTeco biometric machine under <strong>COMM &gt; ADMS Cloud Server</strong>:
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                        <span class="text-muted text-uppercase font-size-10 fw-bold d-block mb-1">Server Address</span>
                        <code class="font-size-14 fw-bold" id="summaryServerAddress">{{ $server_gateway }}</code>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2" onclick="copySummary('summaryServerAddress')" style="border-radius: 6px;">
                            <i class="bx bx-copy font-size-14"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                        <span class="text-muted text-uppercase font-size-10 fw-bold d-block mb-1">Port</span>
                        <code class="font-size-14 fw-bold" id="summaryPort">{{ $listener_port }}</code>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2" onclick="copySummary('summaryPort')" style="border-radius: 6px;">
                            <i class="bx bx-copy font-size-14"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                        <span class="text-muted text-uppercase font-size-10 fw-bold d-block mb-1">Heartbeat Interval</span>
                        <code class="font-size-14 fw-bold" id="summaryHeartbeat">{{ $heartbeat_interval }}s</code>
                    </div>
                </div>
            </div>
            <div class="mt-3 p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                <span class="text-muted text-uppercase font-size-10 fw-bold d-block mb-1">Full ADMS URL (New Protocol)</span>
                <code class="font-size-13" id="summaryFullUrl">{{ request()->getSchemeAndHttpHost() }}/iclock/{{ $tenant->tenant_token }}/cdata</code>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2" onclick="copySummary('summaryFullUrl')" style="border-radius: 6px;">
                    <i class="bx bx-copy font-size-14"></i>
                </button>
            </div>
            <div class="mt-2 p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                <span class="text-muted text-uppercase font-size-10 fw-bold d-block mb-1">Legacy ADMS URL (Old Protocol - IP Only)</span>
                <code class="font-size-13" id="summaryLegacyUrl">http://{{ $server_gateway }}:{{ $listener_port }}/iclock/cdata</code>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2" onclick="copySummary('summaryLegacyUrl')" style="border-radius: 6px;">
                    <i class="bx bx-copy font-size-14"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('subscriber.adms.overview') }}" class="btn btn-outline-secondary px-4 rounded-pill font-size-13">
            <i class="bx bx-arrow-back me-1"></i> Back to Overview
        </a>
        <button type="submit" class="btn btn-primary px-5 rounded-pill font-size-13 fw-bold"
            style="box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
            <i class="bx bx-save me-1"></i> Save Configuration
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('listener_port').addEventListener('input', function() {
        document.getElementById('summaryPort').textContent = this.value || '80';
        updateLegacyUrl();
    });
    document.getElementById('server_gateway').addEventListener('input', function() {
        document.getElementById('summaryServerAddress').textContent = this.value || '{{ request()->getHttpHost() }}';
        updateLegacyUrl();
    });
    document.getElementById('heartbeat_interval').addEventListener('input', function() {
        document.getElementById('summaryHeartbeat').textContent = (this.value || '30') + 's';
    });

    function updateLegacyUrl() {
        const host = document.getElementById('server_gateway').value || '{{ request()->getHttpHost() }}';
        const port = document.getElementById('listener_port').value || '80';
        document.getElementById('summaryLegacyUrl').textContent = 'http://' + host + ':' + port + '/iclock/cdata';
    }

    function copySummary(elementId) {
        const text = document.getElementById(elementId).textContent;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-check font-size-14"></i>';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-outline-success');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-outline-secondary');
            }, 1500);
        });
    }
</script>
@endpush
