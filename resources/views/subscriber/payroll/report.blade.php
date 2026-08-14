@extends('layouts.subscriber')

@php
    $tabTitles = [
        'overview' => 'Visual Overview',
        'employee' => 'Employee Report',
        'department' => 'Department Report',
        'punch' => 'Punch Report',
        'leave' => 'Leave Report',
        'timecard' => 'Time Card',
        'salary' => 'Salary Sheet',
        'advance' => 'Advance Report',
    ];
    $currentTitle = $tabTitles[$tab] ?? 'Reports';
@endphp

@section('title', $currentTitle . ' - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .report-tab { padding: 10px 18px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid #e5e7eb; background: #f9fafb; color: #6b7280; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .report-tab:hover { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .report-tab.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .badge-generated { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #e5e7eb; color: #374151; }
    .print-break { page-break-after: always; }
    @media print { .no-print { display: none !important; } .card { border: 1px solid #ddd !important; box-shadow: none !important; } }
</style>

<div class="page-title-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 no-print">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Reports</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">{{ $currentTitle }}</h4>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="{{ route('subscriber.payroll.report') }}" class="d-flex gap-2 align-items-center" id="filterForm">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="department_id" class="form-select form-select-sm" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Depts</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ $departmentId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
            <select name="employee_profile_id" class="form-select form-select-sm" style="width:160px;" onchange="this.form.submit()">
                <option value="">All Employees</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ $employeeId == $e->id ? 'selected' : '' }}>{{ $e->emp_code }} - {{ $e->user?->name }}</option>
                @endforeach
            </select>
            <input type="month" name="month" class="form-control form-control-sm" style="width:150px;" value="{{ $month }}" onchange="this.form.submit()">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> Print</button>
        </form>
    </div>
</div>

@php
    $tabs = [
        'overview' => ['icon' => 'bx-bar-chart-alt-2', 'label' => 'Visual Overview'],
        'employee' => ['icon' => 'bx-user', 'label' => 'Employee Report'],
        'department' => ['icon' => 'bx-building', 'label' => 'Department Report'],
        'punch' => ['icon' => 'bx-data', 'label' => 'Punch Report'],
        'leave' => ['icon' => 'bx-calendar', 'label' => 'Leave Report'],
        'timecard' => ['icon' => 'bx-time', 'label' => 'Time Card'],
        'salary' => ['icon' => 'bx-money', 'label' => 'Salary Sheet'],
        'advance' => ['icon' => 'bx-dollar', 'label' => 'Advance Report'],
    ];
@endphp

{{-- Tab Navigation --}}
<div class="mb-4 d-flex gap-2 flex-wrap no-print">
    @foreach($tabs as $key => $t)
        <a href="{{ route('subscriber.payroll.report', ['tab' => $key, 'month' => $month, 'department_id' => $departmentId, 'employee_profile_id' => $employeeId]) }}"
           class="report-tab {{ $tab === $key ? 'active' : '' }}">
            <i class="bx {{ $t['icon'] }}"></i> {{ $t['label'] }}</a>
    @endforeach
</div>

{{-- ==================== TAB: OVERVIEW ==================== --}}
@if($tab === 'overview')
    @php
        $totalGross = $salaryData->sum('gross_salary');
        $totalNet = $salaryData->sum('net_payable');
        $totalBonus = $salaryData->sum('bonus_amount');
        $totalLate = $salaryData->sum('late_deduction');
        $totalTax = $salaryData->sum('tax_deduction');
        $totalPf = $salaryData->sum('pf_deduction');
        $totalAbsent = $salaryData->sum('absent_deduction');
        $totalOt = $salaryData->sum('ot_payable');
        $totalAdvance = $salaryData->sum('advance_deduction');
        $presentCount = $attendanceData->where('status', 'present')->count();
        $absentCount = $attendanceData->where('status', 'absent')->count();
        $leaveCount = $attendanceData->where('status', 'leave')->count();
        $holidayCount = $attendanceData->where('status', 'holiday')->count();
        $weekendCount = $attendanceData->where('status', 'weekend')->count();
    @endphp

    <div class="card mb-4">
        <div class="card-body p-3 d-flex justify-content-between align-items-center no-print">
            <h6 class="fw-bold mb-0"><i class="bx bx-bar-chart-alt-2 text-primary me-1"></i> Visual Overview — {{ $monthLabel }}</h6>
            @include('subscriber.payroll.exports.export-buttons')
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-size-11 fw-bold">Total Gross</span>
                        <h3 class="mt-2 mb-0 fw-bold">৳{{ number_format($totalGross, 0) }}</h3>
                    </div>
                    <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600"><i class="bx bx-dollar"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-size-11 fw-bold">Total Net Payable</span>
                        <h3 class="mt-2 mb-0 fw-bold text-success">৳{{ number_format($totalNet, 0) }}</h3>
                    </div>
                    <div class="stat-icon bg-green-50 border border-green-100 text-green-600"><i class="bx bx-wallet"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-size-11 fw-bold">Total Bonus</span>
                        <h3 class="mt-2 mb-0 fw-bold text-warning">৳{{ number_format($totalBonus, 0) }}</h3>
                    </div>
                    <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600"><i class="bx bx-gift"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-size-11 fw-bold">Total Deductions</span>
                        <h3 class="mt-2 mb-0 fw-bold text-danger">৳{{ number_format($totalLate + $totalTax + $totalPf + $totalAbsent + $totalAdvance, 0) }}</h3>
                    </div>
                    <div class="stat-icon bg-red-50 border border-red-100 text-red-600"><i class="bx bx-minus-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-pie-chart-alt text-primary me-1"></i> Salary Distribution</h6>
                    <canvas id="salaryPieChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-bar-chart text-primary me-1"></i> Department-wise Net Pay</h6>
                    <canvas id="deptBarChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-chart text-primary me-1"></i> Deduction Breakdown</h6>
                    <canvas id="deductionChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-calendar-check text-primary me-1"></i> Attendance Summary</h6>
                    <canvas id="attendanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-list-check text-primary me-1"></i> Quick Stats</h6>
                    <table class="table table-sm font-size-12 mb-0">
                        <tr><td class="text-muted">Employees Processed</td><td class="fw-bold text-end">{{ $salaryData->count() }}</td></tr>
                        <tr><td class="text-muted">Total Working Days</td><td class="fw-bold text-end">{{ $attendanceData->count() }}</td></tr>
                        <tr><td class="text-muted">Days Present</td><td class="fw-bold text-end text-success">{{ $presentCount }}</td></tr>
                        <tr><td class="text-muted">Days Absent</td><td class="fw-bold text-end text-danger">{{ $absentCount }}</td></tr>
                        <tr><td class="text-muted">Leave Days</td><td class="fw-bold text-end text-warning">{{ $leaveCount }}</td></tr>
                        <tr><td class="text-muted">Holidays</td><td class="fw-bold text-end">{{ $holidayCount }}</td></tr>
                        <tr><td class="text-muted">Weekends</td><td class="fw-bold text-end">{{ $weekendCount }}</td></tr>
                        <tr><td class="text-muted">OT Payable</td><td class="fw-bold text-end">৳{{ number_format($totalOt, 0) }}</td></tr>
                        <tr><td class="text-muted">Avg Net Salary</td><td class="fw-bold text-end">৳{{ number_format($salaryData->avg('net_payable'), 0) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bx bx-line-chart text-primary me-1"></i> Deduction Details</h6>
                    <table class="table table-sm font-size-12 mb-0">
                        <tr><td class="text-muted">Late Deduction</td><td class="fw-bold text-end text-danger">৳{{ number_format($totalLate, 0) }}</td></tr>
                        <tr><td class="text-muted">Absent Deduction</td><td class="fw-bold text-end text-danger">৳{{ number_format($totalAbsent, 0) }}</td></tr>
                        <tr><td class="text-muted">Tax Deduction</td><td class="fw-bold text-end text-danger">৳{{ number_format($totalTax, 0) }}</td></tr>
                        <tr><td class="text-muted">PF Deduction</td><td class="fw-bold text-end text-danger">৳{{ number_format($totalPf, 0) }}</td></tr>
                        <tr><td class="text-muted">Advance Deduction</td><td class="fw-bold text-end text-danger">৳{{ number_format($totalAdvance, 0) }}</td></tr>
                        <tr><td class="text-muted">OT Payable</td><td class="fw-bold text-end text-success">৳{{ number_format($totalOt, 0) }}</td></tr>
                        <tr><td class="text-muted">Bonus Paid</td><td class="fw-bold text-end text-success">৳{{ number_format($totalBonus, 0) }}</td></tr>
                        <tr class="border-top"><td class="fw-bold">Net Total</td><td class="fw-bold text-end text-primary">৳{{ number_format($totalNet, 0) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

@elseif($tab === 'employee')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-user text-primary me-1"></i> Employee Salary Report — {{ $monthLabel }}</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Gross</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late (min)</th>
                            <th>Late Ded.</th>
                            <th>Bonus</th>
                            <th>Tax</th>
                            <th>PF</th>
                            <th>Net</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryData as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $s->emp_code }}<br><span class="text-muted font-size-10">{{ $s->emp_name }}</span></td>
                                <td>{{ $s->dept_name }}</td>
                                <td>{{ $s->designation_name }}</td>
                                <td>৳{{ number_format($s->gross_salary, 0) }}</td>
                                <td>{{ $s->present_days }}</td>
                                <td class="{{ $s->absent_days > 0 ? 'text-danger fw-bold' : '' }}">{{ $s->absent_days }}</td>
                                <td>{{ $s->total_late_minutes }}</td>
                                <td class="{{ $s->late_deduction > 0 ? 'text-danger' : '' }}">{{ $s->late_deduction > 0 ? '৳'.number_format($s->late_deduction, 0) : '—' }}</td>
                                <td class="{{ $s->bonus_amount > 0 ? 'text-success' : '' }}">{{ $s->bonus_amount > 0 ? '৳'.number_format($s->bonus_amount, 0) : '—' }}</td>
                                <td>{{ $s->tax_deduction > 0 ? '৳'.number_format($s->tax_deduction, 0) : '—' }}</td>
                                <td>{{ $s->pf_deduction > 0 ? '৳'.number_format($s->pf_deduction, 0) : '—' }}</td>
                                <td class="fw-bold text-primary">৳{{ number_format($s->net_payable, 0) }}</td>
                                <td><span class="badge {{ $s->status === 'approved' ? 'badge-approved' : 'badge-generated' }} font-size-10 rounded-pill">{{ ucfirst($s->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="14" class="text-center text-muted py-4">No salary records found for this month.</td></tr>
                        @endforelse
                    </tbody>
                    @if($salaryData->count())
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td>৳{{ number_format($salaryData->sum('gross_salary'), 0) }}</td>
                                <td>{{ $salaryData->sum('present_days') }}</td>
                                <td class="text-danger">{{ $salaryData->sum('absent_days') }}</td>
                                <td>{{ $salaryData->sum('total_late_minutes') }}</td>
                                <td class="text-danger">৳{{ number_format($salaryData->sum('late_deduction'), 0) }}</td>
                                <td class="text-success">৳{{ number_format($salaryData->sum('bonus_amount'), 0) }}</td>
                                <td>৳{{ number_format($salaryData->sum('tax_deduction'), 0) }}</td>
                                <td>৳{{ number_format($salaryData->sum('pf_deduction'), 0) }}</td>
                                <td class="text-primary">৳{{ number_format($salaryData->sum('net_payable'), 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

@elseif($tab === 'department')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-building text-primary me-1"></i> Department-wise Salary Report — {{ $monthLabel }}</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10">
                        <tr>
                            <th>Department</th>
                            <th class="text-end">Employees</th>
                            <th class="text-end">Total Gross</th>
                            <th class="text-end">Total Bonus</th>
                            <th class="text-end">Total Deduction</th>
                            <th class="text-end">Total Net</th>
                            <th class="text-end">Avg Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deptSalarySummary as $d)
                            <tr>
                                <td class="fw-semibold">{{ $d['dept_name'] }}</td>
                                <td class="text-end">{{ $d['count'] }}</td>
                                <td class="text-end">৳{{ number_format($d['total_gross'], 0) }}</td>
                                <td class="text-end text-success">৳{{ number_format($d['total_bonus'], 0) }}</td>
                                <td class="text-end text-danger">৳{{ number_format($d['total_deduction'], 0) }}</td>
                                <td class="text-end fw-bold text-primary">৳{{ number_format($d['total_net'], 0) }}</td>
                                <td class="text-end">৳{{ number_format($d['avg_net'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No department data found.</td></tr>
                        @endforelse
                    </tbody>
                    @if($deptSalarySummary->count())
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-end">{{ $deptSalarySummary->sum('count') }}</td>
                                <td class="text-end">৳{{ number_format($deptSalarySummary->sum('total_gross'), 0) }}</td>
                                <td class="text-end text-success">৳{{ number_format($deptSalarySummary->sum('total_bonus'), 0) }}</td>
                                <td class="text-end text-danger">৳{{ number_format($deptSalarySummary->sum('total_deduction'), 0) }}</td>
                                <td class="text-end text-primary">৳{{ number_format($deptSalarySummary->sum('total_net'), 0) }}</td>
                                <td class="text-end">৳{{ number_format($salaryData->avg('net_payable'), 0) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bx bx-bar-chart text-primary me-1"></i> Department Chart</h6>
            <canvas id="deptReportChart" height="120"></canvas>
        </div>
    </div>

@elseif($tab === 'punch')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-data text-primary me-1"></i> Punch Data Report — {{ $monthLabel }}</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="d-flex gap-3 mb-3 font-size-12">
                <span>Total Punches: <strong>{{ $punchData->count() }}</strong></span>
                <span>Check In: <strong class="text-success">{{ $punchData->where('status', 0)->count() }}</strong></span>
                <span>Check Out: <strong class="text-danger">{{ $punchData->where('status', 1)->count() }}</strong></span>
            </div>
            <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10" style="position:sticky; top:0; background:#fff; z-index:1;">
                        <tr>
                            <th>Date & Time</th>
                            <th>Employee</th>
                            <th>PIN</th>
                            <th>Status</th>
                            <th>Verify</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($punchData as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->punch_date_time)->format('d M Y, h:i A') }}</td>
                                <td class="fw-semibold">{{ $p->emp_name }}</td>
                                <td><code>{{ $p->employee_id }}</code></td>
                                <td>
                                    @if($p->status == 0)
                                        <span class="badge bg-success font-size-10">Check In</span>
                                    @else
                                        <span class="badge bg-danger font-size-10">Check Out</span>
                                    @endif
                                </td>
                                <td>{{ $p->verify_type }}</td>
                                <td class="text-muted">{{ $p->source ?? 'ADMS' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No punch data found for this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tab === 'leave')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-calendar text-primary me-1"></i> Leave Report</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="d-flex gap-3 mb-3 font-size-12">
                <span>Total Applications: <strong>{{ $leaveData->count() }}</strong></span>
                <span>Approved: <strong class="text-success">{{ $leaveData->where('status', 'approved')->count() }}</strong></span>
                <span>Pending: <strong class="text-warning">{{ $leaveData->where('status', 'pending')->count() }}</strong></span>
                <span>Rejected: <strong class="text-danger">{{ $leaveData->where('status', 'rejected')->count() }}</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveData as $l)
                            <tr>
                                <td class="fw-semibold">{{ $l->emp_code }}<br><span class="text-muted font-size-10">{{ $l->emp_name }}</span></td>
                                <td>{{ $l->dept_name }}</td>
                                <td><span class="badge bg-light text-dark font-size-10">{{ $l->leave_type_name ?? 'N/A' }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($l->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($l->end_date)->format('d M Y') }}</td>
                                <td class="fw-bold">{{ $l->total_days }}</td>
                                <td class="text-muted">{{ Str::limit($l->reason, 40) }}</td>
                                <td>
                                    @if($l->status === 'approved')
                                        <span class="badge badge-approved font-size-10 rounded-pill">Approved</span>
                                    @elseif($l->status === 'pending')
                                        <span class="badge badge-pending font-size-10 rounded-pill">Pending</span>
                                    @else
                                        <span class="badge badge-rejected font-size-10 rounded-pill">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No leave records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tab === 'timecard')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-time text-primary me-1"></i> Time Card — {{ $monthLabel }}</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10">
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Dept</th>
                            <th>Shift</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Hours</th>
                            <th>Late</th>
                            <th>Early</th>
                            <th>OT</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceData as $a)
                            <tr class="{{ $a->is_late ? 'table-warning' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($a->date)->format('d M, D') }}</td>
                                <td class="fw-semibold">{{ $a->emp_code }}<br><span class="text-muted font-size-10">{{ $a->emp_name }}</span></td>
                                <td>{{ $a->dept_name }}</td>
                                <td><span class="font-size-10">{{ $a->shift_name }}</span></td>
                                <td class="text-success fw-bold">{{ $a->in_time ? \Carbon\Carbon::parse($a->in_time)->format('h:i A') : '—' }}</td>
                                <td class="text-danger fw-bold">{{ $a->out_time ? \Carbon\Carbon::parse($a->out_time)->format('h:i A') : '—' }}</td>
                                <td>{{ number_format($a->total_hours, 2) }}h</td>
                                <td class="{{ $a->late_minutes > 0 ? 'text-danger fw-bold' : '' }}">{{ $a->late_minutes }}m</td>
                                <td>{{ $a->early_minutes }}m</td>
                                <td class="{{ $a->overtime_minutes > 0 ? 'text-success fw-bold' : '' }}">{{ $a->overtime_minutes }}m</td>
                                <td>
                                    @if($a->status === 'present')
                                        <span class="badge bg-success font-size-10 rounded-pill">Present</span>
                                    @elseif($a->status === 'absent')
                                        <span class="badge bg-danger font-size-10 rounded-pill">Absent</span>
                                    @elseif($a->status === 'leave')
                                        <span class="badge bg-info font-size-10 rounded-pill">Leave</span>
                                    @elseif($a->status === 'holiday')
                                        <span class="badge bg-purple font-size-10 rounded-pill" style="background:#a78bfa; color:#fff;">Holiday</span>
                                    @elseif($a->status === 'weekend')
                                        <span class="badge bg-secondary font-size-10 rounded-pill">Weekend</span>
                                    @else
                                        <span class="badge bg-light text-muted font-size-10 rounded-pill">{{ $a->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center text-muted py-4">No attendance data found for this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tab === 'salary')
    @php
        $deptGroups = $salaryData->groupBy('dept_name');
        $viewMode = request('view_mode', 'group');
    @endphp

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h6 class="fw-bold mb-1"><i class="bx bx-money text-primary me-1"></i> Salary Sheet — {{ date('F Y', strtotime($month . '-01')) }}</h6>
                    <span class="font-size-12 text-muted">{{ $salaryData->count() }} employees &bull; Total Net: ৳{{ number_format($salaryData->sum('net_payable'), 0) }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="btn-group btn-group-sm no-print" role="group">
                        <a href="{{ route('subscriber.payroll.report', array_merge(request()->query(), ['tab' => 'salary', 'view_mode' => 'group'])) }}"
                           class="btn {{ $viewMode === 'group' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bx bx-group me-1"></i> Group by Dept
                        </a>
                        <a href="{{ route('subscriber.payroll.report', array_merge(request()->query(), ['tab' => 'salary', 'view_mode' => 'flat'])) }}"
                           class="btn {{ $viewMode === 'flat' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bx bx-list-ul me-1"></i> Flat List
                        </a>
                    </div>
                    <a href="{{ route('subscriber.payroll.report.export', ['type' => 'salary']) }}?month={{ $month }}&department_id={{ $departmentId }}&employee_profile_id={{ $employeeId }}&format=pdf&tab=salary"
                       target="_blank" class="btn btn-outline-danger btn-sm no-print">
                        <i class="bx bx-file me-1"></i> PDF
                    </a>
                    <a href="{{ route('subscriber.payroll.report.export', ['type' => 'salary']) }}?month={{ $month }}&department_id={{ $departmentId }}&employee_profile_id={{ $employeeId }}&format=csv&tab=salary"
                       class="btn btn-outline-success btn-sm no-print">
                        <i class="bx bx-table me-1"></i> Excel
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($viewMode === 'group')
        @foreach($deptGroups as $deptName => $items)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                    <span class="fw-bold"><i class="bx bx-building me-1"></i> {{ $deptName ?: 'N/A' }}</span>
                    <span class="badge bg-white text-primary font-size-11">{{ $items->count() }} employees &bull; Net: ৳{{ number_format($items->sum('net_payable'), 0) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle font-size-11 mb-0">
                            <thead class="text-muted text-uppercase font-size-10">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Basic</th>
                                    <th>House</th>
                                    <th>Medical</th>
                                    <th>Conv.</th>
                                    <th>Gross</th>
                                    <th>P/A/L</th>
                                    <th>Late</th>
                                    <th>Late Ded</th>
                                    <th>Tax</th>
                                    <th>PF</th>
                                    <th>Bonus</th>
                                    <th>OT</th>
                                    <th>Adv</th>
                                    <th>Net</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold" style="white-space:nowrap;">{{ $s->emp_code }}<br><span class="text-muted font-size-10">{{ $s->emp_name }}</span></td>
                                        <td>{{ $s->designation_name }}</td>
                                        <td>৳{{ number_format($s->basic_salary, 0) }}</td>
                                        <td>৳{{ number_format($s->house_rent, 0) }}</td>
                                        <td>৳{{ number_format($s->medical, 0) }}</td>
                                        <td>৳{{ number_format($s->conveyance, 0) }}</td>
                                        <td class="fw-bold">৳{{ number_format($s->gross_salary, 0) }}</td>
                                        <td>{{ $s->present_days }}/{{ $s->absent_days }}/{{ $s->leave_days }}</td>
                                        <td>{{ $s->total_late_minutes }}m</td>
                                        <td class="{{ $s->late_deduction > 0 ? 'text-danger' : '' }}">{{ $s->late_deduction > 0 ? '৳'.number_format($s->late_deduction, 0) : '—' }}</td>
                                        <td>{{ $s->tax_deduction > 0 ? '৳'.number_format($s->tax_deduction, 0) : '—' }}</td>
                                        <td>{{ $s->pf_deduction > 0 ? '৳'.number_format($s->pf_deduction, 0) : '—' }}</td>
                                        <td class="{{ $s->bonus_amount > 0 ? 'text-success fw-bold' : '' }}">{{ $s->bonus_amount > 0 ? '৳'.number_format($s->bonus_amount, 0) : '—' }}</td>
                                        <td class="{{ $s->ot_payable > 0 ? 'text-success' : '' }}">{{ $s->ot_payable > 0 ? '৳'.number_format($s->ot_payable, 0) : '—' }}</td>
                                        <td>{{ $s->advance_deduction > 0 ? '৳'.number_format($s->advance_deduction, 0) : '—' }}</td>
                                        <td class="fw-bold text-primary">৳{{ number_format($s->net_payable, 0) }}</td>
                                        <td><span class="badge {{ $s->status === 'approved' ? 'badge-approved' : 'badge-generated' }} font-size-10 rounded-pill">{{ ucfirst($s->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold border-top" style="background:#f8fafc;">
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal</td>
                                    <td>৳{{ number_format($items->sum('basic_salary'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('house_rent'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('medical'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('conveyance'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('gross_salary'), 0) }}</td>
                                    <td>{{ $items->sum('present_days') }}/{{ $items->sum('absent_days') }}/{{ $items->sum('leave_days') }}</td>
                                    <td>{{ $items->sum('total_late_minutes') }}m</td>
                                    <td class="text-danger">৳{{ number_format($items->sum('late_deduction'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('tax_deduction'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('pf_deduction'), 0) }}</td>
                                    <td class="text-success">৳{{ number_format($items->sum('bonus_amount'), 0) }}</td>
                                    <td class="text-success">৳{{ number_format($items->sum('ot_payable'), 0) }}</td>
                                    <td>৳{{ number_format($items->sum('advance_deduction'), 0) }}</td>
                                    <td class="text-primary">৳{{ number_format($items->sum('net_payable'), 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Grand Total --}}
        <div class="card">
            <div class="card-body p-3" style="background:#4f46e5; color:#fff; border-radius:16px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">GRAND TOTAL ({{ $salaryData->count() }} employees, {{ $deptGroups->count() }} departments)</span>
                    <div class="d-flex gap-4">
                        <span>Gross: <strong>৳{{ number_format($salaryData->sum('gross_salary'), 0) }}</strong></span>
                        <span>Deductions: <strong>৳{{ number_format($salaryData->sum('late_deduction') + $salaryData->sum('absent_deduction') + $salaryData->sum('tax_deduction') + $salaryData->sum('pf_deduction') + $salaryData->sum('advance_deduction'), 0) }}</strong></span>
                        <span>Bonus: <strong>৳{{ number_format($salaryData->sum('bonus_amount'), 0) }}</strong></span>
                        <span>Net: <strong>৳{{ number_format($salaryData->sum('net_payable'), 0) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Flat List View --}}
        <div class="card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle font-size-11">
                        <thead class="text-muted text-uppercase font-size-10">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Dept</th>
                                <th>Designation</th>
                                <th>Basic</th>
                                <th>House</th>
                                <th>Medical</th>
                                <th>Conv.</th>
                                <th>Other</th>
                                <th>Gross</th>
                                <th>P</th>
                                <th>A</th>
                                <th>L</th>
                                <th>Late Ded</th>
                                <th>Abs Ded</th>
                                <th>Tax</th>
                                <th>PF</th>
                                <th>Bonus</th>
                                <th>OT</th>
                                <th>Adv</th>
                                <th>Net</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salaryData as $i => $s)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold" style="white-space:nowrap;">{{ $s->emp_code }}<br><span class="text-muted font-size-10">{{ $s->emp_name }}</span></td>
                                    <td>{{ substr($s->dept_name ?? '', 0, 10) }}</td>
                                    <td>{{ $s->designation_name }}</td>
                                    <td>৳{{ number_format($s->basic_salary, 0) }}</td>
                                    <td>৳{{ number_format($s->house_rent, 0) }}</td>
                                    <td>৳{{ number_format($s->medical, 0) }}</td>
                                    <td>৳{{ number_format($s->conveyance, 0) }}</td>
                                    <td>৳{{ number_format($s->other_allowances, 0) }}</td>
                                    <td class="fw-bold">৳{{ number_format($s->gross_salary, 0) }}</td>
                                    <td>{{ $s->present_days }}</td>
                                    <td class="{{ $s->absent_days > 0 ? 'text-danger' : '' }}">{{ $s->absent_days }}</td>
                                    <td>{{ $s->leave_days }}</td>
                                    <td class="{{ $s->late_deduction > 0 ? 'text-danger' : '' }}">{{ $s->late_deduction > 0 ? '৳'.number_format($s->late_deduction, 0) : '—' }}</td>
                                    <td class="{{ $s->absent_deduction > 0 ? 'text-danger' : '' }}">{{ $s->absent_deduction > 0 ? '৳'.number_format($s->absent_deduction, 0) : '—' }}</td>
                                    <td>{{ $s->tax_deduction > 0 ? '৳'.number_format($s->tax_deduction, 0) : '—' }}</td>
                                    <td>{{ $s->pf_deduction > 0 ? '৳'.number_format($s->pf_deduction, 0) : '—' }}</td>
                                    <td class="{{ $s->bonus_amount > 0 ? 'text-success fw-bold' : '' }}">{{ $s->bonus_amount > 0 ? '৳'.number_format($s->bonus_amount, 0) : '—' }}</td>
                                    <td class="{{ $s->ot_payable > 0 ? 'text-success' : '' }}">{{ $s->ot_payable > 0 ? '৳'.number_format($s->ot_payable, 0) : '—' }}</td>
                                    <td>{{ $s->advance_deduction > 0 ? '৳'.number_format($s->advance_deduction, 0) : '—' }}</td>
                                    <td class="fw-bold text-primary">৳{{ number_format($s->net_payable, 0) }}</td>
                                    <td><span class="badge {{ $s->status === 'approved' ? 'badge-approved' : 'badge-generated' }} font-size-10 rounded-pill">{{ ucfirst($s->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="22" class="text-center text-muted py-4">No salary records found.</td></tr>
                            @endforelse
                        </tbody>
                        @if($salaryData->count())
                            <tfoot class="fw-bold border-top">
                                <tr>
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td>৳{{ number_format($salaryData->sum('basic_salary'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('house_rent'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('medical'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('conveyance'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('other_allowances'), 0) }}</td>
                                    <td class="fw-bold">৳{{ number_format($salaryData->sum('gross_salary'), 0) }}</td>
                                    <td>{{ $salaryData->sum('present_days') }}</td>
                                    <td class="text-danger">{{ $salaryData->sum('absent_days') }}</td>
                                    <td>{{ $salaryData->sum('leave_days') }}</td>
                                    <td class="text-danger">৳{{ number_format($salaryData->sum('late_deduction'), 0) }}</td>
                                    <td class="text-danger">৳{{ number_format($salaryData->sum('absent_deduction'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('tax_deduction'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('pf_deduction'), 0) }}</td>
                                    <td class="text-success">৳{{ number_format($salaryData->sum('bonus_amount'), 0) }}</td>
                                    <td class="text-success">৳{{ number_format($salaryData->sum('ot_payable'), 0) }}</td>
                                    <td>৳{{ number_format($salaryData->sum('advance_deduction'), 0) }}</td>
                                    <td class="text-primary">৳{{ number_format($salaryData->sum('net_payable'), 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif

@elseif($tab === 'advance')
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bx bx-dollar text-primary me-1"></i> Advance Report</h6>
                @include('subscriber.payroll.exports.export-buttons')
            </div>
            <div class="d-flex gap-3 mb-3 font-size-12">
                <span>Total Advances: <strong>{{ $advanceData->count() }}</strong></span>
                <span>Total Amount: <strong class="text-primary">৳{{ number_format($advanceData->sum('amount'), 0) }}</strong></span>
                <span>Approved: <strong class="text-success">{{ $advanceData->where('status', 'approved')->count() }}</strong></span>
                <span>Pending: <strong class="text-warning">{{ $advanceData->where('status', 'pending')->count() }}</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle font-size-12">
                    <thead class="text-muted text-uppercase font-size-10">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Advance Type</th>
                            <th>Amount</th>
                            <th>Approved</th>
                            <th>Installments</th>
                            <th>Monthly Ded.</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advanceData as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->emp_code }}<br><span class="text-muted font-size-10">{{ $a->emp_name }}</span></td>
                                <td>{{ $a->dept_name }}</td>
                                <td><span class="badge bg-light text-dark font-size-10">{{ $a->advance_type_name ?? 'N/A' }}</span></td>
                                <td class="fw-bold">৳{{ number_format($a->amount, 0) }}</td>
                                <td>৳{{ number_format($a->approved_amount, 0) }}</td>
                                <td>{{ $a->installments }}</td>
                                <td>৳{{ number_format($a->monthly_deduction, 0) }}</td>
                                <td>
                                    @if($a->status === 'approved')
                                        <span class="badge badge-approved font-size-10 rounded-pill">Approved</span>
                                    @elseif($a->status === 'pending')
                                        <span class="badge badge-pending font-size-10 rounded-pill">Pending</span>
                                    @else
                                        <span class="badge badge-rejected font-size-10 rounded-pill">{{ ucfirst($a->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($a->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No advance records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const month = '{{ $month }}';
const colors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#f97316','#14b8a6'];

@if($tab === 'overview')
    // Salary Pie Chart
    const totalGross = {{ $totalGross }};
    const totalBonus = {{ $totalBonus }};
    const totalLateDed = {{ $totalLate }};
    const totalTax = {{ $totalTax }};
    const totalPf = {{ $totalPf }};
    const totalAbsentDed = {{ $totalAbsent }};
    new Chart(document.getElementById('salaryPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Gross Salary', 'Bonus', 'Late Deduction', 'Tax', 'PF', 'Absent Deduction'],
            datasets: [{ data: [totalGross, totalBonus, totalLateDed, totalTax, totalPf, totalAbsentDed], backgroundColor: ['#6366f1','#22c55e','#ef4444','#f59e0b','#8b5cf6','#ec4899'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });

    // Department Bar Chart
    const deptLabels = @json($deptSalarySummary->pluck('dept_name'));
    const deptNet = @json($deptSalarySummary->pluck('total_net')->map(fn($v) => round($v)));
    new Chart(document.getElementById('deptBarChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{ label: 'Net Payable', data: deptNet, backgroundColor: '#6366f1' }]
        },
        options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { callback: v => '৳' + (v/1000).toFixed(0) + 'k' } } } }
    });

    // Deduction Chart
    new Chart(document.getElementById('deductionChart'), {
        type: 'bar',
        data: {
            labels: ['Late', 'Absent', 'Tax', 'PF', 'Advance'],
            datasets: [{ label: 'Amount', data: [{{ $totalLate }}, {{ $totalAbsent }}, {{ $totalTax }}, {{ $totalPf }}, {{ $totalAdvance }}], backgroundColor: ['#ef4444','#ec4899','#f59e0b','#8b5cf6','#06b6d4'] }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => '৳' + (v/1000).toFixed(0) + 'k' } } } }
    });

    // Attendance Chart
    new Chart(document.getElementById('attendanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Leave', 'Holiday', 'Weekend'],
            datasets: [{ data: [{{ $presentCount }}, {{ $absentCount }}, {{ $leaveCount }}, {{ $holidayCount }}, {{ $weekendCount }}], backgroundColor: ['#22c55e','#ef4444','#06b6d4','#8b5cf6','#9ca3af'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });
@endif

@if($tab === 'department')
    const deptLabels2 = @json($deptSalarySummary->pluck('dept_name'));
    const deptGross = @json($deptSalarySummary->pluck('total_gross')->map(fn($v) => round($v)));
    const deptNet2 = @json($deptSalarySummary->pluck('total_net')->map(fn($v) => round($v)));
    const deptBonus = @json($deptSalarySummary->pluck('total_bonus')->map(fn($v) => round($v)));
    new Chart(document.getElementById('deptReportChart'), {
        type: 'bar',
        data: {
            labels: deptLabels2,
            datasets: [
                { label: 'Gross', data: deptGross, backgroundColor: '#6366f1' },
                { label: 'Net', data: deptNet2, backgroundColor: '#22c55e' },
                { label: 'Bonus', data: deptBonus, backgroundColor: '#f59e0b' }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } }, scales: { y: { ticks: { callback: v => '৳' + (v/1000).toFixed(0) + 'k' } } } }
    });
@endif
</script>
@endpush
@endsection
