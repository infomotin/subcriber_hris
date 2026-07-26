@extends('layouts.subscriber')

@section('title', 'New Increment')

@section('content')
<style>
    .type-card {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
        padding: 1.25rem;
        text-align: center;
    }
    .type-card:hover { border-color: #5f5af6; background: rgba(95,90,246,0.03); }
    .type-card.selected { border-color: #5f5af6; background: rgba(95,90,246,0.06); }
    .type-card .icon { font-size: 2rem; color: #5f5af6; }
    .type-card .title { font-weight: 700; font-size: 0.9rem; margin-top: 0.5rem; }
    .type-card .desc { font-size: 0.75rem; color: #94a3b8; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-trending-up text-primary me-1.5 align-middle font-size-26"></i>New Increment
        </h4>
    </div>
</div>

<form method="POST" action="{{ route('subscriber.hris.increments.store') }}" id="incForm">
    @csrf
    <input type="hidden" name="increment_type" id="increment_type">

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                <i class="bx bx-category-alt text-primary me-1.5 align-middle font-size-20"></i> Step 1: Select Increment Type
            </h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="type-card" data-type="annual" onclick="selectType(this)">
                        <div class="icon bx bx-calendar-check"></div>
                        <div class="title">Annual</div>
                        <div class="desc">Yearly increment, 1+ year gap</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="type-card" data-type="special" onclick="selectType(this)">
                        <div class="icon bx bx-star"></div>
                        <div class="title">Special</div>
                        <div class="desc">Any time, no year gap needed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="type-card" data-type="manual" onclick="selectType(this)">
                        <div class="icon bx bx-user"></div>
                        <div class="title">Manual</div>
                        <div class="desc">Single employee, fixed amount</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="type-card" data-type="bulk" onclick="selectType(this)">
                        <div class="icon bx bx-group"></div>
                        <div class="title">Bulk</div>
                        <div class="desc">By dept/designation, % based</div>
                    </div>
                </div>
            </div>
            @error('increment_type') <div class="text-danger font-size-12 mt-2">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                <i class="bx bx-cog text-primary me-1.5 align-middle font-size-20"></i> Step 2: Configuration
            </h5>

            {{-- Rule + Based On --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Increment Rule</label>
                    <select class="form-select" name="increment_rule_id">
                        <option value="">— No Rule —</option>
                        @foreach($rules as $rule)
                            <option value="{{ $rule->id }}" {{ old('increment_rule_id') == $rule->id ? 'selected' : '' }} data-based="{{ $rule->increment_based_on }}">{{ $rule->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Based On <span class="text-danger">*</span></label>
                    <select class="form-select" name="based_on" id="based_on" required>
                        <option value="basic" {{ old('based_on') === 'basic' ? 'selected' : '' }}>Basic Salary</option>
                        <option value="gross" {{ old('based_on') === 'gross' ? 'selected' : '' }}>Gross Salary</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-slate-700">% <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="increment_percentage" id="inc_pct" value="{{ old('increment_percentage') }}" placeholder="e.g. 5" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-slate-700">Fixed Amount</label>
                    <input type="number" class="form-control" name="increment_amount" id="inc_amount" value="{{ old('increment_amount') }}" placeholder="BDT" step="0.01" min="0">
                </div>
            </div>

            {{-- Bulk: Department / Designation --}}
            <div id="bulkFields" style="display: none;">
                <hr class="my-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department_id">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Designation <small class="text-muted">(optional)</small></label>
                        <select class="form-select" name="designation_id">
                            <option value="">All Designations</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Manual/Annual/Special: Employee search + eligibility --}}
            <div id="singleFields" style="display: none;">
                <hr class="my-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700">Employee</label>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-start-pill" id="empSearch" placeholder="Search by ID or name...">
                            <button type="button" class="btn btn-outline-primary rounded-end-pill" id="empSearchBtn"><i class="bx bx-search"></i></button>
                        </div>
                        <input type="hidden" name="employee_profile_id" id="employee_profile_id">
                        <div id="empResult" class="mt-2" style="display: none;">
                            <div class="p-3 rounded-3 border" style="background: #f8fafc;">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="" id="empAvatar" class="rounded-circle" width="36" height="36">
                                    <div>
                                        <strong id="empName" class="font-size-13"></strong>
                                        <code id="empCode" class="font-size-11 text-muted d-block"></code>
                                        <small class="text-muted" id="empSalaryInfo"></small>
                                    </div>
                                </div>
                                <div id="eligibilityMsg" class="font-size-12 mt-2 p-2 rounded-3" style="display: none;"></div>
                            </div>
                        </div>
                        @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr class="my-3">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        <a href="{{ route('subscriber.hris.increments.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
        <button type="submit" class="btn btn-primary rounded-pill px-5" style="height: 44px;">Create Increment</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function selectType(el) {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('increment_type').value = el.dataset.type;

    const single = document.getElementById('singleFields');
    const bulk = document.getElementById('bulkFields');
    const type = el.dataset.type;

    single.style.display = (type === 'manual' || type === 'annual' || type === 'special') ? 'block' : 'none';
    bulk.style.display = type === 'bulk' ? 'block' : 'none';
    document.getElementById('inc_amount').disabled = type === 'bulk';
    if (type === 'bulk') document.getElementById('inc_amount').value = '';
}

document.getElementById('based_on').addEventListener('change', function() {
    // When rule changes, it may set based_on
});

document.addEventListener('DOMContentLoaded', function() {
    const ruleSelect = document.querySelector('select[name="increment_rule_id"]');
    ruleSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.based) {
            document.getElementById('based_on').value = opt.dataset.based;
        }
    });
});

document.getElementById('empSearchBtn').addEventListener('click', searchEmployee);
document.getElementById('empSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchEmployee(); }
});

function searchEmployee() {
    const q = document.getElementById('empSearch').value.trim();
    if (!q) return;
    const res = document.getElementById('empResult');
    res.style.display = 'none';

    fetch('{{ route("subscriber.hris.increments.employee-search") }}?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) {
                const emp = data[0];
                document.getElementById('employee_profile_id').value = emp.id;
                document.getElementById('empAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=5f5af6&color=fff&size=36';
                document.getElementById('empName').textContent = emp.name;
                document.getElementById('empCode').textContent = emp.employee_id + ' | ' + emp.department + ' / ' + emp.designation;
                document.getElementById('empSalaryInfo').textContent = 'Basic: ' + Number(emp.basic).toLocaleString() + ' | Gross: ' + Number(emp.gross).toLocaleString();
                res.style.display = 'block';

                const type = document.getElementById('increment_type').value;
                if (type === 'annual') {
                    checkEligibility(emp.id);
                } else {
                    document.getElementById('eligibilityMsg').style.display = 'none';
                }
            } else {
                alert('Employee not found');
            }
        });
}

function checkEligibility(id) {
    fetch('{{ route("subscriber.hris.increments.check-eligibility") }}?employee_profile_id=' + id)
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById('eligibilityMsg');
            msg.textContent = data.message;
            msg.className = 'font-size-12 mt-2 p-2 rounded-3 ' + (data.eligible ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger');
            msg.style.display = 'block';
        });
}
</script>
@endpush
