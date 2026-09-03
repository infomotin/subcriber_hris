@extends('layouts.subscriber')

@section('title', 'Data Verification')

@section('content')
<style>
    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 8px;
        border-radius: 40px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .section-badge.verified { background: #d1fae5; color: #065f46; }
    .section-badge.pending { background: #fef3c7; color: #92400e; }
    .section-badge.expired { background: #fee2e2; color: #991b1b; }
    .verify-btn {
        height: 28px;
        font-size: 0.68rem;
        padding: 0 10px;
        border-radius: 40px;
        min-height: auto;
    }
    .progress-bar-soft { height: 5px; border-radius: 40px; background: #e2e8f0; }
    .progress-bar-soft .bar { height: 100%; border-radius: 40px; background: linear-gradient(90deg, #5f5af6, #8b5cf6); transition: width 0.4s ease; }
    .progress-bar-soft .bar.complete { background: linear-gradient(90deg, #10b981, #059669); }
    .table-verification td, .table-verification th { vertical-align: middle; }
    .emp-cell { min-width: 200px; }
    .section-cell { min-width: 110px; text-align: center; }
    .method-option { cursor: pointer; transition: all 0.15s; }
    .method-option:hover { border-color: #5f5af6 !important; background: rgba(95,90,246,0.03) !important; }
    .dropdown-action .btn { font-size: 0.72rem; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-shield-quarter text-primary me-1.5 align-middle font-size-26"></i>Employee Data Verification
        </h4>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted font-size-12">{{ $employees->total() }} employees</span>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 mb-4" style="background:linear-gradient(135deg,rgba(95,90,246,0.03),rgba(139,92,246,0.03));border:1px solid rgba(95,90,246,0.08);border-radius:16px;">
    <div class="card-body px-4 py-3">
        <form method="GET" action="{{ route('subscriber.hris.general.show', 'verification') }}">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <input type="text" class="form-control rounded-pill px-4" name="search" placeholder="Search by name or ID..." value="{{ request('search') }}" style="height:40px;background:#f8fafc;font-size:0.85rem;">
                </div>
                <div class="col-lg-2">
                    <select class="form-select rounded-pill px-3" name="section" style="height:40px;font-size:0.8rem;" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $key => $label)
                            <option value="{{ $key }}" {{ request('section') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select rounded-pill px-3" name="status_filter" style="height:40px;font-size:0.8rem;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status_filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status_filter') === 'verified' ? 'selected' : '' }}>Fully Verified</option>
                        <option value="expired" {{ request('status_filter') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100" style="height:40px;font-size:0.85rem;">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>
                </div>
                <div class="col-lg-2">
                    @if(request('search') || request('section') || request('status_filter'))
                        <a href="{{ route('subscriber.hris.general.show', 'verification') }}" class="btn btn-outline-secondary rounded-pill w-100" style="height:40px;font-size:0.85rem;">
                            <i class="bx bx-x me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-verification">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="min-width:200px;">Employee</th>
                    <th>Department</th>
                    <th>Designation</th>
                    @foreach($sections as $key => $label)
                        <th class="section-cell">{{ Str::title($key) }}</th>
                    @endforeach
                    <th style="min-width:100px;">Progress</th>
                    <th class="text-center" style="min-width:120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    @php
                        $verifications = $emp->verifications->keyBy('section');
                        $sectionsData = [];
                        $verifiedCount = 0;
                        $totalSections = count($sections);
                        $firstPendingSection = null;
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
                            if ($status !== 'verified' && !$firstPendingSection) {
                                $firstPendingSection = $key;
                            }
                            $sectionsData[$key] = ['record' => $v, 'status' => $status];
                        }
                        $pct = $totalSections > 0 ? (int) round(($verifiedCount / $totalSections) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="ps-4 emp-cell">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'U') }}&background=5f5af6&color=fff&size=32" class="rounded-circle" width="32" height="32">
                                <div>
                                    <span class="fw-semibold text-slate-800 font-size-13 d-block">{{ $emp->user->name ?? 'N/A' }}</span>
                                    <code class="font-size-10 text-muted">{{ $emp->employee_id }}</code>
                                </div>
                            </div>
                        </td>
                        <td class="font-size-12 text-muted">{{ $emp->department->name ?? 'N/A' }}</td>
                        <td class="font-size-12 text-muted">{{ $emp->designation->title ?? 'N/A' }}</td>
                        @foreach($sections as $key => $label)
                            @php $sd = $sectionsData[$key]; @endphp
                            <td class="section-cell">
                                <span class="section-badge {{ $sd['status'] }}">
                                    @if($sd['status'] === 'verified')
                                        <i class="bx bx-check-circle font-size-12"></i>
                                    @elseif($sd['status'] === 'expired')
                                        <i class="bx bx-time-five font-size-12"></i>
                                    @else
                                        <i class="bx bx-hourglass font-size-12"></i>
                                    @endif
                                    {{ ucfirst($sd['status']) }}
                                </span>
                            </td>
                        @endforeach
                        <td>
                            <div style="width:90px;">
                                <div class="d-flex justify-content-between font-size-10 mb-1">
                                    <span class="text-muted">Verified</span>
                                    <strong class="{{ $pct === 100 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger') }}">{{ $pct }}%</strong>
                                </div>
                                <div class="progress-bar-soft">
                                    <div class="bar {{ $pct === 100 ? 'complete' : '' }}" style="width:{{ $pct }}%;"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:40px;font-size:0.72rem;">
                                    <i class="bx bx-dots-horizontal-rounded me-0.5"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px;min-width:180px;box-shadow:0 8px 24px rgba(0,0,0,0.12);">
                                    @foreach($sections as $key => $label)
                                        @php $sd = $sectionsData[$key]; @endphp
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 verify-trigger"
                                                data-employee-id="{{ $emp->id }}"
                                                data-section="{{ $key }}"
                                                data-section-label="{{ $label }}"
                                                data-employee-name="{{ $emp->user->name ?? 'N/A' }}"
                                                data-employee-code="{{ $emp->employee_id }}"
                                                data-verified-by="{{ \App\Models\EmployeeVerification::VERIFIED_BY[$key] ?? 'HR Admin' }}"
                                                data-status="{{ $sd['status'] }}">
                                                @if($sd['status'] === 'verified')
                                                    <i class="bx bx-check-circle text-success font-size-15"></i>
                                                    <span class="font-size-12">{{ $label }} <small class="text-success">— Verified</small></span>
                                                @elseif($sd['status'] === 'expired')
                                                    <i class="bx bx-time-five text-warning font-size-15"></i>
                                                    <span class="font-size-12">{{ $label }} <small class="text-warning">— Expired</small></span>
                                                @else
                                                    <i class="bx bx-shield text-primary font-size-15"></i>
                                                    <span class="font-size-12">{{ $label }} <small class="text-muted">— Pending</small></span>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('subscriber.hris.employees.show', $emp->id) }}">
                                            <i class="bx bx-show font-size-15 text-muted"></i>
                                            <span class="font-size-12">View Employee</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 4 + count($sections) + 2 }}" class="text-center py-5">
                            <i class="bx bx-shield-x text-muted font-size-40 d-block mb-2"></i>
                            <p class="text-muted mb-0">No employees match the current filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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
        <div class="modal-content border-0" style="border-radius:16px;">
            <form method="POST" action="{{ route('subscriber.hris.general.verification.verify') }}" id="verifyForm">
                @csrf
                <input type="hidden" name="employee_id" id="verify_employee_id">
                <input type="hidden" name="section" id="verify_section">
                <input type="hidden" name="verified_by" id="verify_verified_by">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-slate-800" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-shield-quarter text-primary me-1.5 align-middle font-size-22"></i> Verify Section
                        </h5>
                        <p class="text-muted font-size-13 mb-0 mt-1">
                            <span id="modalEmployeeInfo">—</span>
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div id="modalSectionLabel" class="mb-3">
                        <span class="badge bg-primary rounded-pill px-3 py-1 font-size-11">Section: <strong id="modalSectionName">—</strong></span>
                    </div>
                    <label class="form-label fw-semibold text-slate-700 font-size-13">Select verification method:</label>
                    <div class="vstack gap-2" id="methodOptions">
                        @foreach(\App\Models\EmployeeVerification::METHODS as $value => $label)
                        <div class="form-check p-3 rounded-3 border method-option" style="cursor:pointer;transition:all 0.15s;">
                            <input class="form-check-input" type="radio" name="verification_method" id="method_{{ $value }}" value="{{ $value }}" required>
                            <label class="form-check-label fw-semibold text-slate-700 w-100" for="method_{{ $value }}" style="cursor:pointer;">
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

    document.querySelectorAll('.verify-trigger').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const status = this.dataset.status;
            if (status === 'verified') {
                return;
            }

            document.getElementById('verify_employee_id').value = this.dataset.employeeId;
            document.getElementById('verify_section').value = this.dataset.section;
            document.getElementById('verify_verified_by').value = this.dataset.verifiedBy;
            document.getElementById('modalEmployeeInfo').textContent = this.dataset.employeeName + ' (' + this.dataset.employeeCode + ')';
            document.getElementById('modalSectionName').textContent = this.dataset.sectionLabel;

            const radios = document.querySelectorAll('input[name="verification_method"]');
            radios.forEach(r => r.checked = false);

            document.querySelectorAll('.method-option').forEach(o => {
                o.style.borderColor = '#dee2e6';
                o.style.background = '';
            });

            const modal = new bootstrap.Modal(verifyModal);
            modal.show();
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
