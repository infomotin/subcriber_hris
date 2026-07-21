@extends('layouts.system_admin')

@section('title', 'System & Server Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-line-chart text-warning me-2 font-size-22"></i> System & Server Monitoring Hub</h4>
        <p class="text-muted font-size-13 mb-0">Realtime application health audits, hardware diagnostics, category-based logs, and network traffic monitor.</p>
    </div>
</div>

<!-- Health & Hardware Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">CPU Load</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['cpu_load'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-primary rounded p-2">
                        <i class="bx bx-chip text-primary font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Memory Usage</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['memory_usage'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-success rounded p-2">
                        <i class="bx bx-hdd text-success font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Disk Space Free</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['disk_free'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-warning rounded p-2">
                        <i class="bx bx-pie-chart-alt-2 text-warning font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">PHP Engine</p>
                        <h4 class="fw-bold text-dark mb-0">v{{ $metrics['php_version'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-info rounded p-2">
                        <i class="bx bxl-php text-info font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Health Check Audit Banner -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-pulse text-danger me-2"></i> System Health Audit Checks</h5>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            @foreach($healthCheck as $checkName => $info)
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-capitalize text-dark mb-1">{{ str_replace('_', ' ', $checkName) }}</h6>
                            <small class="text-muted d-block">{{ $info['message'] }}</small>
                        </div>
                        <span class="badge {{ $info['status'] === 'ok' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($info['status']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Category-Based Logs Filter & List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-receipt text-primary me-2"></i> System Activity Logs by Category</h5>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'all']) }}" class="btn {{ $category === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All Logs</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'info']) }}" class="btn {{ $category === 'info' ? 'btn-info text-white' : 'btn-outline-secondary' }}">System / Info</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'warning']) }}" class="btn {{ $category === 'warning' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Warnings</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'error']) }}" class="btn {{ $category === 'error' ? 'btn-danger' : 'btn-outline-secondary' }}">Errors</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Category Level</th>
                        <th>Log Message</th>
                        <th>Ip / User</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>#{{ $log->id }}</td>
                            <td>
                                <span class="badge {{ $log->level === 'error' ? 'bg-danger' : ($log->level === 'warning' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ strtoupper($log->level) }}
                                </span>
                            </td>
                            <td><span class="font-monospace text-dark">{{ $log->message }}</span></td>
                            <td><small class="text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</small></td>
                            <td><small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No system activity logs recorded for this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
