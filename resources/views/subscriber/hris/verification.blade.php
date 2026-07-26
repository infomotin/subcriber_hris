@extends('layouts.subscriber')

@section('title', 'Data Verification')

@section('content')
<style>
    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .section-badge.verified { background: #d1fae5; color: #065f46; }
    .section-badge.pending { background: #fef3c7; color: #92400e; }
    .section-badge.expired { background: #fee2e2; color: #991b1b; }
    .progress-bar-soft {
        height: 6px;
        border-radius: 40px;
        background: #e2e8f0;
    }
    .progress-bar-soft .bar {
        height: 100%;
        border-radius: 40px;
        background: linear-gradient(90deg, #5f5af6, #8b5cf6);
        transition: width 0.4s ease;
    }
    .progress-bar-soft .bar.complete { background: linear-gradient(90deg, #10b981, #059669); }
    .verify-checkbox:checked + .btn-outline-primary {
        background: var(--color-primary) !important;
        color: #fff !important;
        border-color: var(--color-primary) !important;
    }
    .verify-btn {
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
            <i class="bx bx-shield-quarter text-primary me-1.5 align-middle font-size-26"></i>Employee Data Verification
        </h4>
    </div>
    <div>
        <span class="text-muted font-size-12">{{ $employees->total() }} employees</span>
    </div>
</div>

<div class="card border-0 mb-4" style="background: linear-gradient(135deg, rgba(95,90,246,0.03), rgba(139,92,246,0.03)); border: 1px solid rgba(95,90,246,0.08); border-radius: 16px;">
    <div class="card-body px-4 py-3">
        <form method="GET" action="{{ route('subscriber.hris.general.show', 'verification') }}" id="filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <input type="text" class="form-control rounded-pill px-4" name="search" placeholder="Search by name or ID..." value="{{ request('search') }}" style="height: 40px; background: #f8fafc; font-size: 0.85rem;">
                </div>
                <div class="col-lg-2">
                    <select class="form-select rounded-pill px-3" name="section" style="height: 40px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $key => $label)
                            <option value="{{ $key }}" {{ request('section') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select rounded-pill px-3" name="status_filter" style="height: 40px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status_filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status_filter') === 'verified' ? 'selected' : '' }}>Fully Verified</option>
                        <option value="expired" {{ request('status_filter') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100" style="height: 40px; font-size: 0.85rem;">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>
                </div>
                <div class="col-lg-2">
                    @if(request('search') || request('section') || request('status_filter'))
                        <a href="{{ route('subscriber.hris.general.show', 'verification') }}" class="btn btn-outline-secondary rounded-pill w-100" style="height: 40px; font-size: 0.85rem;">
                            <i class="bx bx-x me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@forelse($employees as $emp)
    @php
        $verifications = $emp->verifications->keyBy('section');
        $sectionsData = [];
        $verifiedCount = 0;
        $totalSections = count($sections);
        foreach (array_keys($sections) as $key) {
            $v = $verifications->get($key);
            $status = 'pending';
            if ($v) {
                if ($v->status === 'verified' && (!$v->expires_at || $v->expires_at->isFuture())) {
                    $status = 'verified';
                    $verifiedCount++;
                } elseif ($v->expires_at && $v->expires_at->isPast()) {
                    $status = 'expired';
                }
            }
            $sectionsData[$key] = ['record' => $v, 'status' => $status];
        }
        $pct = $totalSections > 0 ? (int) round(($verifiedCount / $totalSections) * 100) : 0;
    @endphp
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px;">
        <div class="card-body p-0">
            <div class="p-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'U') }}&background=5f5af6&color=fff&size=40" class="rounded-circle border" width="44" height="44">
                        <div>
                            <h6 class="fw-bold mb-0.5 text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $emp->user->name ?? 'N/A' }}</h6>
                            <code class="font-size-11 text-muted">{{ $emp->employee_id }}</code>
                            <span class="text-muted mx-1">|</span>
                            <span class="font-size-12 text-muted">{{ $emp->department->name ?? 'N/A' }} / {{ $emp->designation->title ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 120px;">
                            <div class="d-flex justify-content-between font-size-11 mb-1">
                                <span class="text-muted">Verified</span>
                                <strong class="{{ $pct === 100 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger') }}">{{ $pct }}%</strong>
                            </div>
                            <div class="progress-bar-soft">
                                <div class="bar {{ $pct === 100 ? 'complete' : '' }}" style="width: {{ $pct }}%;"></div>
                            </div>
                        </div>
                        @if($pct === 100)
                            <span class="badge bg-soft-success text-success rounded-pill px-3 font-size-11 fw-bold py-1.5">
                                <i class="bx bx-badge-check align-middle me-0.5 font-size-15"></i> Complete
                            </span>
                        @elseif($pct > 0)
                            <span class="badge bg-soft-warning text-warning rounded-pill px-3 font-size-11 fw-bold py-1.5">Partial</span>
                        @else
                            <span class="badge bg-soft-danger text-danger rounded-pill px-3 font-size-11 fw-bold py-1.5">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-4 py-3">
                <div class="row g-2">
                    @foreach($sections as $key => $label)
                        @php $sd = $sectionsData[$key]; @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border" style="background: #fafbfc;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="section-badge {{ $sd['status'] }}">
                                        @if($sd['status'] === 'verified')
                                            <i class="bx bx-check-circle font-size-13"></i>
                                        @elseif($sd['status'] === 'expired')
                                            <i class="bx bx-time-five font-size-13"></i>
                                        @else
                                            <i class="bx bx-hourglass font-size-13"></i>
                                        @endif
                                        {{ ucfirst($sd['status']) }}
                                    </span>
                                    <span class="font-size-12 text-slate-700 fw-semibold">{{ $label }}</span>
                                </div>
                                <div>
                                    @if($sd['status'] === 'verified')
                                        <span class="font-size-10 text-muted">
                                            @if($sd['record'] && $sd['record']->verified_by)
                                                {{ $sd['record']->verified_by }}
                                            @endif
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary verify-btn"
                                            data-bs-toggle="modal" data-bs-target="#verifyModal"
                                            data-employee-id="{{ $emp->id }}"
                                            data-section="{{ $key }}"
                                            data-verified-by="{{ \App\Models\EmployeeVerification::VERIFIED_BY[$key] ?? 'HR Admin' }}">
                                            <i class="bx bx-shield me-0.5 font-size-13 align-middle"></i> Verify Now
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-body text-center py-5">
            <i class="bx bx-shield-x text-muted font-size-40 d-block mb-3"></i>
            <p class="text-muted mb-0">No employees match the current filters.</p>
        </div>
    </div>
@endforelse

@if($employees->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $employees->firstItem() }}–{{ $employees->lastItem() }} of {{ $employees->total() }}</small>
        <div>{{ $employees->links() }}</div>
    </div>
@endif
@endsection

@section('modals')
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px;">
            <form method="POST" action="{{ route('subscriber.hris.general.verification.verify') }}" id="verifyForm">
                @csrf
                <input type="hidden" name="employee_id" id="verify_employee_id">
                <input type="hidden" name="section" id="verify_section">
                <input type="hidden" name="verified_by" id="verify_verified_by">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-shield-quarter text-primary me-1.5 align-middle font-size-22"></i> Verify Section
                        </h5>
                        <p class="text-muted font-size-13 mb-0 mt-1">Select the verification method used to confirm this data.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="vstack gap-2" id="methodOptions">
                        @foreach(\App\Models\EmployeeVerification::METHODS as $value => $label)
                        <div class="form-check p-3 rounded-3 border method-option" style="cursor: pointer; transition: all 0.15s;">
                            <input class="form-check-input" type="radio" name="verification_method" id="method_{{ $value }}" value="{{ $value }}" required>
                            <label class="form-check-label fw-semibold text-slate-700 w-100" for="method_{{ $value }}" style="cursor: pointer;">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bx bx-shield me-1.5 align-middle font-size-16"></i> Confirm Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const verifyModal = document.getElementById('verifyModal');
    if (!verifyModal) return;

    verifyModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('verify_employee_id').value = button.dataset.employeeId;
        document.getElementById('verify_section').value = button.dataset.section;
        document.getElementById('verify_verified_by').value = button.dataset.verifiedBy;

        const radios = document.querySelectorAll('input[name="verification_method"]');
        radios.forEach(r => r.checked = false);

        const options = document.querySelectorAll('.method-option');
        options.forEach(o => {
            o.style.borderColor = '#dee2e6';
            o.style.background = '';
        });
    });

    document.querySelectorAll('.method-option').forEach(function(option) {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            document.querySelectorAll('.method-option').forEach(o => {
                o.style.borderColor = '#dee2e6';
                o.style.background = '';
            });
            this.style.borderColor = '#5f5af6';
            this.style.background = 'rgba(95,90,246,0.04)';
        });
    });
});
</script>
@endpush