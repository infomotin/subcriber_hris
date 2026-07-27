@extends('layouts.subscriber')
@section('title', 'Advance Details')
@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div><span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Salary Advances</span><h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-dollar text-primary me-1.5 align-middle font-size-26"></i>Advance #{{ $advance->id }}</h4></div>
    <a href="{{ route('subscriber.hris.advances.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-arrow-back me-1"></i> Back</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show rounded-pill px-4"><i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-slate-800 mb-0" style="font-family:'Poppins',sans-serif;"><i class="bx bx-detail text-primary me-1.5"></i> Advance Details</h6>
                    @if($advance->status==='approved')<span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11"><i class="bx bx-check-circle align-middle me-0.5"></i> Approved</span>
                    @elseif($advance->status==='rejected')<span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11"><i class="bx bx-x-circle align-middle me-0.5"></i> Rejected</span>
                    @else<span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11"><i class="bx bx-time align-middle me-0.5"></i> Pending</span>@endif
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold text-slate-700 font-size-12">Advance Type</label><p class="mb-0 fw-semibold">{{ $advance->advanceType?->name ?? '--' }} <span class="badge bg-soft-{{ $advance->advanceType?->payment_mode === 'one_time' ? 'info' : 'warning' }} text-{{ $advance->advanceType?->payment_mode === 'one_time' ? 'info' : 'warning' }} font-size-10">{{ $advance->advanceType?->payment_mode === 'one_time' ? 'One Time' : 'Installment' }}</span></p></div>
                    <div class="col-md-6"><label class="form-label fw-semibold text-slate-700 font-size-12">Paid Source</label><p class="mb-0 fw-semibold">{{ $advance->advanceSource?->name ?? '--' }}</p></div>
                    <div class="col-md-4"><label class="form-label fw-semibold text-slate-700 font-size-12">Requested Amount</label><p class="mb-0 fw-bold font-size-20">{{ number_format($advance->amount, 2) }} <small class="text-muted">BDT</small></p></div>
                    <div class="col-md-4"><label class="form-label fw-semibold text-slate-700 font-size-12">Approved Amount</label><p class="mb-0 fw-bold font-size-20 {{ $advance->approved_amount && $advance->approved_amount != $advance->amount ? 'text-info' : '' }}">{{ $advance->approved_amount ? number_format($advance->approved_amount, 2) : '--' }} <small class="text-muted">BDT</small></p></div>
                    <div class="col-md-4"><label class="form-label fw-semibold text-slate-700 font-size-12">Monthly Deduction</label><p class="mb-0 fw-bold font-size-20">{{ number_format($advance->monthly_deduction, 2) }} <small class="text-muted">BDT/mo</small></p></div>
                    <div class="col-md-6"><label class="form-label fw-semibold text-slate-700 font-size-12">Installments</label><p class="mb-0 fw-semibold">{{ $advance->installments }} month(s)</p></div>
                    <div class="col-md-6"><label class="form-label fw-semibold text-slate-700 font-size-12">Reference Employee</label><p class="mb-0 fw-semibold">{{ $advance->referenceEmployee?->user?->name ?? '--' }}</p></div>
                    <div class="col-12"><label class="form-label fw-semibold text-slate-700 font-size-12">Reason</label><p class="mb-0">{{ $advance->reason ?? 'No reason provided.' }}</p></div>
                    @if($advance->action_remarks)<div class="col-12"><label class="form-label fw-semibold text-slate-700 font-size-12">Remarks</label><p class="mb-0">{{ $advance->action_remarks }}</p><small class="text-muted">By {{ $advance->actionedBy?->name ?? 'System' }}</small></div>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;"><i class="bx bx-user text-primary me-1.5"></i> Employee</h6>
                <div class="d-flex align-items-center gap-3 mb-3"><img src="https://ui-avatars.com/api/?name={{ urlencode($advance->employee?->user?->name ?? 'U') }}&background=10b981&color=fff&size=48" class="rounded-circle" width="48" height="48"><div><div class="fw-bold text-dark">{{ $advance->employee?->user?->name ?? 'N/A' }}</div><code class="font-size-11 text-muted">{{ $advance->employee?->employee_id }}</code></div></div>
                <div style="font-size:0.82rem;">
                    <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Department</span><span class="fw-semibold">{{ $advance->employee?->department?->name ?? '--' }}</span></div>
                    <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Designation</span><span class="fw-semibold">{{ $advance->employee?->designation?->title ?? '--' }}</span></div>
                </div>
            </div>
        </div>
        @if($advance->status==='pending')
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;"><i class="bx bx-cog text-primary me-1.5"></i> Actions</h6>
                <form method="POST" action="{{ route('subscriber.hris.advances.approve', $advance) }}" onsubmit="return confirm('Approve this advance?')">@csrf
                    <textarea class="form-control form-control-sm mb-2" name="action_remarks" rows="2" placeholder="Remarks (optional)"></textarea>
                    <button type="submit" class="btn btn-success w-100 rounded-pill mb-3" style="height:40px;"><i class="bx bx-check me-1"></i> Approve</button>
                </form>
                <form method="POST" action="{{ route('subscriber.hris.advances.reject', $advance) }">@csrf
                    <label class="form-label fw-semibold text-slate-700 font-size-12">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm mb-2" name="action_remarks" rows="2" placeholder="Why?" required></textarea>
                    <button type="submit" class="btn btn-danger w-100 rounded-pill" style="height:40px;" onclick="return confirm('Reject?')"><i class="bx bx-x me-1"></i> Reject</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
