@extends('layouts.subscriber')

@section('title', 'Promotions')

@section('content')
<style>
    .promo-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .promo-badge.merit { background: #dbeafe; color: #1e40af; }
    .promo-badge.seniority { background: #d1fae5; color: #065f46; }
    .promo-badge.departmental { background: #fef3c7; color: #92400e; }
    .promo-badge.positional { background: #ede9fe; color: #5b21b6; }
    .promo-badge.special { background: #fce7f3; color: #9d174d; }
    .promo-letter-btn {
        height: 28px;
        font-size: 0.7rem;
        padding: 0 12px;
        border-radius: 40px;
        min-height: auto;
    }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-trending-up text-primary me-1.5 align-middle font-size-26"></i>Promotions
        </h4>
    </div>
    <div>
        <a href="{{ route('subscriber.hris.promotions.create') }}" class="btn btn-primary rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-plus me-1"></i> New Promotion
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-0">
        <div class="px-4 py-3 border-bottom" style="background: #fafbfc;">
            <form method="GET" action="{{ route('subscriber.hris.promotions.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <input type="text" class="form-control rounded-pill px-4" name="search" placeholder="Search by employee name or ID..." value="{{ request('search') }}" style="height: 38px; background: #fff; font-size: 0.85rem;">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100" style="height: 38px; font-size: 0.85rem;">
                            <i class="bx bx-search me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-lg-2">
                        @if(request('search'))
                            <a href="{{ route('subscriber.hris.promotions.index') }}" class="btn btn-outline-secondary rounded-pill w-100" style="height: 38px; font-size: 0.85rem;">
                                <i class="bx bx-x me-1"></i> Clear
                            </a>
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
                        <th>Previous</th>
                        <th>New</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Ref #</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($promo->employee?->user?->name ?? 'U') }}&background=5f5af6&color=fff&size=28" class="rounded-circle" width="30" height="30">
                                    <div>
                                        <span class="fw-semibold text-slate-800 font-size-13">{{ $promo->employee?->user?->name ?? 'N/A' }}</span>
                                        <code class="font-size-11 text-muted d-block">{{ $promo->employee?->employee_id }}</code>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-size-12 text-muted d-block">{{ $promo->oldDepartment?->name }}</span>
                                <span class="font-size-11 text-muted">{{ $promo->oldDesignation?->title }}</span>
                            </td>
                            <td>
                                <span class="font-size-12 fw-semibold text-success d-block">{{ $promo->newDepartment?->name }}</span>
                                <span class="font-size-11 text-primary">{{ $promo->newDesignation?->title }}</span>
                            </td>
                            <td>
                                <span class="promo-badge {{ $promo->promotion_type }}">{{ $types[$promo->promotion_type] ?? $promo->promotion_type }}</span>
                            </td>
                            <td class="font-size-12">{{ $promo->effective_date?->format('d M Y') }}</td>
                            <td><code class="font-size-11">{{ $promo->reference_number }}</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('subscriber.hris.promotions.show', $promo->id) }}" class="btn btn-sm btn-outline-primary promo-letter-btn">
                                    <i class="bx bx-file me-0.5 align-middle font-size-13"></i> Letter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bx bx-trending-up text-muted font-size-40 d-block mb-3"></i>
                                <p class="text-muted mb-0">No promotions recorded yet.</p>
                                <a href="{{ route('subscriber.hris.promotions.create') }}" class="btn btn-primary rounded-pill mt-3">
                                    <i class="bx bx-plus me-1"></i> Record First Promotion
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($promotions->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $promotions->firstItem() }}–{{ $promotions->lastItem() }} of {{ $promotions->total() }}</small>
        <div>{{ $promotions->links() }}</div>
    </div>
@endif
@endsection
