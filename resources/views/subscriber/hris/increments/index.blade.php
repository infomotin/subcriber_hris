@extends('layouts.subscriber')

@section('title', 'Increments')

@section('content')
<style>
    .inc-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 10px;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .inc-type-badge.annual { background: #dbeafe; color: #1e40af; }
    .inc-type-badge.special { background: #fce7f3; color: #9d174d; }
    .inc-type-badge.manual { background: #d1fae5; color: #065f46; }
    .inc-type-badge.bulk { background: #fef3c7; color: #92400e; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-trending-up text-primary me-1.5 align-middle font-size-26"></i>Increments
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.increments.enforce') }}" class="btn btn-outline-warning rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-check-shield me-1"></i> Enforce
        </a>
        <a href="{{ route('subscriber.hris.increments.create') }}" class="btn btn-primary rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-plus me-1"></i> New Increment
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-0">
        <div class="px-4 py-3 border-bottom" style="background: #fafbfc;">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <input type="text" class="form-control rounded-pill px-4" name="search" placeholder="Search employee..." value="{{ request('search') }}" style="height: 38px; font-size: 0.85rem;">
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select rounded-pill px-3" name="type" style="height: 38px; font-size: 0.8rem;" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach($types as $k => $l)
                                <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select rounded-pill px-3" name="status" style="height: 38px; font-size: 0.8rem;" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="enforced" {{ request('status') === 'enforced' ? 'selected' : '' }}>Enforced</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100" style="height: 38px; font-size: 0.85rem;"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                    </div>
                    <div class="col-lg-2">
                        @if(request('search') || request('type') || request('status'))
                            <a href="{{ route('subscriber.hris.increments.index') }}" class="btn btn-outline-secondary rounded-pill w-100" style="height: 38px; font-size: 0.85rem;"><i class="bx bx-x me-1"></i> Clear</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Employee</th>
                        <th>Type</th>
                        <th>Before (Basic/Gross)</th>
                        <th>After (Basic/Gross)</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Letter</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($increments as $inc)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($inc->employee?->user?->name ?? 'U') }}&background=5f5af6&color=fff&size=28" class="rounded-circle" width="30" height="30">
                                    <div>
                                        <span class="fw-semibold text-slate-800 font-size-13">{{ $inc->employee?->user?->name ?? 'N/A' }}</span>
                                        <code class="font-size-11 text-muted d-block">{{ $inc->employee?->employee_id }}</code>
                                    </div>
                                </div>
                            </td>
                            <td><span class="inc-type-badge {{ $inc->increment_type }}">{{ $types[$inc->increment_type] ?? $inc->increment_type }}</span></td>
                            <td class="font-size-12">{{ number_format($inc->old_basic, 0) }} / {{ number_format($inc->old_gross, 0) }}</td>
                            <td class="font-size-12 fw-semibold text-success">{{ number_format($inc->new_basic, 0) }} / {{ number_format($inc->new_gross, 0) }}</td>
                            <td class="font-size-12 fw-semibold">{{ number_format($inc->increment_amount, 0) }} <span class="text-muted">({{ $inc->increment_percentage }}%)</span></td>
                            <td>
                                @if($inc->status === 'enforced')
                                    <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">Enforced</span>
                                @else
                                    <span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11">Pending</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($inc->status === 'enforced')
                                    <a href="{{ route('subscriber.hris.increments.letter', $inc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11">
                                        <i class="bx bx-file me-0.5"></i> Letter
                                    </a>
                                @else
                                    <span class="text-muted font-size-11">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No increments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($increments->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $increments->firstItem() }}–{{ $increments->lastItem() }} of {{ $increments->total() }}</small>
        <div>{{ $increments->links() }}</div>
    </div>
@endif
@endsection
