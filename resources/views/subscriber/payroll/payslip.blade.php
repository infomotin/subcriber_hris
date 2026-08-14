@extends('layouts.subscriber')

@section('title', 'Payslip - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .badge-generated { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-approved { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .payslip-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; transition: all 0.2s; }
    .payslip-card:hover { border-color: #818cf8; box-shadow: 0 2px 12px rgba(99,102,241,0.1); }
    .payslip-card .emp-name { font-size: 14px; font-weight: 700; color: #1e293b; }
    .payslip-card .emp-id { font-size: 11px; color: #6b7280; }
    .payslip-card .dept { font-size: 11px; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 20px; display: inline-block; }
    .payslip-card .amount { font-size: 16px; font-weight: 700; }
</style>

<div class="page-title-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Tools</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Payslips</h4>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('subscriber.payroll.payslip') }}" class="d-flex gap-2 align-items-center">
            <input type="month" name="month" class="form-control form-control-sm" style="width:160px;" value="{{ $month }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

@php
    $draftCount = $stats->get('generated', ['count' => 0])['count'] ?? 0;
    $approvedCount = $stats->get('approved', ['count' => 0])['count'] ?? 0;
    $draftTotal = $stats->get('generated', ['total_net' => 0])['total_net'] ?? 0;
    $approvedTotal = $stats->get('approved', ['total_net' => 0])['total_net'] ?? 0;
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Total Records</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $salaryData->count() }}</h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600"><i class="bx bx-receipt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Draft</span>
                    <h3 class="mt-2 mb-0 fw-bold {{ $draftCount ? 'text-warning' : '' }}">{{ $draftCount }}</h3>
                    @if($draftCount)
                        <span class="font-size-10 text-muted">৳{{ number_format($draftTotal, 0) }}</span>
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
                    <h3 class="mt-2 mb-0 fw-bold {{ $approvedCount ? 'text-success' : '' }}">{{ $approvedCount }}</h3>
                    @if($approvedCount)
                        <span class="font-size-10 text-muted">৳{{ number_format($approvedTotal, 0) }}</span>
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
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Total Net Payable</span>
                    <h3 class="mt-2 mb-0 fw-bold text-primary">৳{{ number_format($salaryData->sum('net_payable'), 0) }}</h3>
                </div>
                <div class="stat-icon bg-blue-50 border border-blue-100 text-blue-600"><i class="bx bx-wallet"></i></div>
            </div>
        </div>
    </div>
</div>

@if($salaryData->isEmpty())
    <div class="card p-5 text-center">
        <i class="bx bx-receipt text-muted" style="font-size: 4rem;"></i>
        <h5 class="fw-bold mt-4 mb-2">No Payslips Found</h5>
        <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
            No salary records found for <strong>{{ date('F Y', strtotime($month . '-01')) }}</strong>.
            Generate salary first in the <strong>Salary Generation</strong> section.
        </p>
        <a href="{{ route('subscriber.payroll.salary-generate', ['month' => $month]) }}" class="btn btn-primary rounded-pill px-4">
            <i class="bx bx-calculator me-2"></i> Go to Salary Generation
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($salaryData as $record)
            @php
                $genderIcon = $record->gender === 'Male' ? 'bx-male' : ($record->gender === 'Female' ? 'bx-female' : 'bx-user');
                $statusClass = $record->status === 'approved' ? 'badge-approved' : 'badge-generated';
                $statusText = $record->status === 'approved' ? 'Confirmed' : 'Draft';
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="payslip-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="emp-name">{{ $record->emp_name }}</div>
                            <div class="emp-id">{{ $record->emp_code }} &bull; {{ $record->role_name ?? 'N/A' }}</div>
                        </div>
                        <span class="badge {{ $statusClass }} font-size-10 rounded-pill">{{ $statusText }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="dept"><i class="bx bx-building me-1"></i>{{ $record->dept_name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end pt-2" style="border-top: 1px dashed #e5e7eb;">
                        <div class="font-size-11 text-muted">
                            <div>P: {{ $record->present_days }} | A: {{ $record->absent_days }} | L: {{ $record->leave_days }}</div>
                            <div>Late: {{ $record->total_late_minutes }}m &bull; OT: {{ $record->total_ot_minutes }}m</div>
                        </div>
                        <div class="text-end">
                            <div class="font-size-10 text-muted">Net Payable</div>
                            <div class="amount text-primary">৳{{ number_format($record->net_payable, 0) }}</div>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 d-flex justify-content-between font-size-10 text-muted" style="border-top: 1px solid #f3f4f6;">
                        <span>Gross: ৳{{ number_format($record->gross_salary, 0) }}</span>
                        <span>Bonus: ৳{{ number_format($record->bonus_amount, 0) }}</span>
                        <span>Tax: ৳{{ number_format($record->tax_deduction, 0) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
