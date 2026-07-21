@extends('layouts.system_admin')

@section('title', 'System Health & Metrics')

@section('content')
<h3 class="fw-bold mb-4 text-white">System Architecture & Health Audit</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <span class="text-muted font-size-12 text-uppercase">Total Tenants</span>
            <h2 class="fw-bold text-primary mb-0 mt-2">{{ $totalTenants }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <span class="text-muted font-size-12 text-uppercase">Active Subscriptions</span>
            <h2 class="fw-bold text-success mb-0 mt-2">{{ $activeTenants }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <span class="text-muted font-size-12 text-uppercase">DB Ping Response</span>
            <h2 class="fw-bold text-info mb-0 mt-2">{{ $metrics['db_response_time_ms'] }} ms</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <span class="text-muted font-size-12 text-uppercase">Memory Usage</span>
            <h2 class="fw-bold text-warning mb-0 mt-2">{{ $metrics['memory_usage_mb'] }} MB</h2>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-transparent border-bottom">
        <span class="fw-bold text-white"><i class="bx bx-server me-2"></i> Application & Infrastructure Details</span>
    </div>
    <div class="card-body">
        <table class="table table-borderless table-sm">
            <tr><th width="30%">PHP Engine Version:</th><td><code>{{ $metrics['php_version'] }}</code></td></tr>
            <tr><th>Laravel Core Framework:</th><td><code>{{ $metrics['laravel_version'] }}</code></td></tr>
            <tr><th>Database Engine Health:</th><td><span class="badge bg-success">{{ $metrics['db_status'] }}</span></td></tr>
            <tr><th>Peak Memory Allocation:</th><td><code>{{ $metrics['peak_memory_mb'] }} MB</code></td></tr>
            <tr><th>ZKTeco Network Machines:</th><td><code>{{ $metrics['online_devices'] }} Online / {{ $metrics['total_devices'] }} Total</code></td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-transparent border-bottom">
        <span class="fw-bold text-white"><i class="bx bx-list-ul me-2"></i> System Activity Logs</span>
    </div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Level</th>
                    <th>Log Message</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSystemLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, H:i:s') }}</td>
                        <td><span class="badge bg-info">{{ strtoupper($log->level) }}</span></td>
                        <td>{{ $log->message }}</td>
                        <td><code>{{ $log->ip_address ?? '127.0.0.1' }}</code></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No system log entries recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
