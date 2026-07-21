@extends('layouts.system_admin')

@section('title', 'ZKTeco ADMS Network Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-wifi text-warning me-2 font-size-22"></i> ZKTeco ADMS Network Server Settings</h4>
        <p class="text-muted font-size-13 mb-0">Configure hardware listener port, gateway IP, push handshake interval, and active machine sockets.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Network Settings Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-slider-alt text-primary me-2"></i> Listener & Server Port Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.system.network.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">ADMS Push Server Port</label>
                        <input type="number" name="adms_port" class="form-control border-secondary" value="{{ old('adms_port', $setting->adms_port) }}" required>
                        <small class="text-muted">Port on which physical ZKTeco machines send HTTP/TCP requests (Default: 8000 or 80).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Server Gateway Host / IP</label>
                        <input type="text" name="gateway_ip" class="form-control border-secondary" value="{{ old('gateway_ip', $setting->gateway_ip) }}" required>
                        <small class="text-muted">Public IP or domain configured in ZKTeco machine ADMS settings.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Device Heartbeat Ping Interval (Seconds)</label>
                        <input type="number" name="push_interval" class="form-control border-secondary" value="{{ old('push_interval', $setting->push_interval) }}" required>
                    </div>

                    <div class="mb-4 form-check form-switch bg-light p-3 rounded border">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="is_adms_active" id="switchAdmsActive" value="1" {{ $setting->is_adms_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="switchAdmsActive">
                            Enable ZKTeco ADMS Communication Gateway
                        </label>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                        <i class="bx bx-save me-1"></i> Update Network Configuration
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active ADMS Connected Devices List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-chip text-primary me-2"></i> Active Biometric Machine Sockets</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Device Serial</th>
                                <th>Subscriber Tenant</th>
                                <th>Device IP</th>
                                <th>Heartbeat Ping</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeDevices as $dev)
                                <tr>
                                    <td><code class="fw-bold text-dark">{{ $dev->serial_number }}</code></td>
                                    <td><span class="badge bg-secondary">{{ $dev->tenant->name ?? 'Unassigned' }}</span></td>
                                    <td><small class="text-muted">{{ $dev->ip_address ?? 'Dynamic' }}</small></td>
                                    <td><small class="text-muted">{{ $dev->last_heartbeat ? $dev->last_heartbeat->diffForHumans() : 'Never' }}</small></td>
                                    <td>
                                        <span class="badge {{ $dev->status === 'online' ? 'bg-success' : 'bg-danger' }}">
                                            {{ strtoupper($dev->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No ZKTeco biometric devices registered in system.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
