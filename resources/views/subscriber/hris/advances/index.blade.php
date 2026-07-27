@extends('layouts.subscriber')
@section('title', 'Advances')
@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div><span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Salary Advances</span><h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-dollar text-primary me-1.5 align-middle font-size-26"></i>Salary Advances</h4></div>
    <a href="{{ route('subscriber.hris.advances.apply') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-plus me-1"></i> New Advance</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show rounded-pill px-4"><i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
<div class="card border-0 shadow-sm mb-3" style="border-radius:14px;"><div class="card-body p-3"><div class="d-flex flex-wrap gap-2">
    <a href="{{ route('subscriber.hris.advances.index', ['status' => 'all']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}"><i class="bx bx-list-ul me-0.5"></i> All</a>
    <a href="{{ route('subscriber.hris.advances.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}"><i class="bx bx-time me-0.5"></i> Pending</a>
    <a href="{{ route('subscriber.hris.advances.index', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}"><i class="bx bx-check-circle me-0.5"></i> Approved</a>
    <a href="{{ route('subscriber.hris.advances.index', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-secondary' }}"><i class="bx bx-x-circle me-0.5"></i> Rejected</a>
</div></div></div>
<div class="card border-0 shadow-sm mb-3" style="border-radius:14px;"><div class="card-body p-3"><form method="GET" class="d-flex gap-2"><input type="hidden" name="status" value="{{ $status }}"><input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search employee..." style="max-width:350px;"><button type="submit" class="btn btn-sm btn-primary rounded-pill px-4"><i class="bx bx-search me-0.5"></i> Search</button></form></div></div>
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-4">Employee</th><th>Type</th><th>Amount</th><th>Installments</th><th>Source</th><th>Status</th><th class="text-end pe-4">Actions</th></tr></thead>
            <tbody>
                @forelse($advances as $a)
                <tr>
                    <td class="ps-4"><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name={{ urlencode($a->employee?->user?->name ?? 'U') }}&background=10b981&color=fff&size=28" class="rounded-circle" width="30" height="30"><div><span class="fw-semibold text-slate-800 font-size-13">{{ $a->employee?->user?->name ?? 'N/A' }}</span><code class="font-size-11 text-muted d-block">{{ $a->employee?->employee_id }}</code></div></div></td>
                    <td><span class="badge bg-soft-{{ $a->advanceType?->payment_mode === 'one_time' ? 'info' : 'warning' }} text-{{ $a->advanceType?->payment_mode === 'one_time' ? 'info' : 'warning' }} px-3 py-1.5 font-size-11">{{ $a->advanceType?->name ?? 'N/A' }}</span></td>
                    <td class="fw-bold">{{ number_format($a->approved_amount ?? $a->amount, 2) }} <small>BDT</small></td>
                    <td class="font-size-12">{{ $a->installments }}x {{ number_format($a->monthly_deduction, 2) }}/mo</td>
                    <td class="font-size-12">{{ $a->advanceSource?->name ?? '--' }}</td>
                    <td>@if($a->status==='approved')<span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11"><i class="bx bx-check-circle align-middle me-0.5"></i> Approved</span>@elseif($a->status==='rejected')<span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11"><i class="bx bx-x-circle align-middle me-0.5"></i> Rejected</span>@else<span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11"><i class="bx bx-time align-middle me-0.5"></i> Pending</span>@endif</td>
                    <td class="text-end pe-4"><a href="{{ route('subscriber.hris.advances.show', $a) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11"><i class="bx bx-show me-0.5"></i></a></td>
                </tr>
                @empty<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bx bx-dollar font-size-40 d-block mb-2"></i> No advances found.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@if($advances->hasPages())<div class="d-flex justify-content-between align-items-center mt-3"><small class="text-muted">Showing {{ $advances->firstItem() }}-{{ $advances->lastItem() }} of {{ $advances->total() }}</small><div>{{ $advances->links() }}</div></div>@endif
@endsection
