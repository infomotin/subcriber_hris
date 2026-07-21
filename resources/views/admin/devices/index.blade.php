@extends('layouts.app')

@section('title', 'Biometric Devices')

@section('content')
<div class="page-title-box">
    <h4>Biometric Devices</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.devices.create') }}" class="btn btn-primary rounded-pill btn-sm shadow-sm">
            <i class="bx bx-plus me-1"></i> Register New Device
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Device Name</th>
                        <th>Serial Number (SN)</th>
                        <th>IP Address</th>
                        <th>Users</th>
                        <th>Punches</th>
                        <th>Last Heartbeat</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        <tr>
                            <td>
                                <span class="status-indicator {{ $device->isOnline() ? 'status-online' : 'status-offline' }}"></span>
                                <span class="badge {{ $device->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                    {{ $device->isOnline() ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.devices.show', $device) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $device->name ?? 'Device ' . $device->serial_number }}
                                </a>
                            </td>
                            <td><code>{{ $device->serial_number }}</code></td>
                            <td>{{ $device->ip_address ?? 'Dynamic / Cloud' }}</td>
                            <td><span class="badge bg-soft-info text-info"><i class="bx bx-user me-1"></i> {{ $device->users_count }}</span></td>
                            <td><span class="badge bg-soft-primary text-primary"><i class="bx bx-fingerprint me-1"></i> {{ $device->attendance_logs_count }}</span></td>
                            <td>{{ $device->last_heartbeat ? $device->last_heartbeat->diffForHumans() : 'Never' }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.devices.show', $device) }}"><i class="bx bx-show me-2 text-primary"></i> View Details</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.devices.edit', $device) }}"><i class="bx bx-edit me-2 text-info"></i> Edit Settings</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.devices.reboot', $device) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item"><i class="bx bx-reset me-2 text-warning"></i> Queue Reboot</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.devices.clear-logs', $device) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear device logs?')">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-2"></i> Clear Device Logs</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bx bx-devices font-size-36 text-secondary d-block mb-2"></i>
                                No ZKTeco devices registered yet. Devices will auto-register on first ADMS handshake.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($devices->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $devices->links() }}
        </div>
    @endif
</div>
@endsection
