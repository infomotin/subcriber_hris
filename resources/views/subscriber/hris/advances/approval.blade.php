@extends('layouts.subscriber')
@section('title', 'Advance Approval')
@section('content')
<style>.dept-card{border-radius:14px;border:1px solid #e2e8f0;background:#fff;margin-bottom:1rem;overflow:hidden}.dept-header{padding:1rem 1.25rem;cursor:pointer;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;transition:background 0.2s}.dept-header:hover{background:#f1f5f9}.dept-body{display:none;border-top:1px solid #e2e8f0}.dept-body.show{display:block}.bill-row{padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:1rem}.bill-row:last-child{border-bottom:none}.bill-amount{font-weight:800;font-size:1.1rem;color:#0f172a;white-space:nowrap}.bill-meta{font-size:0.78rem;color:#64748b}</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div><span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Salary Advances</span><h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-clipboard text-primary me-1.5 align-middle font-size-26"></i>Advance Approval Dashboard</h4></div>
    <a href="{{ route('subscriber.hris.advances.apply') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-plus me-1"></i> New Advance</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show rounded-pill px-4"><i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

@if($advances->isEmpty())<div class="card border-0 shadow-sm" style="border-radius:14px;"><div class="card-body text-center py-5"><i class="bx bx-check-circle font-size-48 text-success d-block mb-2"></i><h5 class="fw-bold text-slate-800">All Caught Up!</h5><p class="text-muted mb-0">No pending advances to review.</p></div></div>@endif

@foreach($advances as $dept => $deptAdvances)
<div class="dept-card">
    <div class="dept-header" onclick="toggleDept(this)">
        <div class="d-flex align-items-center gap-3"><i class="bx bx-building font-size-22 text-primary"></i><div><div class="fw-bold font-size-14">{{ $dept }}</div><div class="bill-meta">{{ $deptAdvances->count() }} pending &middot; {{ number_format($deptAdvances->sum('amount'), 2) }} BDT total</div></div></div>
        <div class="d-flex align-items-center gap-2"><span class="badge bg-soft-warning text-warning">{{ $deptAdvances->count() }}</span><i class="bx bx-chevron-down font-size-20 text-muted dept-chevron"></i></div>
    </div>
    <div class="dept-body">
        @foreach($deptAdvances as $a)
        <div class="bill-row">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($a->employee?->user?->name ?? 'U') }}&background=10b981&color=fff&size=36" class="rounded-circle" width="38" height="38">
            <div class="flex-grow-1">
                <div class="fw-semibold text-slate-800 font-size-13">{{ $a->employee?->user?->name ?? 'N/A' }} <code class="font-size-10">{{ $a->employee?->employee_id }}</code></div>
                <div class="bill-meta">{{ $a->advanceType?->name ?? '--' }} &middot; {{ $a->advanceSource?->name ?? '--' }} &middot; {{ $a->installments }}x installments
                    @if($a->referenceEmployee) &middot; Ref: {{ $a->referenceEmployee?->user?->name }} @endif
                    @if($a->reason) &middot; <span title="{{ $a->reason }}">{{ Str::limit($a->reason, 40) }}</span> @endif
                </div>
            </div>
            <div class="bill-amount">{{ number_format($a->amount, 2) }} <small class="text-muted">BDT</small></div>
            <div class="d-flex gap-1">
                <form method="POST" action="{{ route('subscriber.hris.advances.approve', $a) }}" onsubmit="return confirm('Approve?')">@csrf<input type="hidden" name="action_remarks" value="Approved"><button class="btn btn-sm btn-outline-success rounded-pill px-3 font-size-11"><i class="bx bx-check me-0.5"></i> Approve</button></form>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11" onclick="showRejectModal({{ $a->id }})"><i class="bx bx-x me-0.5"></i> Reject</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0" style="border-radius:16px;"><form method="POST" id="rejectForm">@csrf
    <div class="modal-header border-bottom-0 pb-2 px-4 pt-4"><h5 class="fw-bold text-slate-800"><i class="bx bx-x-circle text-danger me-1.5 align-middle"></i> Reject Advance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body px-4 py-3"><label class="form-label fw-semibold text-slate-700">Reason <span class="text-danger">*</span></label><textarea class="form-control" name="action_remarks" rows="3" placeholder="Why is this being rejected?" required></textarea></div>
    <div class="modal-footer border-top-0 px-4 pb-4 pt-0"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger rounded-pill px-4">Reject</button></div>
</form></div></div></div>
@endsection

@push('scripts')
<script>
function toggleDept(h){const b=h.nextElementSibling,c=h.querySelector('.dept-chevron');b.classList.toggle('show');c.style.transform=b.classList.contains('show')?'rotate(180deg)':'';}
function showRejectModal(id){document.getElementById('rejectForm').action='{{ route("subscriber.hris.advances.reject","PLACEHOLDER") }}'.replace('PLACEHOLDER',id);new bootstrap.Modal(document.getElementById('rejectModal')).show();}
</script>
@endpush
