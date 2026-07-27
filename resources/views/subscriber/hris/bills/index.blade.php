@extends('layouts.subscriber')

@section('title', 'Bills & Expenses')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Bill Management</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-receipt text-primary me-1.5 align-middle font-size-26"></i>Bills & Expenses
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

{{-- Status Tabs --}}
<div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('subscriber.hris.bills.index', ['status' => 'all']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="bx bx-list-ul me-0.5"></i> All
            </a>
            <a href="{{ route('subscriber.hris.bills.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                <i class="bx bx-time me-0.5"></i> Pending
            </a>
            <a href="{{ route('subscriber.hris.bills.index', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="bx bx-check-circle me-0.5"></i> Approved
            </a>
            <a href="{{ route('subscriber.hris.bills.index', ['status' => 'modified']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'modified' ? 'btn-info' : 'btn-outline-secondary' }}">
                <i class="bx bx-edit me-0.5"></i> Modified
            </a>
            <a href="{{ route('subscriber.hris.bills.index', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill px-4 font-size-12 fw-semibold {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-secondary' }}">
                <i class="bx bx-x-circle me-0.5"></i> Rejected
            </a>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search by employee ID, name, or bill number..." style="max-width:350px;">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4"><i class="bx bx-search me-0.5"></i> Search</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Employee</th>
                    <th>Type</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th>Bill No</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($bill->employee?->user?->name ?? 'U') }}&background=f59e0b&color=fff&size=28" class="rounded-circle" width="30" height="30">
                                <div>
                                    <span class="fw-semibold text-slate-800 font-size-13">{{ $bill->employee?->user?->name ?? 'N/A' }}</span>
                                    <code class="font-size-11 text-muted d-block">{{ $bill->employee?->employee_id }}</code>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11">{{ $bill->billType?->name ?? 'N/A' }}</span></td>
                        <td class="font-size-12">{{ $bill->billPurpose?->name ?? '--' }}</td>
                        <td class="fw-bold text-slate-800">{{ number_format($bill->approved_amount ?? $bill->amount, 2) }} <small>BDT</small></td>
                        <td><code class="font-size-11">{{ $bill->bill_no ?? '--' }}</code></td>
                        <td class="font-size-12">{{ $bill->created_at->format('d M, Y') }}</td>
                        <td>
                            @if($bill->status === 'approved')
                                <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11"><i class="bx bx-check-circle align-middle me-0.5"></i> Approved</span>
                            @elseif($bill->status === 'rejected')
                                <span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11"><i class="bx bx-x-circle align-middle me-0.5"></i> Rejected</span>
                            @elseif($bill->status === 'modified')
                                <span class="badge bg-soft-info text-info px-3 py-1.5 font-size-11"><i class="bx bx-edit align-middle me-0.5"></i> Modified</span>
                            @else
                                <span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11"><i class="bx bx-time align-middle me-0.5"></i> Pending</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('subscriber.hris.bills.show', $bill) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11" title="View Details">
                                    <i class="bx bx-show me-0.5"></i>
                                </a>
                                @if($bill->status === 'approved' || $bill->status === 'modified')
                                    <a href="{{ route('subscriber.hris.bills.pdf', $bill) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 font-size-11" title="Download Invoice">
                                        <i class="bx bx-download me-0.5"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bx bx-receipt font-size-40 d-block mb-2"></i>
                            No bills found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($bills->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $bills->firstItem() }}-{{ $bills->lastItem() }} of {{ $bills->total() }}</small>
        <div>{{ $bills->links() }}</div>
    </div>
@endif
@endsection
