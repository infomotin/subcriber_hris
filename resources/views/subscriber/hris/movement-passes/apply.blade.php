@extends('layouts.subscriber')

@section('title', 'Apply Movement Pass')

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
    .usage-card { border-radius:10px; border:1px solid #e2e8f0; padding:0.85rem; text-align:center; background:#fff; transition:all 0.2s; cursor:pointer; }
    .usage-card:hover { border-color:#5f5af6; box-shadow:0 4px 12px rgba(95,90,246,0.08); }
    .usage-card.selected { border-color:#5f5af6; background:rgba(95,90,246,0.04); box-shadow:0 0 0 2px rgba(95,90,246,0.15); }
    .usage-card.disabled { opacity:0.5; cursor:not-allowed; }
    .usage-card .type-code { font-size:0.6rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:#5f5af6; }
    .usage-card .available { font-size:1.35rem; font-weight:800; color:#0f172a; line-height:1.2; }
    .usage-card .label { font-size:0.65rem; color:#94a3b8; text-transform:uppercase; }
    .history-table { font-size:0.78rem; }
    .history-table th { font-weight:600; color:#64748b; text-transform:uppercase; font-size:0.65rem; letter-spacing:0.04em; }
    .emp-search-card { overflow:visible !important; }
    #empDropdown { z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,0.12); }
    .emp-dd-item:hover { background:#f1f5f9; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-transfer-alt text-primary me-1.5 align-middle font-size-26"></i>Apply Movement Pass
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.movement-passes.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> All Passes
    </a>
</div>

<form method="POST" action="{{ route('subscriber.hris.movement-passes.store') }}" id="passForm">
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
                        <input type="text" class="form-control" id="empSearchInput" placeholder="Search by name or employee ID..." autocomplete="off" required>
                        <input type="hidden" name="employee_profile_id" id="employeeSelect" required>
                        <div id="empDropdown" class="position-absolute w-100 shadow-sm border rounded-bottom d-none" style="max-height:280px;overflow-y:auto;background:#fff;"></div>
                    </div>
                    @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-calendar me-1 text-primary"></i> Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control" name="date" id="passDate" min="{{ date('Y-m-d') }}" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
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
                    </div>
                </div>
            </div>

            {{-- RIGHT: Movement Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Movement Details
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-slate-700">Movement Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="movement_type_id" id="movementTypeSelect" required>
                                    <option value="">-- Choose Movement Type --</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t->id }}"
                                            data-duration="{{ $t->duration_type }}"
                                            data-max-hours="{{ $t->max_hours }}"
                                            data-return="{{ $t->requires_return ? 1 : 0 }}">
                                            {{ $t->name }} ({{ $t->duration_type === 'short_leave' ? 'Max '.$t->max_hours.'h' : 'Day Out' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('movement_type_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Out Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="out_time" id="outTime" required>
                                @error('out_time') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="returnTimeGroup">
                                <label class="form-label fw-semibold text-slate-700">Return Time <span class="text-danger" id="returnReq">*</span></label>
                                <input type="time" class="form-control" name="return_time" id="returnTime">
                                <small class="text-muted" id="returnHint">Required for short leave</small>
                                @error('return_time') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Duration</label>
                                <input type="text" class="form-control bg-light" id="durationDisplay" readonly value="--">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-slate-700">Reason</label>
                                <textarea class="form-control" name="reason" rows="2" placeholder="Optional reason...">{{ old('reason') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('subscriber.hris.movement-passes.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
                                <i class="bx bx-send me-1.5 align-middle font-size-18"></i> Submit Pass
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

{{-- Monthly Usage --}}
<div id="usageSection" style="display:none;" class="mt-4">
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-bar-chart text-primary me-1.5 align-middle font-size-18"></i> Monthly Usage ({{ now()->format('F Y') }})
            </h6>
            <div class="row g-3" id="usageCards"></div>
        </div>
    </div>
</div>

{{-- History --}}
<div id="historySection" style="display:none;" class="mt-4">
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-history text-primary me-1.5 align-middle font-size-18"></i> Pass History (This Month)
            </h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 history-table">
                    <thead class="table-light">
                        <tr><th>Type</th><th>Date</th><th>Out</th><th>Return</th><th>Duration</th><th>Status</th></tr>
                    </thead>
                    <tbody id="historyBody">
                        <tr><td colspan="6" class="text-center py-3 text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const EMPLOYEE_INFO_URL = '{{ route("subscriber.hris.movement-passes.employee-info") }}';
const USAGE_URL = '{{ route("subscriber.hris.movement-passes.monthly-usage") }}';
const HISTORY_URL = '{{ route("subscriber.hris.movement-passes.history") }}';

const employees = @json($employees->map(fn($e) => ['id' => $e->id, 'name' => $e->user?->name ?? 'N/A', 'emp_id' => $e->employee_id]));

const searchInput = document.getElementById('empSearchInput');
const hiddenSelect = document.getElementById('employeeSelect');
const dropdown = document.getElementById('empDropdown');

function renderDropdown(query) {
    const q = (query || '').toLowerCase().trim();
    const matches = q ? employees.filter(e => e.name.toLowerCase().includes(q) || e.emp_id.toLowerCase().includes(q)) : employees;
    dropdown.innerHTML = matches.length === 0
        ? '<div class="px-3 py-2 text-muted font-size-12">No employees found</div>'
        : matches.map(e => `<div class="emp-dd-item px-3 py-2 font-size-13 border-bottom d-flex align-items-center gap-2" data-id="${e.id}" data-name="${e.name} (${e.emp_id})">
            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.name)}&background=5f5af6&color=fff&size=24" class="rounded-circle" width="24" height="24">
            <div><span class="fw-semibold">${e.name}</span><br><code class="font-size-11 text-muted">${e.emp_id}</code></div>
        </div>`).join('');
    dropdown.classList.remove('d-none');
    dropdown.querySelectorAll('.emp-dd-item').forEach(item => {
        item.addEventListener('mousedown', function(ev) {
            ev.preventDefault();
            searchInput.value = this.dataset.name;
            hiddenSelect.value = this.dataset.id;
            dropdown.classList.add('d-none');
            hiddenSelect.dispatchEvent(new Event('change'));
        });
    });
}

searchInput.addEventListener('focus', function() { renderDropdown(this.value); });
searchInput.addEventListener('input', function() { renderDropdown(this.value); });
document.addEventListener('click', function(e) {
    if (!document.getElementById('empSearchWrapper').contains(e.target)) dropdown.classList.add('d-none');
});

// Movement type change
document.getElementById('movementTypeSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;
    const durType = opt.dataset.duration;
    const requiresReturn = opt.dataset.return === '1';
    const returnGroup = document.getElementById('returnTimeGroup');
    const returnInput = document.getElementById('returnTime');
    const returnReq = document.getElementById('returnReq');
    const returnHint = document.getElementById('returnHint');

    if (requiresReturn) {
        returnGroup.style.display = '';
        returnInput.required = true;
        returnReq.style.display = '';
        returnHint.textContent = 'Required for short leave';
    } else {
        returnGroup.style.display = 'none';
        returnInput.required = false;
        returnInput.value = '';
        returnReq.style.display = 'none';
        returnHint.textContent = '';
    }
    calcDuration();
});

// Duration calc
document.getElementById('outTime').addEventListener('change', calcDuration);
document.getElementById('returnTime').addEventListener('change', calcDuration);

function calcDuration() {
    const out = document.getElementById('outTime').value;
    const ret = document.getElementById('returnTime').value;
    const display = document.getElementById('durationDisplay');
    if (out && ret) {
        const [oh, om] = out.split(':').map(Number);
        const [rh, rm] = ret.split(':').map(Number);
        const diff = (rh * 60 + rm) - (oh * 60 + om);
        display.value = diff > 0 ? (diff / 60).toFixed(1) + ' hours' : '--';
    } else if (out && !ret) {
        display.value = 'Day Out';
    } else {
        display.value = '--';
    }
}

// Employee change
hiddenSelect.addEventListener('change', function() {
    const empId = this.value;
    if (!empId) {
        document.getElementById('mainSection').style.display = 'none';
        document.getElementById('usageSection').style.display = 'none';
        document.getElementById('historySection').style.display = 'none';
        return;
    }
    document.getElementById('mainSection').style.display = 'block';

    fetch(EMPLOYEE_INFO_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(emp => {
            if (!emp) return;
            document.getElementById('empName').textContent = emp.name || '--';
            document.getElementById('empCode').textContent = emp.employee_id || '--';
            document.getElementById('empDept').textContent = emp.department || '--';
            document.getElementById('empDesg').textContent = emp.designation || '--';
            const photo = document.getElementById('empPhoto');
            const ph = document.getElementById('empPhotoPlaceholder');
            if (emp.photo_url) { photo.src = emp.photo_url; photo.style.display = 'block'; ph.style.display = 'none'; }
            else { photo.style.display = 'none'; ph.style.display = 'flex'; }
        });

    // Load usage
    const uc = document.getElementById('usageCards');
    uc.innerHTML = '<div class="col-12 text-center py-3 text-muted"><i class="bx bx-loader bx-spin font-size-20 d-block mb-1"></i>Loading...</div>';
    document.getElementById('usageSection').style.display = 'block';

    fetch(USAGE_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(data => {
            uc.innerHTML = '';
            data.forEach(t => {
                const pct = t.max_allowed > 0 ? Math.round((t.used / t.max_allowed) * 100) : 0;
                const disabled = t.remaining <= 0 ? 'disabled' : '';
                const col = document.createElement('div');
                col.className = 'col-md-3 col-sm-6';
                col.innerHTML = `<div class="usage-card ${disabled}">
                    <div class="type-code">${t.code}</div>
                    <div class="available">${t.remaining}</div>
                    <div class="label">Remaining (${t.used}/${t.max_allowed} used)</div>
                </div>`;
                uc.appendChild(col);
            });
        });

    // Load history
    document.getElementById('historySection').style.display = 'block';
    document.getElementById('historyBody').innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted"><i class="bx bx-loader bx-spin"></i> Loading...</td></tr>';

    fetch(HISTORY_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(passes => {
            const body = document.getElementById('historyBody');
            if (passes.length === 0) { body.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">No passes this month.</td></tr>'; return; }
            body.innerHTML = '';
            passes.forEach(p => {
                const sc = p.status === 'approved' ? 'bg-soft-success text-success' : p.status === 'rejected' ? 'bg-soft-danger text-danger' : 'bg-soft-warning text-warning';
                body.innerHTML += `<tr>
                    <td><span class="badge bg-soft-primary text-primary">${p.movement_type?.name || 'N/A'}</span></td>
                    <td>${new Date(p.date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'})}</td>
                    <td><code>${p.out_time}</code></td>
                    <td><code>${p.return_time || '--'}</code></td>
                    <td><code>${p.duration_hours ? p.duration_hours + 'h' : '--'}</code></td>
                    <td><span class="badge ${sc}">${p.status}</span></td>
                </tr>`;
            });
        });
});
</script>
@endpush
