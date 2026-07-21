@extends('layouts.system_admin')

@section('title', 'System Administrator Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-shield-quarter text-warning me-2 font-size-22"></i> System Administrator Control Center</h4>
        <p class="text-muted font-size-13 mb-0">Complete observation of SaaS subscribers, server monitoring, role permissions, databases, and gateway infrastructure.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Total Subscribers</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalTenants }}</h3>
                    </div>
                    <div class="avatar-sm bg-soft-warning rounded p-2">
                        <i class="bx bx-building text-warning font-size-24"></i>
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
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Active Accounts</p>
                        <h3 class="fw-bold text-success mb-0">{{ $activeTenants }}</h3>
                    </div>
                    <div class="avatar-sm bg-soft-success rounded p-2">
                        <i class="bx bx-check-circle text-success font-size-24"></i>
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
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Total Revenue</p>
                        <h3 class="fw-bold text-primary mb-0">৳{{ number_format($totalRevenue) }}</h3>
                    </div>
                    <div class="avatar-sm bg-soft-primary rounded p-2">
                        <i class="bx bx-money text-primary font-size-24"></i>
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
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Server CPU Load</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $metrics['cpu_load'] }}</h3>
                    </div>
                    <div class="avatar-sm bg-soft-info rounded p-2">
                        <i class="bx bx-chip text-info font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Quick Links Matrix -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.system.users.index') }}" class="card border-0 shadow-sm text-decoration-none hover-card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar-sm bg-warning text-dark rounded d-flex align-items-center justify-content-center font-size-20">
                    <i class="bx bx-user-voice"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">User Manager</h6>
                    <small class="text-muted">Manage SaaS Accounts</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.system.roles.index') }}" class="card border-0 shadow-sm text-decoration-none hover-card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar-sm bg-primary text-white rounded d-flex align-items-center justify-content-center font-size-20">
                    <i class="bx bx-key"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Role & Permissions</h6>
                    <small class="text-muted">Assign Menu Access</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.system.monitoring.index') }}" class="card border-0 shadow-sm text-decoration-none hover-card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar-sm bg-info text-white rounded d-flex align-items-center justify-content-center font-size-20">
                    <i class="bx bx-line-chart"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">System Monitoring</h6>
                    <small class="text-muted">Logs & Realtime Traffic</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.system.gateways.index') }}" class="card border-0 shadow-sm text-decoration-none hover-card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar-sm bg-success text-white rounded d-flex align-items-center justify-content-center font-size-20">
                    <i class="bx bx-cog"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Gateways Config</h6>
                    <small class="text-muted">SMS, Mail & SSLCommerz</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Logs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-primary me-2"></i> Recent System Observations</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSystemLogs as $sLog)
                        <tr>
                            <td>#{{ $sLog->id }}</td>
                            <td>
                                <span class="badge {{ $sLog->level === 'error' ? 'bg-danger' : ($sLog->level === 'warning' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ strtoupper($sLog->level) }}
                                </span>
                            </td>
                            <td><span class="font-monospace text-dark">{{ $sLog->message }}</span></td>
                            <td><small class="text-muted">{{ $sLog->ip_address ?? '127.0.0.1' }}</small></td>
                            <td><small class="text-muted">{{ $sLog->created_at->format('M d, Y H:i:s') }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No system observation logs recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
