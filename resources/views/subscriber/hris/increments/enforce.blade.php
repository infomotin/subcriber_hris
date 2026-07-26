@extends('layouts.subscriber')

@section('title', 'Enforce Increments')

@section('content')
<style>
    .salary-compare {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
    }
    .salary-compare .old { color: #dc2626; text-decoration: line-through; }
    .salary-compare .new { color: #059669; font-weight: 700; }
    .salary-compare .arrow { color: #94a3b8; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-check-shield text-primary me-1.5 align-middle font-size-26"></i>Enforce Increments
        </h4>
        <p class="text-muted font-size-12 mb-0 mt-1">Pending increments awaiting enforcement. Once enforced, this action cannot be undone.</p>
    </div>
</div>

@if($increments->isEmpty())
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-body text-center py-5">
            <i class="bx bx-check-circle text-success font-size-48 d-block mb-3"></i>
            <p class="text-muted mb-0">All increments have been enforced. No pending items.</p>
        </div>
    </div>
@else
    <form method="POST" action="{{ route('subscriber.hris.increments.do-enforce') }}" id="enforceForm" onsubmit="return confirm('Are you sure? Enforced increments CANNOT be undone. This will update employee salaries permanently.')">
        @csrf
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #fafbfc;">
                    <div>
                        <span class="fw-semibold text-slate-700">{{ $increments->count() }} pending increment(s)</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="toggleAll()">
                            <i class="bx bx-select-multiple me-1"></i> Toggle All
                        </button>
                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-4">
                            <i class="bx bx-check-shield me-1"></i> Enforce Selected
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="ps-4">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                </th>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>Increment</th>
                                <th class="text-end pe-4">Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($increments as $inc)
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" name="ids[]" value="{{ $inc->id }}" class="inc-check">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($inc->employee?->user?->name ?? 'U') }}&background=5f5af6&color=fff&size=28" class="rounded-circle" width="30" height="30">
                                            <div>
                                                <span class="fw-semibold text-slate-800 font-size-13">{{ $inc->employee?->user?->name ?? 'N/A' }}</span>
                                                <code class="font-size-11 text-muted d-block">{{ $inc->employee?->employee_id }}</code>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11 text-uppercase">{{ $inc->increment_type }}</span></td>
                                    <td class="font-size-12 text-danger">
                                        <span class="fw-semibold">{{ number_format($inc->old_basic, 0) }}</span> / <span class="fw-semibold">{{ number_format($inc->old_gross, 0) }}</span>
                                    </td>
                                    <td class="font-size-12 text-success">
                                        <span class="fw-bold">{{ number_format($inc->new_basic, 0) }}</span> / <span class="fw-bold">{{ number_format($inc->new_gross, 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-slate-800 font-size-13">{{ number_format($inc->increment_amount, 0) }}</span>
                                        <span class="text-muted font-size-11">({{ $inc->increment_percentage }}%)</span>
                                    </td>
                                    <td class="text-end pe-4"><code class="font-size-11">{{ $inc->reference_number }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endif
@endsection

@push('scripts')
<script>
function toggleAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.inc-check').forEach(c => c.checked = checked);
}
</script>
@endpush
