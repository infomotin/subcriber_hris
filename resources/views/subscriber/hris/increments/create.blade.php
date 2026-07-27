@extends('layouts.subscriber')

@section('title', 'New Increment')

@section('content')
<style>
    .emp-info-card { border-radius:14px; overflow:hidden; border:1px solid #e2e8f0; background:#fff; }
    .emp-info-card .emp-header { background:linear-gradient(135deg,#5f5af6 0%,#7c3aed 100%); padding:1.5rem; text-align:center; color:#fff; }
    .emp-info-card .emp-photo { width:80px; height:80px; border-radius:50%; border:3px solid rgba(255,255,255,0.4); object-fit:cover; margin-bottom:0.75rem; }
    .emp-info-card .emp-name { font-size:1.05rem; font-weight:700; margin-bottom:0.15rem; }
    .emp-info-card .emp-id { font-size:0.75rem; opacity:0.8; }
    .emp-info-card .info-row { display:flex; justify-content:space-between; padding:0.6rem 1rem; border-bottom:1px solid #f1f5f9; font-size:0.82rem; }
    .emp-info-card .info-row:last-child { border-bottom:none; }
    .emp-info-card .info-label { color:#94a3b8; font-weight:500; }
    .emp-info-card .info-value { color:#1e293b; font-weight:600; text-align:right; }
    .type-pill { border:2px solid #e2e8f0; border-radius:10px; padding:0.6rem 1rem; cursor:pointer; transition:all 0.2s; text-align:center; font-size:0.8rem; font-weight:600; }
    .type-pill:hover { border-color:#5f5af6; background:rgba(95,90,246,0.03); }
    .type-pill.selected { border-color:#5f5af6; background:rgba(95,90,246,0.06); color:#5f5af6; }
    .emp-search-card { overflow:visible !important; }
    #empDropdown { z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,0.12); }
    .emp-dd-item:hover { background:#f1f5f9; }
    .salary-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:0.75rem 1rem; }
    .salary-box .label { font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.04em; }
    .salary-box .value { font-size:1.1rem; font-weight:700; color:#0f172a; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-trending-up text-primary me-1.5 align-middle font-size-26"></i>New Increment
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.increments.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> All Increments
    </a>
</div>

<form method="POST" action="{{ route('subscriber.hris.increments.store') }}" id="incForm">
    @csrf

    {{-- Employee Selector --}}
    <div class="card border-0 shadow-sm mb-4 emp-search-card" style="border-radius:14px;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-user me-1 text-primary"></i> Select Employee <span class="text-danger">*</span>
                    </label>
                    <div class="position-relative" id="empSearchWrapper">
                        <input type="text" class="form-control" id="empSearchInput" placeholder="Search by name or employee ID..." autocomplete="off">
                        <input type="hidden" name="employee_profile_id" id="employee_profile_id">
                        <div id="empDropdown" class="position-absolute w-100 shadow-sm border rounded-bottom d-none" style="max-height:280px;overflow-y:auto;background:#fff;"></div>
                    </div>
                    @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-package me-1 text-primary"></i> Increment Type <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <div class="type-pill flex-fill selected" data-type="manual" onclick="selectType(this)">
                            <i class="bx bx-user d-block font-size-18 mb-1"></i> Manual
                        </div>
                        <div class="type-pill flex-fill" data-type="annual" onclick="selectType(this)">
                            <i class="bx bx-calendar d-block font-size-18 mb-1"></i> Annual
                        </div>
                        <div class="type-pill flex-fill" data-type="special" onclick="selectType(this)">
                            <i class="bx bx-star d-block font-size-18 mb-1"></i> Special
                        </div>
                        <div class="type-pill flex-fill" data-type="bulk" onclick="selectType(this)">
                            <i class="bx bx-group d-block font-size-18 mb-1"></i> Bulk
                        </div>
                    </div>
                    <input type="hidden" name="increment_type" id="increment_type" value="manual">
                    @error('increment_type') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Main Two Column Layout --}}
    <div id="mainSection" style="display:none;">
        <div class="row g-4">
            {{-- LEFT: Employee Info --}}
            <div class="col-lg-4">
                <div class="emp-info-card shadow-sm">
                    <div class="emp-header">
                        <img id="empPhoto" class="emp-photo" src="" style="display:none;">
                        <div id="empPhotoPlaceholder" class="emp-photo d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.2);display:flex !important;">
                            <i class="bx bx-user font-size-32"></i>
                        </div>
                        <div class="emp-name" id="empName">--</div>
                        <div class="emp-id" id="empCode">--</div>
                    </div>
                    <div class="p-0">
                        <div class="info-row"><span class="info-label">Department</span><span class="info-value" id="empDept">--</span></div>
                        <div class="info-row"><span class="info-label">Designation</span><span class="info-value" id="empDesg">--</span></div>
                        <div class="info-row"><span class="info-label">Joining Date</span><span class="info-value" id="empJoin">--</span></div>
                        <div class="info-row"><span class="info-label">Status</span><span class="info-value" id="empStatus">--</span></div>
                    </div>
                </div>

                {{-- Salary Box --}}
                <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;" id="salaryCard">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-money text-primary me-1.5 align-middle font-size-18"></i> Current Salary
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="salary-box text-center">
                                    <div class="label">Basic</div>
                                    <div class="value" id="curBasic">--</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="salary-box text-center">
                                    <div class="label">Gross</div>
                                    <div class="value" id="curGross">--</div>
                                </div>
                            </div>
                        </div>
                        <div id="eligibilityMsg" class="font-size-12 mt-3 p-2 rounded-3" style="display:none;"></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Increment Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Increment Details
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">Increment Rule</label>
                                <select class="form-select" name="increment_rule_id" id="ruleSelect">
                                    <option value="">-- No Rule --</option>
                                    @foreach($rules as $rule)
                                        <option value="{{ $rule->id }}" data-based="{{ $rule->increment_based_on }}">{{ $rule->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">Based On <span class="text-danger">*</span></label>
                                <select class="form-select" name="based_on" id="based_on" required>
                                    <option value="basic">Basic Salary</option>
                                    <option value="gross">Gross Salary</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">Notes</label>
                                <input type="text" class="form-control" name="notes" placeholder="Optional" value="{{ old('notes') }}">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Amount Row --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Increment Percentage (%)</label>
                                <input type="number" class="form-control" name="increment_percentage" id="inc_pct" value="{{ old('increment_percentage') }}" placeholder="e.g. 5" step="0.01" min="0" max="100">
                                <small class="text-muted">Percentage of basic/gross salary</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Fixed Amount (BDT)</label>
                                <input type="number" class="form-control" name="increment_amount" id="inc_amount" value="{{ old('increment_amount') }}" placeholder="e.g. 5000" step="0.01" min="0">
                                <small class="text-muted">Override percentage with fixed amount</small>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div id="previewBox" class="mt-4 p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">New Basic</small>
                                    <strong id="newBasic" class="text-success">--</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Increment</small>
                                    <strong id="incPreview" class="text-primary">--</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">New Gross</small>
                                    <strong id="newGross" class="text-success">--</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Bulk Fields --}}
                        <div id="bulkFields" style="display:none;" class="mt-3">
                            <hr class="my-3">
                            <h6 class="fw-bold text-slate-800 mb-3">Bulk Target</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-slate-700">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" name="department_id">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-slate-700">Designation <small class="text-muted">(optional)</small></label>
                                    <select class="form-select" name="designation_id">
                                        <option value="">All Designations</option>
                                        @foreach($designations as $desig)
                                            <option value="{{ $desig->id }}">{{ $desig->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('subscriber.hris.increments.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
                                <i class="bx bx-save me-1.5 align-middle font-size-18"></i> Create Increment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@if ($errors->any())
    <div class="alert alert-danger rounded-pill px-4 mt-3">
        <i class="bx bx-error-circle me-1 align-middle"></i>
        @foreach ($errors->all() as $error) {{ $error }} @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
const EMPLOYEE_SEARCH_URL = '{{ route("subscriber.hris.increments.employee-search") }}';
const CHECK_ELIGIBILITY_URL = '{{ route("subscriber.hris.increments.check-eligibility") }}';

const employees = @json($employees);

let selectedEmp = null;

// Searchable dropdown
const searchInput = document.getElementById('empSearchInput');
const hiddenSelect = document.getElementById('employee_profile_id');
const dropdown = document.getElementById('empDropdown');

function renderDropdown(query) {
    const q = (query || '').toLowerCase().trim();
    const matches = q ? employees.filter(e => e.name.toLowerCase().includes(q) || e.emp_id.toLowerCase().includes(q)) : employees;
    dropdown.innerHTML = matches.length === 0
        ? '<div class="px-3 py-2 text-muted font-size-12">No employees found</div>'
        : matches.map(e => `<div class="emp-dd-item px-3 py-2 font-size-13 border-bottom d-flex align-items-center gap-2" data-id="${e.id}">
            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.name)}&background=5f5af6&color=fff&size=24" class="rounded-circle" width="24" height="24">
            <div><span class="fw-semibold">${e.name}</span><br><code class="font-size-11 text-muted">${e.emp_id}</code></div>
        </div>`).join('');
    dropdown.classList.remove('d-none');
    dropdown.querySelectorAll('.emp-dd-item').forEach(item => {
        item.addEventListener('mousedown', function(ev) {
            ev.preventDefault();
            const empId = this.dataset.id;
            const emp = employees.find(e => e.id == empId);
            if (!emp) return;
            selectedEmp = emp;
            searchInput.value = emp.name + ' (' + emp.emp_id + ')';
            hiddenSelect.value = emp.id;
            dropdown.classList.add('d-none');
            showEmployeeInfo(emp);
        });
    });
}

searchInput.addEventListener('focus', function() { renderDropdown(this.value); });
searchInput.addEventListener('input', function() { renderDropdown(this.value); });
document.addEventListener('click', function(e) {
    if (!document.getElementById('empSearchWrapper').contains(e.target)) dropdown.classList.add('d-none');
});

function showEmployeeInfo(emp) {
    document.getElementById('mainSection').style.display = 'block';
    document.getElementById('empName').textContent = emp.name;
    document.getElementById('empCode').textContent = emp.emp_id;
    document.getElementById('empDept').textContent = emp.department;
    document.getElementById('empDesg').textContent = emp.designation;
    document.getElementById('empJoin').textContent = emp.joining_date || '--';
    document.getElementById('empStatus').textContent = emp.status;
    document.getElementById('curBasic').textContent = Number(emp.basic).toLocaleString() + ' BDT';
    document.getElementById('curGross').textContent = Number(emp.gross).toLocaleString() + ' BDT';
    calcPreview();

    const type = document.getElementById('increment_type').value;
    if (type === 'annual') {
        checkEligibility(emp.id);
    } else {
        document.getElementById('eligibilityMsg').style.display = 'none';
    }
}

function checkEligibility(id) {
    fetch(CHECK_ELIGIBILITY_URL + '?employee_profile_id=' + id)
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById('eligibilityMsg');
            msg.textContent = data.message;
            msg.className = 'font-size-12 p-2 rounded-3 ' + (data.eligible ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger');
            msg.style.display = 'block';
        });
}

// Type selection
function selectType(el) {
    document.querySelectorAll('.type-pill').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('increment_type').value = el.dataset.type;

    const bulk = document.getElementById('bulkFields');
    const empField = document.querySelector('[name="employee_profile_id"]');
    if (el.dataset.type === 'bulk') {
        bulk.style.display = 'block';
        empField.removeAttribute('required');
        document.getElementById('inc_amount').disabled = true;
        document.getElementById('inc_amount').value = '';
    } else {
        bulk.style.display = 'none';
        empField.setAttribute('required', 'required');
        document.getElementById('inc_amount').disabled = false;
    }

    if (selectedEmp && el.dataset.type === 'annual') {
        checkEligibility(selectedEmp.id);
    } else {
        document.getElementById('eligibilityMsg').style.display = 'none';
    }
}

// Rule select -> based_on
document.getElementById('ruleSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.based) {
        document.getElementById('based_on').value = opt.dataset.based;
    }
});

// Preview calc
document.getElementById('inc_pct').addEventListener('input', calcPreview);
document.getElementById('inc_amount').addEventListener('input', calcPreview);
document.getElementById('based_on').addEventListener('change', calcPreview);

function calcPreview() {
    if (!selectedEmp) return;
    const basedOn = document.getElementById('based_on').value;
    const base = basedOn === 'gross' ? selectedEmp.gross : selectedEmp.basic;
    const pct = parseFloat(document.getElementById('inc_pct').value) || 0;
    const fixed = parseFloat(document.getElementById('inc_amount').value) || 0;
    const inc = fixed > 0 ? fixed : Math.round(base * pct / 100);
    if (inc <= 0) { document.getElementById('previewBox').style.display = 'none'; return; }

    const newBasic = basedOn === 'basic' ? selectedEmp.basic + inc : selectedEmp.basic;
    const newGross = selectedEmp.gross + inc;
    document.getElementById('newBasic').textContent = Number(newBasic).toLocaleString() + ' BDT';
    document.getElementById('incPreview').textContent = '+' + Number(inc).toLocaleString() + ' BDT';
    document.getElementById('newGross').textContent = Number(newGross).toLocaleString() + ' BDT';
    document.getElementById('previewBox').style.display = 'block';
}
</script>
@endpush
