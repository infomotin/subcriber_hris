@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="page-title-box">
    <h4>Dashboard Overview</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm">
            <i class="bx bx-calendar-check me-1"></i> View All Logs
        </a>
    </div>
</div>

<!-- Stat Metric Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Total Devices</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $totalDevices }}</h3>
                </div>
                <div class="stat-icon bg-soft-primary text-primary" style="background: rgba(85, 110, 230, 0.1);">
                    <i class="bx bx-devices"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Online Devices</span>
                    <h3 class="mt-2 mb-0 fw-bold text-success">{{ $onlineDevices }}</h3>
                </div>
                <div class="stat-icon text-success" style="background: rgba(52, 195, 143, 0.1);">
                    <i class="bx bx-wifi"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Today's Punches</span>
                    <h3 class="mt-2 mb-0 fw-bold text-info">{{ $todayPunches }}</h3>
                </div>
                <div class="stat-icon text-info" style="background: rgba(80, 165, 241, 0.1);">
                    <i class="bx bx-fingerprint"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Registered Users</span>
                    <h3 class="mt-2 mb-0 fw-bold text-warning">{{ $totalUsers }}</h3>
                </div>
                <div class="stat-icon text-warning" style="background: rgba(241, 180, 76, 0.1);">
                    <i class="bx bx-user-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Feed Row -->
<div class="row mt-3">
    <!-- 7 Day Attendance Activity Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-line-chart me-1 text-primary"></i> Attendance Activity (Last 7 Days)</span>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Devices List -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-chip me-1 text-primary"></i> Connected Devices</span>
                <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-link text-decoration-none">Manage</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($devices->take(5) as $device)
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                            <div class="d-flex align-items-center">
                                <span class="status-indicator {{ $device->isOnline() ? 'status-online' : 'status-offline' }}"></span>
                                <div>
                                    <h6 class="mb-0 font-size-14">{{ $device->name ?? $device->serial_number }}</h6>
                                    <small class="text-muted">SN: {{ $device->serial_number }} | IP: {{ $device->ip_address ?? 'N/A' }}</small>
                                </div>
                            </div>
                            <span class="badge {{ $device->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                {{ $device->isOnline() ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bx bx-info-circle font-size-24 mb-2"></i>
                            <p class="mb-0">No biometric devices connected yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Punch Logs Stream -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-time me-1 text-primary"></i> Live Attendance Logs Feed</span>
                <a href="{{ route('admin.attendance.export') }}" class="btn btn-sm btn-outline-success">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>User PIN</th>
                                <th>User Name</th>
                                <th>Device</th>
                                <th>Punched Time</th>
                                <th>Status</th>
                                <th>Verify Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                                    <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $log->device->serial_number ?? 'N/A' }}</span></td>
                                    <td>{{ $log->punched_at->format('M d, Y h:i:s A') }}</td>
                                    <td>
                                        <span class="badge bg-soft-info text-info">
                                            {{ $log->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-secondary text-secondary">
                                            <i class="bx bx-shield-check me-1"></i> {{ $log->verify_type_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No recent attendance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($last7Days->keys()) !!},
            datasets: [{
                label: 'Attendance Punches',
                data: {!! json_encode($last7Days->values()) !!},
                borderColor: '#556ee6',
                backgroundColor: 'rgba(85, 110, 230, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f8f8fb' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
