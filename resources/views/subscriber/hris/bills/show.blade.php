@extends('layouts.subscriber')

@section('title', 'Bill Details')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Bill Management</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-receipt text-primary me-1.5 align-middle font-size-26"></i>Bill #{{ $bill->id }}
        </h4>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($bill->status, ['approved', 'modified']))
            <a href="{{ route('subscriber.hris.bills.pdf', $bill) }}" class="btn btn-success rounded-pill px-4" style="height:40px;font-size:0.85rem;">
                <i class="bx bx-download me-1"></i> Invoice PDF
            </a>
        @endif
        <a href="{{ route('subscriber.hris.bills.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- LEFT: Bill Info --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-slate-800 mb-0" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Bill Information
                    </h6>
                    @if($bill->status === 'pending')
                        <span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11"><i class="bx bx-time align-middle me-0.5"></i> Pending Approval</span>
                    @elseif($bill->status === 'approved')
                        <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11"><i class="bx bx-check-circle align-middle me-0.5"></i> Approved</span>
                    @elseif($bill->status === 'rejected')
                        <span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11"><i class="bx bx-x-circle align-middle me-0.5"></i> Rejected</span>
                    @else
                        <span class="badge bg-soft-info text-info px-3 py-1.5 font-size-11"><i class="bx bx-edit align-middle me-0.5"></i> Modified</span>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Bill Type</label>
                        <p class="mb-0 text-dark fw-semibold">{{ $bill->billType?->name ?? '--' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Purpose</label>
                        <p class="mb-0 text-dark fw-semibold">{{ $bill->billPurpose?->name ?? '--' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Amount (BDT)</label>
                        <p class="mb-0 text-dark fw-bold font-size-20">{{ number_format($bill->amount, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Approved Amount (BDT)</label>
                        <p class="mb-0 text-dark fw-bold font-size-20 {{ $bill->approved_amount && $bill->approved_amount != $bill->amount ? 'text-info' : '' }}">
                            {{ $bill->approved_amount ? number_format($bill->approved_amount, 2) : '--' }}
                            @if($bill->approved_amount && $bill->approved_amount != $bill->amount)
                                <small class="font-size-11 text-muted">(modified)</small>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Bill Number</label>
                        <p class="mb-0"><code>{{ $bill->bill_no ?? '--' }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Submitted On</label>
                        <p class="mb-0 text-dark">{{ $bill->created_at->format('d M, Y h:i A') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Description</label>
                        <p class="mb-0 text-dark">{{ $bill->description ?? 'No description provided.' }}</p>
                    </div>
                    @if($bill->voucher_path)
                    <div class="col-12">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Voucher / Receipt</label>
                        <a href="{{ Storage::disk('public')->url($bill->voucher_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bx bx-file me-0.5"></i> View Attachment
                        </a>
                    </div>
                    @endif
                </div>

                @if($bill->action_remarks)
                <div class="mt-4 pt-3 border-top">
                    <label class="form-label fw-semibold text-slate-700 font-size-12">Approval Remarks</label>
                    <p class="mb-0 text-dark">{{ $bill->action_remarks }}</p>
                    <small class="text-muted">By {{ $bill->actionedBy?->name ?? 'System' }}</small>
                </div>
                @endif
            </div>
        </div>

        {{-- Modification History --}}
        @if($bill->modifications->count())
        <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-history text-primary me-1.5 align-middle font-size-18"></i> Modification History
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Original Amount</th>
                                <th>New Amount</th>
                                <th>Reason</th>
                                <th>Modified By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->modifications as $mod)
                            <tr>
                                <td>{{ number_format($mod->original_amount, 2) }} BDT</td>
                                <td class="fw-bold text-info">{{ number_format($mod->new_amount, 2) }} BDT</td>
                                <td>{{ $mod->reason }}</td>
                                <td>{{ $mod->modifier?->name ?? '--' }}</td>
                                <td>{{ $mod->created_at->format('d M, Y h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Employee + Actions --}}
    <div class="col-lg-4">
        {{-- Employee Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-user text-primary me-1.5 align-middle font-size-18"></i> Employee
                </h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($bill->employee?->user?->name ?? 'U') }}&background=f59e0b&color=fff&size=48" class="rounded-circle" width="48" height="48">
                    <div>
                        <div class="fw-bold text-dark">{{ $bill->employee?->user?->name ?? 'N/A' }}</div>
                        <code class="font-size-11 text-muted">{{ $bill->employee?->employee_id }}</code>
                    </div>
                </div>
                <div style="font-size:0.82rem;">
                    <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Department</span><span class="fw-semibold">{{ $bill->employee?->department?->name ?? '--' }}</span></div>
                    <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Designation</span><span class="fw-semibold">{{ $bill->employee?->designation?->title ?? '--' }}</span></div>
                </div>
            </div>
        </div>

        {{-- Actions (for pending bills) --}}
        @if($bill->status === 'pending')
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-cog text-primary me-1.5 align-middle font-size-18"></i> Actions
                </h6>

                {{-- Approve --}}
                <form method="POST" action="{{ route('subscriber.hris.bills.approve', $bill) }}" onsubmit="return confirm('Approve this bill?')">
                    @csrf
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm" name="action_remarks" rows="2" placeholder="Remarks (optional)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill mb-3" style="height:40px;">
                        <i class="bx bx-check me-1"></i> Approve Bill
                    </button>
                </form>

                {{-- Modify Amount --}}
                <form method="POST" action="{{ route('subscriber.hris.bills.modify', $bill) }}" id="modifyForm">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">New Amount (BDT)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="new_amount" value="{{ $bill->amount }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Reason for Modification <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" name="reason" rows="2" placeholder="Why are you modifying the amount?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-info w-100 rounded-pill mb-3 text-white" style="height:40px;" onclick="return confirm('Modify amount and approve?')">
                        <i class="bx bx-edit me-1"></i> Modify & Approve
                    </button>
                </form>

                {{-- Reject --}}
                <form method="POST" action="{{ route('subscriber.hris.bills.reject', $bill) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-slate-700 font-size-12">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" name="action_remarks" rows="2" placeholder="Why is this bill being rejected?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 rounded-pill" style="height:40px;" onclick="return confirm('Reject this bill?')">
                        <i class="bx bx-x me-1"></i> Reject Bill
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
