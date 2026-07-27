@extends('layouts.subscriber')

@section('title', 'Corporate HR & Payroll Dashboard')

@section('content')
<style>
    .hr-stat-card {
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.95);
        transition: all 0.2s ease;
    }
    .hr-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    }
    .hr-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Corporate HR & Payroll</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Live Data Dashboard</h4>
    </div>
    <div class="page-title-right">
        <a href="{{ route('subscriber.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bx bx-shield-quarter me-1"></i> ADMS Portal
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Total Employees</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $totalEmployees }}</h3>
                    <small class="text-success font-size-11">{{ $activeEmployees }} Active</small>
                </div>
                <div class="hr-stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600">
                    <i class="bx bx-user"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Today's Attendance</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $todayLogs }}</h3>
                    <small class="font-size-11"><span class="text-success">{{ $todayCheckIns }} In</span> / <span class="text-danger">{{ $todayCheckOuts }} Out</span></small>
                </div>
                <div class="hr-stat-icon bg-emerald-50 border border-emerald-100 text-emerald-600">
                    <i class="bx bx-fingerprint"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Payroll (Gross)</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ number_format($totalPayroll, 0) }}</h3>
                    <small class="text-muted font-size-11">Avg: {{ number_format($avgSalary, 0) }}</small>
                </div>
                <div class="hr-stat-icon bg-amber-50 border border-amber-100 text-amber-600">
                    <i class="bx bx-money"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Gender Ratio</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $maleCount }}M / {{ $femaleCount }}F</h3>
                    <small class="text-muted font-size-11">{{ $totalEmployees > 0 ? round($maleCount / max($totalEmployees, 1) * 100) : 0 }}% Male</small>
                </div>
                <div class="hr-stat-icon bg-sky-50 border border-sky-100 text-sky-600">
                    <i class="bx bx-male-female"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Pending Leaves</span>
                    <h3 class="mt-2 mb-0 fw-bold text-warning" style="font-family: 'Poppins', sans-serif;">{{ $pendingLeaves }}</h3>
                    <small class="text-muted font-size-11">{{ $pendingLeaveAmount }} days</small>
                </div>
                <div class="hr-stat-icon bg-orange-50 border border-orange-100 text-orange-600">
                    <i class="bx bx-calendar-x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Pending Bills</span>
                    <h3 class="mt-2 mb-0 fw-bold text-danger" style="font-family: 'Poppins', sans-serif;">{{ $pendingBills }}</h3>
                    <small class="text-muted font-size-11">{{ number_format($pendingBillAmount, 0) }} BDT</small>
                </div>
                <div class="hr-stat-icon bg-red-50 border border-red-100 text-red-600">
                    <i class="bx bx-receipt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Pending Advances</span>
                    <h3 class="mt-2 mb-0 fw-bold text-info" style="font-family: 'Poppins', sans-serif;">{{ $pendingAdvances }}</h3>
                    <small class="text-muted font-size-11">{{ number_format($pendingAdvanceAmount, 0) }} BDT</small>
                </div>
                <div class="hr-stat-icon bg-sky-50 border border-sky-100 text-sky-600">
                    <i class="bx bx-credit-card"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hr-stat-card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Pending Movements</span>
                    <h3 class="mt-2 mb-0 fw-bold text-secondary" style="font-family: 'Poppins', sans-serif;">{{ $pendingMovements }}</h3>
                    <small class="text-muted font-size-11">Awaiting approval</small>
                </div>
                <div class="hr-stat-icon bg-slate-50 border border-slate-100 text-slate-600">
                    <i class="bx bx-transfer"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-git-branch text-primary me-2"></i> Employees by Department</h6>
            </div>
            <div class="card-body p-4">
                @forelse($departments as $dept)
                @php
                    $pct = $totalEmployees > 0 ? round($dept->employees_count / $totalEmployees * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="font-size-13 fw-medium text-slate-700">{{ $dept->name }}</span>
                        <span class="font-size-12 text-muted">{{ $dept->employees_count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px; background: #eef2ff;">
                        <div class="progress-bar bg-primary progress-bar-animated" role="progressbar" style="width: {{ $pct }}%; border-radius: 10px;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted font-size-13 text-center py-4 mb-0">No departments found.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-calendar-x text-warning me-2"></i> Recent Leave Applications</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaves as $leave)
                            <tr>
                                <td class="font-size-13">{{ $leave->employee->employee_id ?? 'N/A' }}</td>
                                <td>{{ $leave->total_days }}</td>
                                <td><span class="badge bg-{{ $leave->status === 'pending' ? 'warning' : ($leave->status === 'approved' ? 'success' : 'danger') }} font-size-11">{{ ucfirst($leave->status) }}</span></td>
                                <td class="font-size-12 text-muted">{{ $leave->created_at->format('M d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4 font-size-13">No recent leaves.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-receipt text-danger me-2"></i> Recent Bill Applications</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBills as $bill)
                            <tr>
                                <td class="font-size-13">{{ $bill->employee->employee_id ?? 'N/A' }}</td>
                                <td class="fw-medium">{{ number_format($bill->amount, 0) }}</td>
                                <td><span class="badge bg-{{ $bill->status === 'pending' ? 'warning' : ($bill->status === 'approved' ? 'success' : 'danger') }} font-size-11">{{ ucfirst($bill->status) }}</span></td>
                                <td class="font-size-12 text-muted">{{ $bill->created_at->format('M d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4 font-size-13">No recent bills.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-chip text-primary me-2"></i> Device & Biometric Overview</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-indigo-50 rounded-3 p-3 text-center border border-indigo-100">
                            <span class="text-muted font-size-12 d-block">Devices</span>
                            <h4 class="fw-bold text-indigo-700 mb-0 mt-1">{{ $devicesCount }}</h4>
                            <small class="text-success font-size-11">{{ $onlineDevicesCount }} Online</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-amber-50 rounded-3 p-3 text-center border border-amber-100">
                            <span class="text-muted font-size-12 d-block">Today's Punches</span>
                            <h4 class="fw-bold text-amber-700 mb-0 mt-1">{{ $todayPunches }}</h4>
                            <small class="text-muted font-size-11">Biometric logs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-emerald-50 rounded-3 p-3 text-center border border-emerald-100">
                            <span class="text-muted font-size-12 d-block">Biometric Users</span>
                            <h4 class="fw-bold text-emerald-700 mb-0 mt-1">{{ $usersCount }}</h4>
                            <small class="text-muted font-size-11">ZKTeco users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-sky-50 rounded-3 p-3 text-center border border-sky-100">
                            <span class="text-muted font-size-12 d-block">Employee Strength</span>
                            <h4 class="fw-bold text-sky-700 mb-0 mt-1">{{ $totalEmployees }}</h4>
                            <small class="text-muted font-size-11">HR records</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection