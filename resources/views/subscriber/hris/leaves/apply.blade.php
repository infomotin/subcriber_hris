@extends('layouts.subscriber')

@section('title', 'Leave Application')

@section('content')
<style>
    .emp-info-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    .emp-info-card .emp-header {
        background: linear-gradient(135deg, #5f5af6 0%, #7c3aed 100%);
        padding: 1.5rem;
        text-align: center;
        color: #fff;
    }
    .emp-info-card .emp-photo {
        width: 80px; height: 80px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.4);
        object-fit: cover;
        margin-bottom: 0.75rem;
    }
    .emp-info-card .emp-name {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
    }
    .emp-info-card .emp-id {
        font-size: 0.75rem;
        opacity: 0.8;
    }
    .emp-info-card .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.82rem;
    }
    .emp-info-card .info-row:last-child { border-bottom: none; }
    .emp-info-card .info-label { color: #94a3b8; font-weight: 500; }
    .emp-info-card .info-value { color: #1e293b; font-weight: 600; text-align: right; }
    .emp-placeholder {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        min-height: 280px; color: #94a3b8;
    }
    .emp-search-card { overflow: visible !important; }
    #empDropdown {
        z-index: 9999;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .emp-dd-item:hover { background: #f1f5f9; }
    .emp-dd-item.selected { background: rgba(95,90,246,0.08); }

    .balance-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.85rem;
        text-align: center;
        transition: all 0.2s;
        background: #fff;
        cursor: pointer;
    }
    .balance-card:hover { border-color: #5f5af6; box-shadow: 0 4px 12px rgba(95,90,246,0.08); }
    .balance-card.selected { border-color: #5f5af6; background: rgba(95,90,246,0.04); box-shadow: 0 0 0 2px rgba(95,90,246,0.15); }
    .balance-card.disabled { opacity: 0.5; cursor: not-allowed; }
    .balance-card .type-code { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #5f5af6; }
    .balance-card .available { font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .balance-card .label { font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.03em; }
    .balance-card .spent { font-size: 0.7rem; color: #dc2626; }
    .balance-card .allocated { font-size: 0.7rem; color: #64748b; }

    .history-table { font-size: 0.78rem; }
    .history-table th { font-weight: 600; color: #64748b; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.04em; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-calendar-check text-primary me-1.5 align-middle font-size-26"></i>Leave Application
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.leaves.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px; font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> All Applications
    </a>
</div>

<form method="POST" action="{{ route('subscriber.hris.leaves.store') }}" id="leaveForm">
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
                        <div id="empDropdown" class="position-absolute w-100 shadow-sm border rounded-bottom d-none" style="max-height:280px; overflow-y:auto; background:#fff;"></div>
                    </div>
                    @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-calendar me-1 text-primary"></i> Leave Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="leaveTypeSelect" required>
                        <option value="">-- Choose Leave Type --</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" data-days="{{ $type->days_per_year }}">{{ $type->name }} ({{ $type->days_per_year }}d/yr)</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="leave_type_id" id="leave_type_id_hidden">
                    @error('leave_type_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Main Two Column Layout --}}
    <div id="mainSection" style="display:none;">
        <div class="row g-4">

            {{-- LEFT: Employee Info --}}
            <div class="col-lg-4">
                <div class="emp-info-card shadow-sm" id="empInfoCard">
                    <div class="emp-header">
                        <img id="empPhoto" class="emp-photo" src="" alt="Employee Photo" style="display:none;">
                        <div id="empPhotoPlaceholder" class="emp-photo d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.2); display:flex !important;">
                            <i class="bx bx-user font-size-32"></i>
                        </div>
                        <div class="emp-name" id="empName">--</div>
                        <div class="emp-id" id="empCode">--</div>
                    </div>
                    <div class="p-0">
                        <div class="info-row"><span class="info-label">Department</span><span class="info-value" id="empDept">--</span></div>
                        <div class="info-row"><span class="info-label">Designation</span><span class="info-value" id="empDesg">--</span></div>
                        <div class="info-row"><span class="info-label">Joining Date</span><span class="info-value" id="empJoin">--</span></div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-value" id="empPhone">--</span></div>
                        <div class="info-row"><span class="info-label">Blood Group</span><span class="info-value" id="empBlood">--</span></div>
                        <div class="info-row"><span class="info-label">Status</span><span class="info-value" id="empStatus">--</span></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Leave Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Leave Details
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="startDate" min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" id="endDate" min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-slate-700">Total Days</label>
                                <input type="text" class="form-control bg-light" id="totalDaysDisplay" readonly value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-slate-700">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" rows="3" required placeholder="Reason for leave...">{{ old('reason') }}</textarea>
                            </div>
                            @if($leaveReasons->count())
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-slate-700">Quick Reason</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($leaveReasons as $r)
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-reason" data-reason="{{ $r->reason }}">{{ $r->reason }}</button>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('subscriber.hris.leaves.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
                                <i class="bx bx-send me-1.5 align-middle font-size-18"></i> Submit Application
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

{{-- Leave Balance Section --}}
<div id="balanceSection" style="display:none;" class="mt-4">
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-wallet text-primary me-1.5 align-middle font-size-18"></i> Leave Balance ({{ now()->year }})
            </h6>
            <div class="row g-3" id="balanceCards"></div>
        </div>
    </div>
</div>

{{-- Applied Leaves History --}}
<div id="historySection" style="display:none;" class="mt-4">
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-history text-primary me-1.5 align-middle font-size-18"></i> Applied Leaves This Year
            </h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 history-table">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-center">PDF</th>
                        </tr>
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
const EMPLOYEE_INFO_URL = '{{ route("subscriber.hris.leaves.employee-info") }}';
const BALANCE_URL = '{{ route("subscriber.hris.leaves.balance") }}';
const HISTORY_URL = '{{ route("subscriber.hris.leaves.history") }}';
const PDF_URL_BASE = '{{ route("subscriber.hris.leaves.pdf", "PLACEHOLDER") }}';
const CSRF = '{{ csrf_token() }}';

// Employee data for search
const employees = @json($employees->map(fn($e) => ['id' => $e->id, 'name' => $e->user?->name ?? 'N/A', 'emp_id' => $e->employee_id]));

const searchInput = document.getElementById('empSearchInput');
const hiddenSelect = document.getElementById('employeeSelect');
const dropdown = document.getElementById('empDropdown');

function renderDropdown(query) {
    const q = (query || '').toLowerCase().trim();
    const matches = q
        ? employees.filter(e => e.name.toLowerCase().includes(q) || e.emp_id.toLowerCase().includes(q))
        : employees;

    if (matches.length === 0) {
        dropdown.innerHTML = '<div class="px-3 py-2 text-muted font-size-12">No employees found</div>';
    } else {
        dropdown.innerHTML = matches.map(e =>
            `<div class="emp-dd-item px-3 py-2 font-size-13 cursor-pointer border-bottom d-flex align-items-center gap-2" data-id="${e.id}" data-name="${e.name} (${e.emp_id})">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.name)}&background=5f5af6&color=fff&size=24" class="rounded-circle" width="24" height="24">
                <div><span class="fw-semibold">${e.name}</span><br><code class="font-size-11 text-muted">${e.emp_id}</code></div>
            </div>`
        ).join('');
    }
    dropdown.classList.remove('d-none');

    dropdown.querySelectorAll('.emp-dd-item').forEach(item => {
        item.addEventListener('mousedown', function(ev) {
            ev.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name;
            searchInput.value = name;
            hiddenSelect.value = id;
            dropdown.classList.add('d-none');
            hiddenSelect.dispatchEvent(new Event('change'));
        });
    });
}

searchInput.addEventListener('focus', function() { renderDropdown(this.value); });
searchInput.addEventListener('input', function() { renderDropdown(this.value); });
document.addEventListener('click', function(e) {
    if (!document.getElementById('empSearchWrapper').contains(e.target)) {
        dropdown.classList.add('d-none');
    }
});

// Quick reason buttons
document.querySelectorAll('.quick-reason').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelector('textarea[name="reason"]').value = this.dataset.reason;
    });
});

// Employee select (hidden input change)
hiddenSelect.addEventListener('change', function() {
    const empId = this.value;
    if (!empId) {
        document.getElementById('mainSection').style.display = 'none';
        document.getElementById('balanceSection').style.display = 'none';
        document.getElementById('historySection').style.display = 'none';
        return;
    }

    document.getElementById('mainSection').style.display = 'block';

    // Load employee info
    fetch(EMPLOYEE_INFO_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(emp => {
            if (!emp) return;
            document.getElementById('empName').textContent = emp.name || '--';
            document.getElementById('empCode').textContent = emp.employee_id || '--';
            document.getElementById('empDept').textContent = emp.department || '--';
            document.getElementById('empDesg').textContent = emp.designation || '--';
            document.getElementById('empJoin').textContent = emp.joining_date || '--';
            document.getElementById('empPhone').textContent = emp.phone || '--';
            document.getElementById('empBlood').textContent = emp.blood_group || '--';
            document.getElementById('empStatus').textContent = emp.status || '--';

            const photo = document.getElementById('empPhoto');
            const placeholder = document.getElementById('empPhotoPlaceholder');
            if (emp.photo_url) {
                photo.src = emp.photo_url;
                photo.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                photo.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        });

    // Load balance
    const balanceContainer = document.getElementById('balanceCards');
    balanceContainer.innerHTML = '<div class="col-12 text-center py-3 text-muted"><i class="bx bx-loader bx-spin font-size-20 d-block mb-1"></i>Loading...</div>';
    document.getElementById('balanceSection').style.display = 'block';

    fetch(BALANCE_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(data => {
            balanceContainer.innerHTML = '';
            data.forEach(t => {
                const pct = t.allocated > 0 ? Math.round((t.spent / t.allocated) * 100) : 0;
                const disabled = t.available <= 0 ? 'disabled' : '';
                const col = document.createElement('div');
                col.className = 'col-md-3 col-sm-6';
                col.innerHTML = `
                    <div class="balance-card ${disabled}" data-type-id="${t.id}" data-available="${t.available}" onclick="selectType(this, ${t.id}, ${t.available})">
                        <div class="type-code">${t.code}</div>
                        <div class="available">${t.available}</div>
                        <div class="label">Days Available</div>
                        <div class="d-flex justify-content-between mt-1 px-1">
                            <span class="allocated">${t.allocated} allotted</span>
                            <span class="spent">${t.spent} used</span>
                        </div>
                        <div class="progress mt-2" style="height:3px;">
                            <div class="progress-bar" style="width:${pct}%; background:${pct >= 80 ? '#dc2626' : pct >= 50 ? '#f59e0b' : '#10b981'};"></div>
                        </div>
                    </div>
                `;
                balanceContainer.appendChild(col);
            });
        });

    // Load history
    document.getElementById('historySection').style.display = 'block';
    document.getElementById('historyBody').innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted"><i class="bx bx-loader bx-spin"></i> Loading...</td></tr>';

    fetch(HISTORY_URL + '?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(leaves => {
            const body = document.getElementById('historyBody');
            if (leaves.length === 0) {
                body.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">No leaves applied this year.</td></tr>';
                return;
            }
            body.innerHTML = '';
            leaves.forEach(l => {
                const statusClass = l.status === 'approved' ? 'bg-soft-success text-success' : l.status === 'rejected' ? 'bg-soft-danger text-danger' : 'bg-soft-warning text-warning';
                const pdfUrl = PDF_URL_BASE.replace('PLACEHOLDER', l.id);
                body.innerHTML += `<tr>
                    <td><span class="badge bg-soft-primary text-primary">${l.leave_type?.name || 'N/A'}</span></td>
                    <td>${new Date(l.start_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short'})} - ${new Date(l.end_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'})}</td>
                    <td><strong>${l.total_days}d</strong></td>
                    <td style="max-width:150px;"><span class="text-truncate d-inline-block" style="max-width:150px;">${l.reason || '-'}</span></td>
                    <td><span class="badge ${statusClass}">${l.status}</span></td>
                    <td class="text-center"><a href="${pdfUrl}" class="btn btn-sm btn-outline-primary rounded-pill" title="Download PDF"><i class="bx bx-download"></i></a></td>
                </tr>`;
            });
        });
});

function selectType(el, typeId, available) {
    if (available <= 0) return;
    document.querySelectorAll('.balance-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('leave_type_id_hidden').value = typeId;
    const sel = document.getElementById('leaveTypeSelect');
    sel.value = typeId;
}

document.getElementById('leaveTypeSelect').addEventListener('change', function() {
    document.getElementById('leave_type_id_hidden').value = this.value;
});

document.querySelectorAll('input[name="start_date"], input[name="end_date"]').forEach(el => {
    el.addEventListener('change', calcDays);
});

function calcDays() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    if (start && end) {
        const s = new Date(start), e = new Date(end);
        const diff = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('totalDaysDisplay').value = diff > 0 ? diff + ' day' + (diff > 1 ? 's' : '') : '0';
    }
}
</script>
@endpush
