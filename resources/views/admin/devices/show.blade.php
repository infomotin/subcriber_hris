@extends('layouts.app')

@section('title', 'Device Details - ' . $device->serial_number)

@section('content')
<div class="page-title-box">
    <h4>Device: {{ $device->name ?? $device->serial_number }}</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary btn-sm rounded-pill">
            <i class="bx bx-arrow-back me-1"></i> Back to Devices
        </a>
    </div>
</div>

<div class="row">
    <!-- Device Specs & Control Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Device Information</span>
                <span class="badge {{ $device->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                    {{ $device->isOnline() ? 'Online' : 'Offline' }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th class="text-muted" width="40%">Serial Number:</th>
                        <td><code>{{ $device->serial_number }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">IP Address:</th>
                        <td>{{ $device->ip_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Push Version:</th>
                        <td>{{ $device->push_version ?? 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Firmware:</th>
                        <td>{{ $device->firmware_version ?? 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Users Count:</th>
                        <td>{{ $device->user_count }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Fingerprints:</th>
                        <td>{{ $device->fp_count }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Faces:</th>
                        <td>{{ $device->face_count }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Total Logs:</th>
                        <td>{{ $device->att_count }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Last Heartbeat:</th>
                        <td>{{ $device->last_heartbeat ? $device->last_heartbeat->format('Y-m-d H:i:s') : 'Never' }}</td>
                    </tr>
                </table>

                <hr>

                <h6 class="fw-bold mb-3">Remote Actions Queue</h6>
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.devices.reboot', $device) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100"><i class="bx bx-reset me-1"></i> Trigger Remote Reboot</button>
                    </form>
                    <form action="{{ route('admin.devices.query-info', $device) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-info w-100"><i class="bx bx-info-circle me-1"></i> Query Device Info</button>
                    </form>
                    <form action="{{ route('admin.devices.clear-logs', $device) }}" method="POST" onsubmit="return confirm('Clear all logs on physical device?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100"><i class="bx bx-trash me-1"></i> Clear Remote Logs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs & Pending Commands -->
    <div class="col-lg-8">
        <!-- Recent Logs -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bx bx-time me-1 text-primary"></i> Recent Attendance Logs
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>User PIN</th>
                                <th>Punched At</th>
                                <th>Status</th>
                                <th>Verify Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                                    <td>{{ $log->punched_at->format('Y-m-d H:i:s') }}</td>
                                    <td><span class="badge bg-soft-info text-info">{{ $log->status_label }}</span></td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $log->verify_type_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No logs recorded for this device yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pending Commands Queue -->
        <div class="card">
            <div class="card-header">
                <i class="bx bx-terminal me-1 text-primary"></i> Device Command Audit Queue
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>CMD ID</th>
                                <th>Command String</th>
                                <th>Status</th>
                                <th>Executed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($device->commands as $cmd)
                                <tr>
                                    <td><code>#{{ $cmd->id }}</code></td>
                                    <td><code>{{ $cmd->command }}</code></td>
                                    <td>
                                        <span class="badge {{ $cmd->status === 'executed' ? 'bg-soft-success text-success' : ($cmd->status === 'pending' ? 'bg-soft-warning text-warning' : 'bg-soft-danger text-danger') }}">
                                            {{ strtoupper($cmd->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $cmd->executed_at ? $cmd->executed_at->diffForHumans() : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No commands queued for this device.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
