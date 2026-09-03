@extends('layouts.subscriber')

@section('title', 'My Biometric Machines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-chip text-primary me-2"></i> Biometric Machines</h4>
        <p class="text-muted font-size-13 mb-0">Manage ZKTeco terminals under your active plan quota.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary font-size-12 p-2">
            {{ $devices->total() }} / {{ $tenant->max_devices }} Used
        </span>
        @if($tenant->canAddDevice())
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddDevice">
                <i class="bx bx-plus me-1"></i> Register
            </button>
        @else
            <a href="{{ route('subscriber.plans') }}" class="btn btn-warning btn-sm">
                <i class="bx bx-crown me-1"></i> Upgrade
            </a>
        @endif
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2"><i class="bx bx-chip"></i></div>
                    <div><span class="text-muted font-size-11">Total Machines</span><br><span class="fw-bold font-size-14">{{ $devices->total() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2"><i class="bx bx-check-circle"></i></div>
                    <div><span class="text-muted font-size-11">Online</span><br><span class="fw-bold font-size-14 text-success">{{ $devices->getCollection()->filter(fn($d) => $d->isOnline())->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2"><i class="bx bx-x-circle"></i></div>
                    <div><span class="text-muted font-size-11">Offline</span><br><span class="fw-bold font-size-14 text-secondary">{{ $devices->getCollection()->filter(fn($d) => !$d->isOnline())->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2"><i class="bx bx-crown"></i></div>
                    <div><span class="text-muted font-size-11">Quota Remaining</span><br><span class="fw-bold font-size-14">{{ $tenant->max_devices - $devices->total() }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="max-width: 180px;">
                <span class="input-group-text bg-light border-end-0 py-1"><i class="bx bx-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Search name or serial..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bx bx-search me-1"></i> Search</button>
            @if(request()->has('search'))
                <a href="{{ route('subscriber.devices.index') }}" class="btn btn-sm btn-outline-secondary px-2"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="text-muted font-size-11 ms-auto">Showing {{ $devices->firstItem() ?? 0 }}-{{ $devices->lastItem() ?? 0 }} of {{ number_format($devices->total()) }}</span>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:35px">#</th>
                        <th>Machine Name</th>
                        <th>Serial Number</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Heartbeat</th>
                        <th>Users</th>
                        <th>Punches</th>
                        <th style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        <tr>
                            <td class="text-muted">{{ $devices->firstItem() + $loop->index }}</td>
                            <td><strong class="text-dark">{{ $device->name }}</strong></td>
                            <td><code>{{ $device->serial_number }}</code></td>
                            <td>{{ $device->ip_address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $device->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $device->isOnline() ? 'ONLINE' : 'OFFLINE' }}
                                </span>
                            </td>
                            <td>{{ $device->last_heartbeat ? $device->last_heartbeat->diffForHumans() : 'Never' }}</td>
                            <td><span class="badge bg-info">{{ $device->user_count }}</span></td>
                            <td><span class="badge bg-info">{{ $device->att_count }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" onclick="checkDevice({{ $device->id }}, '{{ $device->name }}')" title="Test">
                                        <i class="bx bx-signal-5"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="editDevice({{ $device->id }}, '{{ $device->name }}', '{{ $device->serial_number }}', '{{ $device->ip_address ?? '' }}')" title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDevice({{ $device->id }}, '{{ $device->name }}')" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4"><i class="bx bx-info-circle me-1"></i> No ZKTeco biometric machines registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($devices->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $devices->withQueryString()->links() }}
    </div>
    @endif
</div>

@if($tenant->canAddDevice())
<div class="modal fade" id="modalAddDevice" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('subscriber.devices.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-chip text-primary me-2"></i> Register New Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 font-size-13 mb-3">
                    Quota Remaining: <strong>{{ $tenant->max_devices - $devices->total() }} slots</strong> on <strong>{{ $tenant->plan->name ?? 'Plan' }}</strong>.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Machine Name / Location</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Front Gate Terminal">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Serial Number (SN)</label>
                    <input type="text" name="serial_number" class="form-control" required placeholder="e.g. ZKT99221100">
                    <small class="text-muted">Located on the sticker at the back of your ZKTeco device.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">IP Address (Optional)</label>
                    <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.1.150">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success px-3"><i class="bx bx-check me-1"></i> Register</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="modal fade" id="modalEditDevice" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="editDeviceForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-edit text-primary me-2"></i> Edit Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Machine Name</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Serial Number</label>
                    <input type="text" name="serial_number" id="editSerial" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">IP Address</label>
                    <input type="text" name="ip_address" id="editIp" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-3"><i class="bx bx-check me-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDeviceStatus" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-chip text-primary me-2"></i> <span id="statusDeviceName">Device</span> — Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Checking device...</p>
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

function editDevice(id, name, serial, ip) {
    document.getElementById('editName').value = name;
    document.getElementById('editSerial').value = serial;
    document.getElementById('editIp').value = ip;
    document.getElementById('editDeviceForm').action = '{{ url('subscriber/devices') }}/' + id;
    new bootstrap.Modal(document.getElementById('modalEditDevice')).show();
}

function deleteDevice(id, name) {
    if (!confirm('Delete machine "' + name + '" permanently?')) return;
    fetch('{{ url('subscriber/devices') }}/' + id, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' }
    })
    .then(r => { if (r.redirected) { location.href = r.url; return; } return r.json(); })
    .then(data => { if (data && data.redirect) location.href = data.redirect; else location.reload(); })
    .catch(() => location.reload());
}

function checkDevice(id, name) {
    currentDeviceId = id;
    document.getElementById('statusDeviceName').textContent = name;
    document.getElementById('statusBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Checking...</p></div>';
    new bootstrap.Modal(document.getElementById('modalDeviceStatus')).show();
    refreshStatus();
}

function refreshStatus() {
    if (!currentDeviceId) return;
    document.getElementById('btnRefreshStatus').disabled = true;
    fetch('{{ url('subscriber/devices') }}/' + currentDeviceId + '/status')
        .then(r => r.json())
        .then(d => {
            const onlineBadge = d.online
                ? '<span class="badge bg-success px-2 py-1"><i class="bx bx-check-circle me-1"></i> ONLINE</span>'
                : '<span class="badge bg-secondary px-2 py-1"><i class="bx bx-x-circle me-1"></i> OFFLINE</span>';
            const heartbeatHtml = d.last_heartbeat ? '<span title="' + d.last_heartbeat + '">' + d.last_heartbeat_humans + '</span>' : '<span class="text-danger">Never</span>';

            document.getElementById('statusBody').innerHTML = `
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <h6 class="fw-bold font-size-11 mb-2">Device Identity</h6>
                            <div class="font-size-12">
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Status</span>${onlineBadge}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Serial</span><code>${d.serial_number}</code></div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">IP</span>${d.ip_address || 'N/A'}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Port</span>${d.port}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Firmware</span>${d.firmware_version}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <h6 class="fw-bold font-size-11 mb-2">Communication</h6>
                            <div class="font-size-12">
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Heartbeat</span>${heartbeatHtml}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Timezone</span>${d.timezone}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Realtime</span>${d.realtime ? '<span class="text-success">On</span>' : '<span class="text-warning">Off</span>'}</div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Delay</span>${d.delay}s</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <div class="row text-center">
                                <div class="col-4"><span class="fw-bold font-size-16 text-success">${d.user_count}</span><br><span class="font-size-10 text-muted">Users</span></div>
                                <div class="col-4"><span class="fw-bold font-size-16 text-primary">${d.att_count}</span><br><span class="font-size-10 text-muted">Punches</span></div>
                                <div class="col-4">${d.online ? '<span class="fw-bold font-size-16 text-success"><i class="bx bx-check-circle"></i></span>' : '<span class="fw-bold font-size-16 text-danger"><i class="bx bx-x-circle"></i></span>'}<br><span class="font-size-10 text-muted">${d.online ? 'OK' : 'Offline'}</span></div>
                            </div>
                        </div>
                    </div>
                </div>`;
        })
        .catch(e => { document.getElementById('statusBody').innerHTML = '<div class="text-center py-4 text-danger"><i class="bx bx-error-circle font-size-36"></i><p class="mt-2">Failed: ' + e.message + '</p></div>'; })
        .finally(() => { document.getElementById('btnRefreshStatus').disabled = false; });
}
</script>
@endpush
@endsection
