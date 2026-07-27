@extends('layouts.subscriber')

@section('title', 'Bill Approval')

@section('content')
<style>
    .dept-card { border-radius:14px; border:1px solid #e2e8f0; background:#fff; margin-bottom:1rem; overflow:hidden; }
    .dept-header { padding:1rem 1.25rem; cursor:pointer; display:flex; align-items:center; justify-content:space-between; background:#f8fafc; transition:background 0.2s; }
    .dept-header:hover { background:#f1f5f9; }
    .dept-header .dept-name { font-weight:700; font-size:0.95rem; color:#0f172a; }
    .dept-header .badge { font-size:0.7rem; }
    .dept-body { display:none; border-top:1px solid #e2e8f0; }
    .dept-body.show { display:block; }
    .bill-row { padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:1rem; }
    .bill-row:last-child { border-bottom:none; }
    .bill-amount { font-weight:800; font-size:1.1rem; color:#0f172a; white-space:nowrap; }
    .bill-meta { font-size:0.78rem; color:#64748b; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Bill Management</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-clipboard text-primary me-1.5 align-middle font-size-26"></i>Bill Approval Dashboard
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.bills.apply') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-plus me-1"></i> New Bill
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($bills->isEmpty())
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body text-center py-5">
            <i class="bx bx-check-circle font-size-48 text-success d-block mb-2"></i>
            <h5 class="fw-bold text-slate-800">All Caught Up!</h5>
            <p class="text-muted mb-0">No pending bills to review.</p>
        </div>
    </div>
@endif

@foreach($bills as $dept => $deptBills)
    <div class="dept-card">
        <div class="dept-header" onclick="toggleDept(this)">
            <div class="d-flex align-items-center gap-3">
                <i class="bx bx-building font-size-22 text-primary"></i>
                <div>
                    <div class="dept-name">{{ $dept }}</div>
                    <div class="bill-meta">{{ $deptBills->count() }} pending bill{{ $deptBills->count() > 1 ? 's' : '' }} &middot; {{ number_format($deptBills->sum('amount'), 2) }} BDT total</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-warning text-warning">{{ $deptBills->count() }}</span>
                <i class="bx bx-chevron-down font-size-20 text-muted dept-chevron"></i>
            </div>
        </div>
        <div class="dept-body">
            @foreach($deptBills as $bill)
                <div class="bill-row">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($bill->employee?->user?->name ?? 'U') }}&background=f59e0b&color=fff&size=36" class="rounded-circle" width="38" height="38">
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-slate-800 font-size-13">{{ $bill->employee?->user?->name ?? 'N/A' }}</div>
                        <div class="bill-meta">
                            <code class="font-size-10">{{ $bill->employee?->employee_id }}</code> &middot;
                            {{ $bill->billType?->name ?? '--' }} &middot;
                            {{ $bill->billPurpose?->name ?? '--' }}
                            @if($bill->bill_no) &middot; <code class="font-size-10">{{ $bill->bill_no }}</code> @endif
                            @if($bill->description) &middot; <span title="{{ $bill->description }}">{{ Str::limit($bill->description, 40) }}</span> @endif
                        </div>
                        @if($bill->voucher_path)
                            <a href="{{ Storage::disk('public')->url($bill->voucher_path) }}" target="_blank" class="font-size-11 text-primary"><i class="bx bx-link-external me-0.5"></i>Voucher</a>
                        @endif
                    </div>
                    <div class="bill-amount">{{ number_format($bill->amount, 2) }} <small class="text-muted">BDT</small></div>
                    <div class="d-flex gap-1">
                        <form method="POST" action="{{ route('subscriber.hris.bills.approve', $bill) }}" onsubmit="return confirm('Approve?')">
                            @csrf
                            <input type="hidden" name="action_remarks" value="Approved">
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3 font-size-11"><i class="bx bx-check me-0.5"></i> Approve</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 font-size-11 text-white" onclick="showModifyModal({{ $bill->id }}, {{ $bill->amount }})">
                            <i class="bx bx-edit me-0.5"></i> Modify
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11" onclick="showRejectModal({{ $bill->id }})">
                            <i class="bx bx-x me-0.5"></i> Reject
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-2 px-4 pt-4">
                    <h5 class="fw-bold text-slate-800" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-x-circle text-danger me-1.5 align-middle"></i> Reject Bill
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label class="form-label fw-semibold text-slate-700">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="action_remarks" rows="3" placeholder="Why is this bill being rejected?" required></textarea>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modify Modal --}}
<div class="modal fade" id="modifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;">
            <form method="POST" id="modifyForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-2 px-4 pt-4">
                    <h5 class="fw-bold text-slate-800" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-edit text-info me-1.5 align-middle"></i> Modify Amount
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Original Amount (BDT)</label>
                        <input type="text" class="form-control bg-light" id="modifyOriginal" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">New Amount (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="new_amount" id="modifyNewAmount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Reason for Modification <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Why are you modifying this amount?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 text-white">Modify & Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDept(header) {
    const body = header.nextElementSibling;
    const chevron = header.querySelector('.dept-chevron');
    body.classList.toggle('show');
    chevron.style.transform = body.classList.contains('show') ? 'rotate(180deg)' : '';
}

function showRejectModal(billId) {
    document.getElementById('rejectForm').action = '{{ route("subscriber.hris.bills.reject", "PLACEHOLDER") }}'.replace('PLACEHOLDER', billId);
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showModifyModal(billId, amount) {
    document.getElementById('modifyForm').action = '{{ route("subscriber.hris.bills.modify", "PLACEHOLDER") }}'.replace('PLACEHOLDER', billId);
    document.getElementById('modifyOriginal').value = parseFloat(amount).toFixed(2) + ' BDT';
    document.getElementById('modifyNewAmount').value = amount;
    new bootstrap.Modal(document.getElementById('modifyModal')).show();
}
</script>
@endpush
