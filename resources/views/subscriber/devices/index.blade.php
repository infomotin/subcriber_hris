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
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No ZKTeco biometric machines registered under your tenant yet.</td></tr>
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

@endsection
