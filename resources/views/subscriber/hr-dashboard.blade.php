@extends('layouts.subscriber')

@section('title', 'HR Dashboard')

@section('content')
<style>
    .dash-stat {
        border-radius: 16px;
        border: none;
        background: #fff;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .dash-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .dash-stat .stat-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    .dash-stat .stat-value { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.6rem; line-height: 1.1; }
    .dash-stat .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; color: #94a3b8; }
    .dash-stat .stat-sub { font-size: 0.72rem; color: #64748b; }
    .dash-stat::after {
        content: ''; position: absolute; top: 0; right: 0; width: 80px; height: 80px;
        border-radius: 0 16px 0 80px; opacity: 0.04;
    }
    .dash-stat.violet::after { background: #7c3aed; }
    .dash-stat.emerald::after { background: #10b981; }
    .dash-stat.amber::after { background: #f59e0b; }
    .dash-stat.sky::after { background: #0ea5e9; }
    .dash-stat.rose::after { background: #f43f5e; }
    .dash-stat.indigo::after { background: #6366f1; }
    .activity-item {
        padding: 12px 16px; border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #f8fafc; }
    .quick-link {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; border-radius: 12px;
        text-decoration: none; color: #334155;
        transition: all 0.15s; border: 1px solid #f1f5f9;
        background: #fff;
    }
    .quick-link:hover { background: #f0f0ff; border-color: #c7d2fe; color: #4f46e5; transform: translateY(-1px); }
    .quick-link i { font-size: 1.2rem; }
    .dept-row { padding: 10px 0; border-bottom: 1px solid #f8fafc; }
    .dept-row:last-child { border-bottom: none; }
    .section-card {
        border: none; border-radius: 16px;
        background: #fff; overflow: hidden;
    }
    .section-card .card-header {
        background: #fff; border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
    }
    .badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .badge-dot.green { background: #10b981; }
    .badge-dot.yellow { background: #f59e0b; }
    .badge-dot.red { background: #f43f5e; }
    .progress-soft { height: 6px; border-radius: 10px; background: #e2e8f0; }
    .progress-soft .bar { height: 100%; border-radius: 10px; transition: width 0.6s ease; }
</style>

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Corporate HR & Payroll</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;" class="mb-0">
            <i class="bx bx-grid-alt text-primary me-1.5 align-middle font-size-26"></i>Dashboard
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.employees.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bx bx-plus me-1"></i> Add Employee
        </a>
        <a href="{{ route('subscriber.hris.general.show', 'verification') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bx bx-shield-quarter me-1"></i> Verify Data
        </a>
    </div>
</div>

{{-- Stat Cards Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.employees.index') }}" class="text-decoration-none">
            <div class="card dash-stat violet p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Employees</div>
                        <div class="stat-value text-slate-800 mt-1">{{ $totalEmployees }}</div>
                        <div class="stat-sub mt-1"><span class="text-success fw-semibold">{{ $activeEmployees }}</span> active · <span class="text-muted">{{ $inactiveEmployees }} inactive</span></div>
                    </div>
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="bx bx-group"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.attendance.index') }}" class="text-decoration-none">
            <div class="card dash-stat emerald p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Today's Attendance</div>
                        <div class="stat-value text-slate-800 mt-1">{{ $todayLogs }}</div>
                        <div class="stat-sub mt-1"><span class="text-success fw-semibold">{{ $todayCheckIns }} check-in</span> · <span class="text-danger">{{ $todayCheckOuts }} check-out</span></div>
                    </div>
                    <div class="stat-icon" style="background:#d1fae5;color:#059669;"><i class="bx bx-fingerprint"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.payroll.database') }}" class="text-decoration-none">
            <div class="card dash-stat amber p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Payroll (Gross)</div>
                        <div class="stat-value text-slate-800 mt-1">{{ number_format($totalPayroll, 0) }}</div>
                        <div class="stat-sub mt-1">Avg: <span class="fw-semibold">{{ number_format($avgSalary, 0) }}</span> BDT</div>
                    </div>
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="bx bx-money"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.general.show', 'verification') }}" class="text-decoration-none">
            <div class="card dash-stat sky p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Verification Progress</div>
                        <div class="stat-value text-slate-800 mt-1">{{ $verificationStats['verified'] }}<span class="font-size-14 text-muted">/{{ $verificationStats['total'] }}</span></div>
                        <div class="stat-sub mt-1">
                            <span class="text-success fw-semibold">{{ $verificationStats['verified'] }} verified</span> ·
                            <span class="text-warning">{{ $verificationStats['pending'] }} pending</span>
                        </div>
                    </div>
                    <div class="stat-icon" style="background:#e0f2fe;color:#0284c7;"><i class="bx bx-shield-quarter"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Stat Cards Row 2: Pending Actions --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.leaves.index') }}" class="text-decoration-none">
            <div class="card dash-stat rose p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Pending Leaves</div>
                        <div class="stat-value mt-1" style="color:#f43f5e;">{{ $pendingLeaves }}</div>
                        <div class="stat-sub mt-1">{{ $pendingLeaveAmount }} days total</div>
                    </div>
                    <div class="stat-icon" style="background:#ffe4e6;color:#e11d48;"><i class="bx bx-calendar-x"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.bills.index') }}" class="text-decoration-none">
            <div class="card dash-stat rose p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Pending Bills</div>
                        <div class="stat-value mt-1" style="color:#f43f5e;">{{ $pendingBills }}</div>
                        <div class="stat-sub mt-1">{{ number_format($pendingBillAmount, 0) }} BDT</div>
                    </div>
                    <div class="stat-icon" style="background:#ffe4e6;color:#e11d48;"><i class="bx bx-receipt"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.advances.index') }}" class="text-decoration-none">
            <div class="card dash-stat indigo p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Pending Advances</div>
                        <div class="stat-value mt-1" style="color:#6366f1;">{{ $pendingAdvances }}</div>
                        <div class="stat-sub mt-1">{{ number_format($pendingAdvanceAmount, 0) }} BDT</div>
                    </div>
                    <div class="stat-icon" style="background:#e0e7ff;color:#4f46e5;"><i class="bx bx-credit-card"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subscriber.hris.movement-passes.index') }}" class="text-decoration-none">
            <div class="card dash-stat p-3" style="border-left: 3px solid #8b5cf6;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Pending Movements</div>
                        <div class="stat-value mt-1" style="color:#8b5cf6;">{{ $pendingMovements }}</div>
                        <div class="stat-sub mt-1">Awaiting approval</div>
                    </div>
                    <div class="stat-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="bx bx-transfer"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="row g-4 mb-4">
    {{-- Department Breakdown --}}
    <div class="col-lg-5">
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bx bx-building-house text-primary me-2"></i>Departments</h6>
                <a href="{{ route('subscriber.hris.departments.index') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">View All <i class="bx bx-right-arrow-alt"></i></a>
            </div>
            <div class="card-body p-3">
                @forelse($departments as $dept)
                    @php $pct = $totalEmployees > 0 ? round($dept->employees_count / max($totalEmployees, 1) * 100) : 0; @endphp
                    <a href="{{ route('subscriber.hris.employees.index', ['search' => $dept->name]) }}" class="text-decoration-none">
                        <div class="dept-row d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center font-size-14" style="width:34px;height:34px;background:{{ ['#ede9fe','#dbeafe','#d1fae5','#fef3c7','#ffe4e6','#e0f2fe'][$loop->index % 6] }};color:{{ ['#7c3aed','#2563eb','#059669','#d97706','#e11d48','#0284c7'][$loop->index % 6] }};">
                                    <i class="bx bx-building"></i>
                                </div>
                                <div>
                                    <span class="font-size-13 fw-semibold text-slate-700 d-block">{{ $dept->name }}</span>
                                    <span class="font-size-11 text-muted">{{ $dept->employees_count }} employees</span>
                                </div>
                            </div>
                            <div class="text-end" style="width:100px;">
                                <div class="d-flex justify-content-between font-size-10 mb-1">
                                    <span class="text-muted">{{ $pct }}%</span>
                                </div>
                                <div class="progress-soft">
                                    <div class="bar" style="width:{{ $pct }}%;background:{{ ['#7c3aed','#2563eb','#059669','#d97706','#e11d48','#0284c7'][$loop->index % 6] }};"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted text-center py-4 font-size-13 mb-0">No departments found.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Leaves --}}
    <div class="col-lg-7">
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bx bx-calendar text-warning me-2"></i>Recent Leave Applications</h6>
                <a href="{{ route('subscriber.hris.leaves.index') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">View All <i class="bx bx-right-arrow-alt"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaves as $leave)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($leave->employee->user->name ?? 'U') }}&background=7c3aed&color=fff&size=28" class="rounded-circle" width="28" height="28">
                                        <div>
                                            <span class="font-size-12 fw-semibold text-slate-700 d-block">{{ $leave->employee->user->name ?? 'N/A' }}</span>
                                            <code class="font-size-10 text-muted">{{ $leave->employee->employee_id ?? '' }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-size-12">{{ $leave->leaveType->name ?? 'N/A' }}</td>
                                <td class="font-size-12 fw-semibold">{{ $leave->total_days }}</td>
                                <td class="font-size-11 text-muted">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d') }}</td>
                                <td>
                                    @if($leave->status === 'pending')
                                        <span class="badge bg-warning text-dark font-size-10">Pending</span>
                                    @elseif($leave->status === 'approved')
                                        <span class="badge bg-success font-size-10">Approved</span>
                                    @else
                                        <span class="badge bg-danger font-size-10">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('subscriber.hris.leaves.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 font-size-10">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4 font-size-13">No recent leaves.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Second Row --}}
<div class="row g-4 mb-4">
    {{-- Recent Employees --}}
    <div class="col-lg-6">
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bx bx-user-plus text-success me-2"></i>Recently Added Employees</h6>
                <a href="{{ route('subscriber.hris.employees.index') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">View All <i class="bx bx-right-arrow-alt"></i></a>
            </div>
            <div class="card-body p-0">
                @forelse($recentEmployees as $emp)
                <a href="{{ route('subscriber.hris.employees.show', $emp->id) }}" class="text-decoration-none">
                    <div class="activity-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'U') }}&background=10b981&color=fff&size=36" class="rounded-circle" width="36" height="36">
                            <div>
                                <span class="font-size-13 fw-semibold text-slate-700 d-block">{{ $emp->user->name ?? 'N/A' }}</span>
                                <span class="font-size-11 text-muted">{{ $emp->employee_id }} · {{ $emp->department->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="font-size-11 text-muted d-block">{{ $emp->designation->title ?? 'N/A' }}</span>
                            <span class="font-size-10 text-muted">{{ $emp->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4 font-size-13">No employees found.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Bills --}}
    <div class="col-lg-6">
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bx bx-receipt text-danger me-2"></i>Recent Bill Applications</h6>
                <a href="{{ route('subscriber.hris.bills.index') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">View All <i class="bx bx-right-arrow-alt"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBills as $bill)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($bill->employee->user->name ?? 'U') }}&background=f43f5e&color=fff&size=28" class="rounded-circle" width="28" height="28">
                                        <div>
                                            <span class="font-size-12 fw-semibold text-slate-700 d-block">{{ $bill->employee->user->name ?? 'N/A' }}</span>
                                            <code class="font-size-10 text-muted">{{ $bill->employee->employee_id ?? '' }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-size-12">{{ $bill->billType->name ?? 'N/A' }}</td>
                                <td class="font-size-12 fw-semibold">{{ number_format($bill->amount, 0) }} BDT</td>
                                <td>
                                    @if($bill->status === 'pending')
                                        <span class="badge bg-warning text-dark font-size-10">Pending</span>
                                    @elseif($bill->status === 'approved')
                                        <span class="badge bg-success font-size-10">Approved</span>
                                    @else
                                        <span class="badge bg-danger font-size-10">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('subscriber.hris.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 font-size-10">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4 font-size-13">No recent bills.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Third Row --}}
<div class="row g-4 mb-4">
    {{-- Recent Advances --}}
    <div class="col-lg-6">
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bx bx-credit-card text-info me-2"></i>Recent Salary Advances</h6>
                <a href="{{ route('subscriber.hris.advances.index') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">View All <i class="bx bx-right-arrow-alt"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Installments</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAdvances as $adv)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($adv->employee->user->name ?? 'U') }}&background=0ea5e9&color=fff&size=28" class="rounded-circle" width="28" height="28">
                                        <div>
                                            <span class="font-size-12 fw-semibold text-slate-700 d-block">{{ $adv->employee->user->name ?? 'N/A' }}</span>
                                            <code class="font-size-10 text-muted">{{ $adv->employee->employee_id ?? '' }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-size-12">{{ $adv->advanceType->name ?? 'N/A' }}</td>
                                <td class="font-size-12 fw-semibold">{{ number_format($adv->amount, 0) }} BDT</td>
                                <td class="font-size-12">{{ $adv->installments }} months</td>
                                <td>
                                    @if($adv->status === 'pending')
                                        <span class="badge bg-warning text-dark font-size-10">Pending</span>
                                    @elseif($adv->status === 'approved')
                                        <span class="badge bg-success font-size-10">Approved</span>
                                    @else
                                        <span class="badge bg-danger font-size-10">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4 font-size-13">No recent advances.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions + Device Status --}}
    <div class="col-lg-6">
        <div class="row g-3">
            {{-- Quick Actions --}}
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0"><i class="bx bx-bolt text-primary me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.leaves.apply') }}" class="quick-link">
                                    <i class="bx bx-calendar-plus text-warning"></i>
                                    <span class="font-size-12 fw-semibold">Apply Leave</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.bills.apply') }}" class="quick-link">
                                    <i class="bx bx-receipt text-danger"></i>
                                    <span class="font-size-12 fw-semibold">Submit Bill</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.advances.apply') }}" class="quick-link">
                                    <i class="bx bx-dollar text-info"></i>
                                    <span class="font-size-12 fw-semibold">Request Advance</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.movement-passes.apply') }}" class="quick-link">
                                    <i class="bx bx-transfer text-primary"></i>
                                    <span class="font-size-12 fw-semibold">Movement Pass</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.employees.create') }}" class="quick-link">
                                    <i class="bx bx-user-plus text-success"></i>
                                    <span class="font-size-12 fw-semibold">Add Employee</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('subscriber.hris.users.create') }}" class="quick-link">
                                    <i class="bx bx-user text-indigo" style="color:#6366f1;"></i>
                                    <span class="font-size-12 fw-semibold">Create User</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Device & Biometric Status --}}
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="bx bx-chip text-primary me-2"></i>Device & Biometric</h6>
                        <a href="{{ route('subscriber.adms.overview') }}" class="font-size-11 text-primary text-decoration-none fw-semibold">Details <i class="bx bx-right-arrow-alt"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#ede9fe;">
                                    <div class="font-size-11 text-muted fw-semibold">Devices</div>
                                    <div class="fw-bold font-size-18" style="color:#7c3aed;">{{ $devicesCount }}</div>
                                    <div class="font-size-10"><span class="badge-dot green"></span> {{ $onlineDevicesCount }} online</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#fef3c7;">
                                    <div class="font-size-11 text-muted fw-semibold">Today Punches</div>
                                    <div class="fw-bold font-size-18" style="color:#d97706;">{{ $todayPunches }}</div>
                                    <div class="font-size-10 text-muted">biometric logs</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#d1fae5;">
                                    <div class="font-size-11 text-muted fw-semibold">ZKTeco Users</div>
                                    <div class="fw-bold font-size-18" style="color:#059669;">{{ $usersCount }}</div>
                                    <div class="font-size-10 text-muted">synced users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gender Distribution --}}
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0"><i class="bx bx-male-female me-2" style="color:#ec4899;"></i>Gender Distribution</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $malePct = $totalEmployees > 0 ? round($maleCount / $totalEmployees * 100) : 0;
                            $femalePct = $totalEmployees > 0 ? round($femaleCount / $totalEmployees * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between font-size-12 mb-1">
                                    <span class="fw-semibold text-slate-700"><i class="bx bx-male text-primary me-1"></i> Male</span>
                                    <span class="text-muted">{{ $maleCount }} ({{ $malePct }}%)</span>
                                </div>
                                <div class="progress-soft">
                                    <div class="bar" style="width:{{ $malePct }}%;background:#6366f1;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between font-size-12 mb-1">
                                    <span class="fw-semibold text-slate-700"><i class="bx bx-female text-pink-500 me-1"></i> Female</span>
                                    <span class="text-muted">{{ $femaleCount }} ({{ $femalePct }}%)</span>
                                </div>
                                <div class="progress-soft">
                                    <div class="bar" style="width:{{ $femalePct }}%;background:#ec4899;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
