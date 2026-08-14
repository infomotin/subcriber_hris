@extends('layouts.subscriber')

@section('title', 'My Biometric Machines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">My Biometric Machines</h4>
        <p class="text-muted font-size-13 mb-0">Manage and register physical ZKTeco terminals under your active plan quota.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary font-size-13 p-2">
            Machine Quota: {{ $devices->total() }} / {{ $tenant->max_devices }} Used
        </span>

        @if($tenant->canAddDevice())
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddDevice">
                <i class="bx bx-plus me-1"></i> Register Machine
            </button>
        @else
            <a href="{{ route('subscriber.plans') }}" class="btn btn-warning btn-sm" title="Quota Reached - Upgrade Plan">
                <i class="bx bx-crown me-1"></i> Quota Full - Upgrade Plan
            </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Machine Name</th>
                        <th>Serial Number</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Heartbeat Pulse</th>
                        <th>Users</th>
                        <th>Punches</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        <tr>
                            <td><strong class="text-dark">{{ $device->name }}</strong></td>
                            <td><code>{{ $device->serial_number }}</code></td>
                            <td>{{ $device->ip_address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $device->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $device->isOnline() ? 'ONLINE' : 'OFFLINE' }}
                                </span>
                            </td>
                            <td>{{ $device->last_heartbeat ? $device->last_heartbeat->diffForHumans() : 'Never' }}</td>
                            <td>{{ $device->user_count }}</td>
                            <td>{{ $device->att_count }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="checkDevice({{ $device->id }}, '{{ $device->name }}')" title="Test Communication">
                                    <i class="bx bx-signal-5"></i> Test
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No ZKTeco biometric machines registered under your tenant yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Register Biometric Machine -->
@if($tenant->canAddDevice())
<div class="modal fade" id="modalAddDevice" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('subscriber.devices.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-chip text-primary me-2"></i> Register New Biometric Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 font-size-13">
                    Machine Quota Remaining: <strong>{{ $tenant->max_devices - $devices->total() }} slots available</strong> on your active <strong>{{ $tenant->plan->name ?? 'Plan' }}</strong>.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Machine Name / Location</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Front Gate Terminal">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Machine Serial Number (SN)</label>
                    <input type="text" name="serial_number" class="form-control" required placeholder="e.g. ZKT99221100">
                    <small class="text-muted">Located on the sticker at the back of your ZKTeco device or under Sys Info &gt; Device Info.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Device IP Address (Optional)</label>
                    <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.1.150">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success px-4"><i class="bx bx-check me-1"></i> Register Machine</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal: Device Status Detail -->
<div class="modal fade" id="modalDeviceStatus" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-chip text-primary me-2"></i> <span id="statusDeviceName">Device</span> — Connection Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Checking device communication...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnRefreshStatus" onclick="refreshStatus()">
                    <i class="bx bx-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDeviceId = null;

function checkDevice(id, name) {
    currentDeviceId = id;
    document.getElementById('statusDeviceName').textContent = name;
    document.getElementById('statusBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Checking device communication...</p>
        </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('modalDeviceStatus'));
    modal.show();
    refreshStatus();
}

function refreshStatus() {
    if (!currentDeviceId) return;
    document.getElementById('btnRefreshStatus').disabled = true;
    document.getElementById('btnRefreshStatus').innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Checking...';

    fetch('{{ url('subscriber/devices') }}/' + currentDeviceId + '/status')
        .then(r => r.json())
        .then(d => {
            const onlineBadge = d.online
                ? '<span class="badge bg-success font-size-13 px-3 py-2"><i class="bx bx-check-circle me-1"></i> ONLINE</span>'
                : '<span class="badge bg-secondary font-size-13 px-3 py-2"><i class="bx bx-x-circle me-1"></i> OFFLINE</span>';

            const heartbeatHtml = d.last_heartbeat
                ? '<span title="' + d.last_heartbeat + '">' + d.last_heartbeat_humans + '</span>'
                : '<span class="text-danger">Never connected</span>';

            document.getElementById('statusBody').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <h6 class="fw-bold font-size-12 mb-3">Device Identity</h6>
                            <div class="font-size-13">
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Status</span>${onlineBadge}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Serial Number</span><code>${d.serial_number}</code></div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">IP Address</span>${d.ip_address || 'N/A'}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Port</span>${d.port}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Firmware</span>${d.firmware_version}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Push Version</span>${d.push_version}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Registered At</span>${d.registered_at}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <h6 class="fw-bold font-size-12 mb-3">Communication Status</h6>
                            <div class="font-size-13">
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Heartbeat</span>${heartbeatHtml}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Timezone</span>${d.timezone}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Real-time Push</span>${d.realtime ? '<span class="text-success">Enabled</span>' : '<span class="text-warning">Disabled</span>'}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Delay</span>${d.delay}s</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Error Delay</span>${d.error_delay}s</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Trans Times</span>${d.trans_times}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Trans Flag</span>${d.trans_flag}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <h6 class="fw-bold font-size-12 mb-2 text-green-700">Data Stats</h6>
                            <div class="row text-center">
                                <div class="col-4"><span class="fw-bold font-size-20 text-green-600">${d.user_count}</span><br><span class="font-size-11 text-muted">Users Synced</span></div>
                                <div class="col-4"><span class="fw-bold font-size-20 text-primary">${d.att_count}</span><br><span class="font-size-11 text-muted">Punches Received</span></div>
                                <div class="col-4">
                                    ${d.online
                                        ? '<span class="fw-bold font-size-20 text-success"><i class="bx bx-check-circle"></i></span><br><span class="font-size-11 text-muted">Communication OK</span>'
                                        : '<span class="fw-bold font-size-20 text-danger"><i class="bx bx-x-circle"></i></span><br><span class="font-size-11 text-muted">No Recent Heartbeat</span>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    ${!d.online ? `
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#fef2f2;border:1px solid #fecaca;">
                            <h6 class="fw-bold font-size-12 text-danger mb-1"><i class="bx bx-error-circle me-1"></i> Device appears OFFLINE</h6>
                            <p class="font-size-12 text-muted mb-0">
                                Last heartbeat was ${d.last_heartbeat_humans}. Ensure the device:<br>
                                • Has internet/LAN connectivity<br>
                                • Is configured with correct Server IP <code>15.235.229.40</code> and Port <code>80</code><br>
                                • Has ADMS Cloud Server enabled in COMM settings<br>
                                • Can reach this server (check firewall, proxy, DNS)
                            </p>
                        </div>
                    </div>` : ''}
                </div>
            `;
        })
        .catch(e => {
            document.getElementById('statusBody').innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="bx bx-error-circle font-size-48"></i>
                    <p class="mt-2">Failed to check device: ${e.message}</p>
                </div>
            `;
        })
        .finally(() => {
            document.getElementById('btnRefreshStatus').disabled = false;
            document.getElementById('btnRefreshStatus').innerHTML = '<i class="bx bx-refresh me-1"></i> Refresh';
        });
}
</script>
@endpush

@endsection
