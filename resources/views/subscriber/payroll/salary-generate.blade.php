@extends('layouts.subscriber')

@section('title', 'Generate Salary - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
    .workflow-step { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; }
    .workflow-step.active { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
    .workflow-step.done { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .workflow-step.pending { background: #f9fafb; color: #9ca3af; border: 1px solid #e5e7eb; }
    .workflow-step .step-num { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }
    .workflow-step.active .step-num { background: #4f46e5; color: #fff; }
    .workflow-step.done .step-num { background: #059669; color: #fff; }
    .workflow-step.pending .step-num { background: #d1d5db; color: #fff; }
    .badge-generated { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-approved { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
</style>

<div class="page-title-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Databases</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Generate Salary</h4>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('subscriber.payroll.salary-generate') }}" class="d-flex gap-2 align-items-center">
            <input type="month" name="month" class="form-control form-control-sm" style="width:160px;" value="{{ $month }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

@php
    $draftCount = $payrollStats->get('generated')->count ?? 0;
    $approvedCount = $payrollStats->get('approved')->count ?? 0;
    $totalExisting = $draftCount + $approvedCount;
    $draftTotal = $payrollStats->get('generated')->total_net ?? 0;
    $approvedTotal = $payrollStats->get('approved')->total_net ?? 0;
    $hasDraft = $draftCount > 0;
    $hasApproved = $approvedCount > 0;
    $hasAnyPayroll = $totalExisting > 0;
@endphp

{{-- Workflow Steps --}}
<div class="card mb-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bx bx-flow-chart text-primary me-1"></i> Salary Workflow</h6>
    <div class="d-flex gap-3 flex-wrap">
        {{-- Step 1: Always done when there's any payroll data --}}
        <div class="workflow-step {{ $hasAnyPayroll ? 'done' : 'active' }}">
            <span class="step-num">{{ $hasAnyPayroll ? '<i class="bx bx-check"></i>' : '1' }}</span>
            <span>Pre-Process & Preview</span>
        </div>
        {{-- Step 2: Done when records exist (draft or approved) --}}
        <div class="workflow-step {{ $hasAnyPayroll ? 'done' : 'pending' }}">
            <span class="step-num">{{ $hasAnyPayroll ? '<i class="bx bx-check"></i>' : '2' }}</span>
            <span>Generate (Draft)</span>
        </div>
        {{-- Step 3: Active when drafts need review --}}
        <div class="workflow-step {{ $hasApproved && !$hasDraft ? 'done' : ($hasDraft ? 'active' : 'pending') }}">
            <span class="step-num">{{ ($hasApproved && !$hasDraft) ? '<i class="bx bx-check"></i>' : ($hasDraft ? '3' : '3') }}</span>
            <span>Review & Check</span>
        </div>
        {{-- Step 4: Active when drafts need confirm --}}
        <div class="workflow-step {{ $hasApproved && !$hasDraft ? 'done' : ($hasDraft ? 'active' : 'pending') }}">
            <span class="step-num">{{ ($hasApproved && !$hasDraft) ? '<i class="bx bx-check"></i>' : ($hasDraft ? '4' : '4') }}</span>
            <span>Confirm (Lock)</span>
        </div>
    </div>
    @if($hasApproved && !$hasDraft)
        <div class="mt-3 p-3 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0;">
            <span class="font-size-12 text-success fw-semibold">
                <i class="bx bx-check-circle me-1"></i> Salary for {{ date('F Y', strtotime($month . '-01')) }} is <strong>confirmed</strong> and ready for payment.
            </span>
        </div>
    @endif
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Employees</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $employees->count() }}</h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600"><i class="bx bx-user"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Draft Records</span>
                    <h3 class="mt-2 mb-0 fw-bold {{ $hasDraft ? 'text-warning' : '' }}">{{ $draftCount }}</h3>
                    @if($hasDraft)
                        <span class="font-size-10 text-muted">Net: ৳{{ number_format($draftTotal, 0) }}</span>
                    @endif
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600"><i class="bx bx-file"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Confirmed</span>
                    <h3 class="mt-2 mb-0 fw-bold {{ $hasApproved ? 'text-success' : '' }}">{{ $approvedCount }}</h3>
                    @if($hasApproved)
                        <span class="font-size-10 text-muted">Net: ৳{{ number_format($approvedTotal, 0) }}</span>
                    @endif
                </div>
                <div class="stat-icon bg-green-50 border border-green-100 text-green-600"><i class="bx bx-check-double"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Calculated Preview</span>
                    <h3 class="mt-2 mb-0 fw-bold text-primary">{{ number_format(collect($payrollData)->sum('net_payable'), 0) }}</h3>
                </div>
                <div class="stat-icon bg-blue-50 border border-blue-100 text-blue-600"><i class="bx bx-calculator"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 p-4" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(99,102,241,0.15) !important;">
    <div class="d-flex align-items-center gap-3">
        <i class="bx bx-info-circle text-primary font-size-24"></i>
        <div>
            <strong class="font-size-13">Month: {{ date('F Y', strtotime($month . '-01')) }}</strong>
            <p class="font-size-12 text-muted mb-0 mt-1">
                @if(!$hasAnyPayroll)
                    <strong>Step 1:</strong> Preview calculated salary below. Select employees and click <strong>"Generate Salary"</strong> to create draft records.
                @elseif($hasDraft)
                    <strong>Step 3:</strong> Review the draft salary table. When satisfied, click <strong>"Confirm & Lock"</strong> on the right panel to finalize. You can also <strong>"Remove Draft"</strong> to start over.
                @else
                    <strong>Done!</strong> Salary is confirmed and ready for payment processing.
                @endif
            </p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0"><i class="bx bx-calculator text-primary me-1"></i> Salary Preview — {{ date('F Y', strtotime($month . '-01')) }}</h5>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('subscriber.payroll.salary-generate') }}" class="d-flex gap-2">
                            <select name="department_id" class="form-select form-select-sm" style="width:140px;" onchange="this.form.submit()">
                                <option value="">All Depts</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            <select name="designation_id" class="form-select form-select-sm" style="width:140px;" onchange="this.form.submit()">
                                <option value="">All Designations</option>
                                @foreach($designations as $d)
                                    <option value="{{ $d->id }}" {{ request('designation_id') == $d->id ? 'selected' : '' }}>{{ $d->title }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="month" value="{{ $month }}">
                        </form>
                    </div>
                </div>

                @if($employees->count())
                    <form method="POST" action="{{ route('subscriber.payroll.salary-generate.generate') }}" id="salaryForm" onsubmit="return confirm('Generate draft salary for the selected employees for {{ date('F Y', strtotime($month . '-01')) }}?')">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll" onchange="document.querySelectorAll('.emp-check').forEach(c => c.checked = this.checked)">
                                <label class="form-check-label fw-semibold font-size-13" for="selectAll">Select All</label>
                            </div>
                            <span class="text-muted font-size-12"><span id="selectedCount">0</span> selected</span>
                        </div>

                        <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                            <table class="table table-hover align-middle font-size-12">
                                <thead class="text-muted text-uppercase font-size-10">
                                    <tr>
                                        <th style="width:30px;"></th>
                                        <th>Employee</th>
                                        <th>Dept</th>
                                        <th>P/L</th>
                                        <th>Gross</th>
                                        <th>Late</th>
                                        <th>Bonus</th>
                                        <th>Adv.</th>
                                        <th>Net</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $emp)
                                        @php
                                            $pd = $payrollData[$emp->id] ?? null;
                                            $empStatus = $existingPayroll[$emp->id] ?? null;
                                        @endphp
                                        <tr class="{{ $empStatus === 'approved' ? 'table-success' : ($empStatus === 'generated' ? 'table-warning' : '') }}">
                                            <td>
                                                @if($pd && $empStatus !== 'approved')
                                                    <input class="form-check-input emp-check" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}">
                                                @elseif(!$pd)
                                                    <span class="text-muted font-size-11">—</span>
                                                @else
                                                    <i class="bx bx-lock text-success font-size-14"></i>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">{{ $emp->employee_id }}<br><span class="text-muted font-size-10">{{ $emp->user?->name ?? $emp->employee_id }}</span></td>
                                            <td>{{ $emp->department?->name ? substr($emp->department->name, 0, 8) : '—' }}</td>
                                            <td>
                                                @if($pd)
                                                    {{ $pd['present_days'] }}/{{ $pd['absent_days'] }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>@if($pd) {{ number_format($pd['gross_salary'], 0) }} @else <span class="text-danger font-size-11">No Struct</span> @endif</td>
                                            <td>@if($pd && $pd['late_deduction'] > 0) <span class="text-danger">{{ number_format($pd['late_deduction'], 0) }}</span> @else — @endif</td>
                                            <td>@if($pd && $pd['bonus_amount'] > 0) <span class="text-success">{{ number_format($pd['bonus_amount'], 0) }}</span> @else — @endif</td>
                                            <td>@if($pd && $pd['advance_deduction'] > 0) {{ number_format($pd['advance_deduction'], 0) }} @else — @endif</td>
                                            <td class="fw-bold">@if($pd) {{ number_format($pd['net_payable'], 0) }} @else — @endif</td>
                                            <td>
                                                @if($empStatus === 'approved')
                                                    <span class="badge badge-approved font-size-10 rounded-pill"><i class="bx bx-check-double me-1"></i>Confirmed</span>
                                                @elseif($empStatus === 'generated')
                                                    <span class="badge badge-generated font-size-10 rounded-pill"><i class="bx bx-file me-1"></i>Draft</span>
                                                @elseif($pd)
                                                    <span class="badge bg-light text-muted font-size-10 rounded-pill">Ready</span>
                                                @else
                                                    <span class="badge bg-light text-muted font-size-10 rounded-pill">No Data</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="font-size-12 text-muted">
                                <span id="selectedCountBottom">0</span> employee(s) selected &bull;
                                Net total: <strong id="netTotal">0</strong>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bx bx-play-circle me-2"></i> Generate Salary (Draft)
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-user-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No employees found for the selected filters.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bx bx-list-ul text-primary me-1"></i> Per-Employee Breakdown</h6>
                <p class="font-size-12 text-muted mb-3">Click an employee row to see full calculation details.</p>

                @php $firstEmp = $employees->first(); @endphp
                @if($firstEmp && ($pd = $payrollData[$firstEmp->id] ?? null))
                    @include('subscriber.payroll.partials.salary-breakdown', ['emp' => $firstEmp, 'pd' => $pd])
                @else
                    <div class="text-center py-4">
                        <i class="bx bx-calculator text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted font-size-12 mt-2">Select filters to see a breakdown</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Confirm & Undo Panel --}}
        <div class="card">
            <div class="card-body p-4">
                {{-- Confirm Button --}}
                @if($hasDraft)
                    <div class="mb-3 p-3 rounded-3" style="background:#eef2ff; border:1px solid #c7d2fe;">
                        <span class="font-size-12 fw-semibold text-indigo-700">
                            <i class="bx bx-info-circle me-1"></i> <strong>Next Step:</strong> Review the salary table, then click "Confirm & Lock" to finalize.
                        </span>
                    </div>
                    <h6 class="fw-bold mb-3 text-success"><i class="bx bx-check-double me-1"></i> Confirm & Lock Salary</h6>
                    <form method="POST" action="{{ route('subscriber.payroll.salary-generate.confirm') }}" onsubmit="return confirm('Confirm and LOCK {{ $draftCount }} draft payroll record(s) for {{ date('F Y', strtotime($month . '-01')) }}?\n\n⚠️ After confirming, salary CANNOT be undone.\n\nClick OK to confirm.')">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <div class="mb-3 p-3 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0;">
                            <span class="font-size-13">
                                <strong>{{ $draftCount }}</strong> draft record(s) ready to confirm.<br>
                                <span class="text-success fw-bold">Total Net: ৳{{ number_format($draftTotal, 0) }}</span>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-success rounded-pill w-100 mb-3">
                            <i class="bx bx-check-double me-2"></i> Confirm & Lock Salary
                        </button>
                    </form>

                    <hr>
                    <h6 class="fw-bold mb-3 text-danger"><i class="bx bx-undo me-1"></i> Undo Draft</h6>
                    <form method="POST" action="{{ route('subscriber.payroll.salary-generate.undo') }}" onsubmit="return confirm('Delete {{ $draftCount }} draft payroll record(s) for {{ date('F Y', strtotime($month . '-01')) }}? Confirmed records will NOT be affected.')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                            <i class="bx bx-trash me-2"></i> Remove Draft Records
                        </button>
                    </form>
                @elseif($hasApproved)
                    <div class="p-3 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0;">
                        <span class="font-size-13 text-success fw-semibold">
                            <i class="bx bx-check-double me-1"></i> Salary is <strong>confirmed</strong> and ready for payment.
                        </span>
                        <div class="mt-2 font-size-12 text-muted">
                            {{ $approvedCount }} record(s) &bull; Net: ৳{{ number_format($approvedTotal, 0) }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bx bx-calculator text-muted font-size-24"></i>
                        <p class="font-size-12 text-muted mt-2 mb-0">Select employees and click "Generate Salary" to start.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.emp-check').forEach(c => {
        c.addEventListener('change', updateSelection);
    });
    function updateSelection() {
        const checks = document.querySelectorAll('.emp-check:checked');
        const count = checks.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('selectedCountBottom').textContent = count;

        let netTotal = 0;
        checks.forEach(c => {
            const row = c.closest('tr');
            const netCell = row.querySelector('td:nth-child(9)');
            if (netCell) {
                const val = parseFloat(netCell.textContent.replace(/,/g, '')) || 0;
                netTotal += val;
            }
        });
        document.getElementById('netTotal').textContent = netTotal.toLocaleString();
    }
    updateSelection();

    document.querySelectorAll('#salaryForm tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.querySelector('td:not(:first-child)')?.addEventListener('click', function(e) {
            if (e.target.closest('.form-check-input')) return;
            const check = row.querySelector('.emp-check');
            if (!check) return;
            const empId = check.value;
            const breakdown = document.querySelector('.salary-breakdown');
            if (breakdown) {
                fetchBreakdown(empId);
            }
        });
    });
</script>
@endpush
@endsection
