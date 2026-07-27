@extends('layouts.subscriber')

@section('title', 'Apply Bill')

@section('content')
<style>
    .emp-info-card { border-radius:14px; overflow:hidden; border:1px solid #e2e8f0; background:#fff; }
    .emp-info-card .emp-header { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%); padding:1.5rem; text-align:center; color:#fff; }
    .emp-info-card .emp-photo { width:80px; height:80px; border-radius:50%; border:3px solid rgba(255,255,255,0.4); object-fit:cover; margin-bottom:0.75rem; }
    .emp-info-card .emp-name { font-size:1.05rem; font-weight:700; margin-bottom:0.15rem; }
    .emp-info-card .emp-id { font-size:0.75rem; opacity:0.8; }
    .emp-info-card .info-row { display:flex; justify-content:space-between; padding:0.6rem 1rem; border-bottom:1px solid #f1f5f9; font-size:0.82rem; }
    .emp-info-card .info-row:last-child { border-bottom:none; }
    .emp-info-card .info-label { color:#94a3b8; font-weight:500; }
    .emp-info-card .info-value { color:#1e293b; font-weight:600; text-align:right; }
    .emp-placeholder { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:280px; color:#94a3b8; }
    .emp-search-card { overflow:visible !important; }
    #empDropdown { z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,0.12); }
    .emp-dd-item:hover { background:#f1f5f9; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Bill Management</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-receipt text-primary me-1.5 align-middle font-size-26"></i>Submit Bill / Expense
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.bills.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> All Bills
    </a>
</div>

<form method="POST" action="{{ route('subscriber.hris.bills.store') }}" id="billForm" enctype="multipart/form-data">
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
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-category me-1 text-primary"></i> Bill Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('bill_type_id') is-invalid @enderror" name="bill_type_id" id="billTypeSelect" required>
                        <option value="">-- Select --</option>
                        @foreach($billTypes as $type)
                            <option value="{{ $type->id }}" {{ old('bill_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('bill_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700 mb-1">
                        <i class="bx bx-target-lock me-1 text-primary"></i> Purpose <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('bill_purpose_id') is-invalid @enderror" name="bill_purpose_id" id="purposeSelect" required>
                        <option value="">-- Select --</option>
                        @foreach($billPurposes as $p)
                            <option value="{{ $p->id }}" {{ old('bill_purpose_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('bill_purpose_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <img id="empPhoto" class="emp-photo" src="" alt="" style="display:none;">
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
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-value" id="empPhone">--</span></div>
                        <div class="info-row"><span class="info-label">Status</span><span class="info-value" id="empStatus">--</span></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Bill Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                            <i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Bill Details
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Bill Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Bill Number</label>
                                <input type="text" class="form-control @error('bill_no') is-invalid @enderror" name="bill_no" value="{{ old('bill_no') }}" placeholder="e.g. BILL-2026-001">
                                @error('bill_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Voucher / Receipt Upload</label>
                                <input type="file" class="form-control @error('voucher') is-invalid @enderror" name="voucher" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text">JPG, PNG or PDF (max 5MB)</div>
                                @error('voucher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-slate-700">Description / Remarks</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="2" placeholder="What is this bill for...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('subscriber.hris.bills.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
                                <i class="bx bx-send me-1.5 align-middle font-size-18"></i> Submit Bill
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
const EMPLOYEE_INFO_URL = '{{ route("subscriber.hris.bills.employee-info") }}';
const employees = @json($employees);

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
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.name)}&background=f59e0b&color=fff&size=24" class="rounded-circle" width="24" height="24">
                <div><span class="fw-semibold">${e.name}</span><br><code class="font-size-11 text-muted">${e.emp_id}</code></div>
            </div>`
        ).join('');
    }
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
    if (!document.getElementById('empSearchWrapper').contains(e.target)) {
        dropdown.classList.add('d-none');
    }
});

hiddenSelect.addEventListener('change', function() {
    const empId = this.value;
    if (!empId) {
        document.getElementById('mainSection').style.display = 'none';
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
            document.getElementById('empJoin').textContent = emp.joining_date || '--';
            document.getElementById('empPhone').textContent = emp.phone || '--';
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
});
</script>
@endpush
