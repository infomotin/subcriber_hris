@extends('layouts.subscriber')
@section('title', 'Apply Advance')
@section('content')
<style>
    .emp-info-card{border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;background:#fff}
    .emp-info-card .emp-header{background:linear-gradient(135deg,#10b981 0%,#059669 100%);padding:1.5rem;text-align:center;color:#fff}
    .emp-info-card .emp-photo{width:80px;height:80px;border-radius:50%;border:3px solid rgba(255,255,255,0.4);object-fit:cover;margin-bottom:0.75rem}
    .emp-info-card .emp-name{font-size:1.05rem;font-weight:700;margin-bottom:0.15rem}
    .emp-info-card .emp-id{font-size:0.75rem;opacity:0.8}
    .emp-info-card .info-row{display:flex;justify-content:space-between;padding:0.6rem 1rem;border-bottom:1px solid #f1f5f9;font-size:0.82rem}
    .emp-info-card .info-row:last-child{border-bottom:none}
    .emp-info-card .info-label{color:#94a3b8;font-weight:500}
    .emp-info-card .info-value{color:#1e293b;font-weight:600;text-align:right}
    .emp-search-card{overflow:visible!important}
    #empDropdown{z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.12)}
    .emp-dd-item:hover{background:#f1f5f9}
    .advance-card{border-radius:10px;border:1px solid #e2e8f0;padding:0.85rem;text-align:center;transition:all 0.2s;background:#fff;cursor:pointer}
    .advance-card:hover{border-color:#10b981;box-shadow:0 4px 12px rgba(16,185,129,0.08)}
    .advance-card.selected{border-color:#10b981;background:rgba(16,185,129,0.04);box-shadow:0 0 0 2px rgba(16,185,129,0.15)}
    .advance-card .type-code{font-size:0.6rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#10b981}
    .advance-card .mode{font-size:0.7rem;color:#64748b}
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Salary Advances</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-dollar text-primary me-1.5 align-middle font-size-26"></i>Apply for Advance</h4>
    </div>
    <a href="{{ route('subscriber.hris.advances.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-arrow-back me-1"></i> All Advances</a>
</div>

<form method="POST" action="{{ route('subscriber.hris.advances.store') }}" id="advanceForm">
    @csrf
    <div class="card border-0 shadow-sm mb-4 emp-search-card" style="border-radius:14px;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700 mb-1"><i class="bx bx-user me-1 text-primary"></i> Select Employee <span class="text-danger">*</span></label>
                    <div class="position-relative" id="empSearchWrapper">
                        <input type="text" class="form-control" id="empSearchInput" placeholder="Search by name or ID..." autocomplete="off" required>
                        <input type="hidden" name="employee_profile_id" id="employeeSelect" required>
                        <div id="empDropdown" class="position-absolute w-100 shadow-sm border rounded-bottom d-none" style="max-height:280px;overflow-y:auto;background:#fff;"></div>
                    </div>
                    @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700 mb-1"><i class="bx bx-category me-1 text-primary"></i> Advance Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="advance_type_id" id="advanceTypeSelect" required>
                        <option value="">-- Select --</option>
                        @foreach($advanceTypes as $t)
                            <option value="{{ $t->id }}" data-mode="{{ $t->payment_mode }}">{{ $t->name }} ({{ $t->payment_mode === 'one_time' ? 'One Time' : 'Installment' }})</option>
                        @endforeach
                    </select>
                    @error('advance_type_id') <div class="text-danger font-size-12">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700 mb-1"><i class="bx bx-dollar me-1 text-primary"></i> Paid Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="advance_source_id" required>
                        <option value="">-- Select --</option>
                        @foreach($advanceSources as $s)
                            <option value="{{ $s->id }}" {{ old('advance_source_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('advance_source_id') <div class="text-danger font-size-12">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-slate-700 mb-1"><i class="bx bx-user me-1 text-primary"></i> Reference</label>
                    <div class="position-relative" id="refSearchWrapper">
                        <input type="text" class="form-control form-control-sm" id="refSearchInput" placeholder="Reference employee" autocomplete="off">
                        <input type="hidden" name="reference_employee_id" id="refSelect">
                        <div id="refDropdown" class="position-absolute w-100 shadow-sm border rounded-bottom d-none" style="max-height:200px;overflow-y:auto;background:#fff;z-index:9999;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mainSection" style="display:none;">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="emp-info-card shadow-sm">
                    <div class="emp-header">
                        <img id="empPhoto" class="emp-photo" src="" alt="" style="display:none;">
                        <div id="empPhotoPlaceholder" class="emp-photo d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.2);display:flex!important;"><i class="bx bx-user font-size-32"></i></div>
                        <div class="emp-name" id="empName">--</div>
                        <div class="emp-id" id="empCode">--</div>
                    </div>
                    <div class="p-0">
                        <div class="info-row"><span class="info-label">Department</span><span class="info-value" id="empDept">--</span></div>
                        <div class="info-row"><span class="info-label">Designation</span><span class="info-value" id="empDesg">--</span></div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-value" id="empPhone">--</span></div>
                    </div>
                </div>
                {{-- Existing Advances --}}
                <div id="existingAdvances" class="mt-4" style="display:none;">
                    <div class="card border-0 shadow-sm" style="border-radius:14px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;"><i class="bx bx-history text-primary me-1.5"></i> Existing Advances</h6>
                            <div id="existingList"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;"><i class="bx bx-detail text-primary me-1.5 align-middle font-size-18"></i> Advance Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label fw-semibold text-slate-700">Amount (BDT) <span class="text-danger">*</span></label><input type="number" step="0.01" min="1" class="form-control" name="amount" id="amountInput" value="{{ old('amount') }}" required oninput="calcInstallment()"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold text-slate-700">Installments</label><input type="number" class="form-control" name="installments" id="installmentsInput" value="{{ old('installments', 1) }}" min="1" max="60" oninput="calcInstallment()"><div class="form-text" id="installmentHint">Set to 1 for one-time payment</div></div>
                            <div class="col-md-6"><label class="form-label fw-semibold text-slate-700">Monthly Deduction</label><input type="text" class="form-control bg-light" id="monthlyDeduction" readonly value="0.00 BDT"></div>
                            <div class="col-md-12"><label class="form-label fw-semibold text-slate-700">Reason / Purpose</label><textarea class="form-control" name="reason" rows="3" placeholder="Why do you need this advance?">{{ old('reason') }}</textarea></div>
                        </div>
                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('subscriber.hris.advances.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;"><i class="bx bx-send me-1.5 align-middle font-size-18"></i> Submit Application</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@if ($errors->any())<div class="alert alert-danger rounded-pill px-4 mt-3"><i class="bx bx-error-circle me-1 align-middle"></i> @foreach ($errors->all() as $error) {{ $error }} @endforeach</div>@endif
@endsection

@push('scripts')
<script>
const EMP_INFO_URL = '{{ route("subscriber.hris.advances.employee-info") }}';
const employees = @json($employees);
const searchInput = document.getElementById('empSearchInput');
const hiddenSelect = document.getElementById('employeeSelect');
const dropdown = document.getElementById('empDropdown');
const refInput = document.getElementById('refSearchInput');
const refHidden = document.getElementById('refSelect');
const refDropdown = document.getElementById('refDropdown');

function renderDropdown(input, dd, data, q) {
    const query = (q||'').toLowerCase().trim();
    const matches = query ? data.filter(e => e.name.toLowerCase().includes(query) || e.emp_id.toLowerCase().includes(query)) : data;
    dd.innerHTML = matches.length === 0 ? '<div class="px-3 py-2 text-muted font-size-12">No results</div>' :
        matches.map(e => `<div class="emp-dd-item px-3 py-2 font-size-13 cursor-pointer border-bottom d-flex align-items-center gap-2" data-id="${e.id}" data-name="${e.name} (${e.emp_id})"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.name)}&background=10b981&color=fff&size=24" class="rounded-circle" width="24" height="24"><div><span class="fw-semibold">${e.name}</span><br><code class="font-size-11 text-muted">${e.emp_id}</code></div></div>`).join('');
    dd.classList.remove('d-none');
    dd.querySelectorAll('.emp-dd-item').forEach(item => {
        item.addEventListener('mousedown', function(ev) { ev.preventDefault(); input.value = this.dataset.name; dd.classList.add('d-none'); if(input===searchInput){hiddenSelect.value=this.dataset.id;hiddenSelect.dispatchEvent(new Event('change'));}else{refHidden.value=this.dataset.id;} });
    });
}

searchInput.addEventListener('focus', function(){ renderDropdown(searchInput, dropdown, employees, this.value); });
searchInput.addEventListener('input', function(){ renderDropdown(searchInput, dropdown, employees, this.value); });
refInput.addEventListener('focus', function(){ renderDropdown(refInput, refDropdown, employees, this.value); });
refInput.addEventListener('input', function(){ renderDropdown(refInput, refDropdown, employees, this.value); });
document.addEventListener('click', function(e) {
    if (!document.getElementById('empSearchWrapper').contains(e.target)) dropdown.classList.add('d-none');
    if (!document.getElementById('refSearchWrapper').contains(e.target)) refDropdown.classList.add('d-none');
});

document.getElementById('advanceTypeSelect').addEventListener('change', function() {
    const mode = this.options[this.selectedIndex]?.dataset?.mode;
    const instInput = document.getElementById('installmentsInput');
    const hint = document.getElementById('installmentHint');
    if (mode === 'one_time') { instInput.value = 1; instInput.max = 1; hint.textContent = 'One-time payment (single installment)'; }
    else { instInput.max = 60; hint.textContent = 'Monthly installment (up to 60 months)'; }
    calcInstallment();
});

function calcInstallment() {
    const amount = parseFloat(document.getElementById('amountInput').value) || 0;
    const inst = parseInt(document.getElementById('installmentsInput').value) || 1;
    const monthly = inst > 0 ? (amount / inst).toFixed(2) : '0.00';
    document.getElementById('monthlyDeduction').value = monthly + ' BDT';
}

hiddenSelect.addEventListener('change', function() {
    const empId = this.value;
    if (!empId) { document.getElementById('mainSection').style.display='none'; return; }
    document.getElementById('mainSection').style.display='block';
    fetch(EMP_INFO_URL+'?employee_profile_id='+empId).then(r=>r.json()).then(emp => {
        if(!emp)return;
        document.getElementById('empName').textContent=emp.name||'--';
        document.getElementById('empCode').textContent=emp.employee_id||'--';
        document.getElementById('empDept').textContent=emp.department||'--';
        document.getElementById('empDesg').textContent=emp.designation||'--';
        document.getElementById('empPhone').textContent=emp.phone||'--';
        const photo=document.getElementById('empPhoto'),ph=document.getElementById('empPhotoPlaceholder');
        if(emp.photo_url){photo.src=emp.photo_url;photo.style.display='block';ph.style.display='none';}else{photo.style.display='none';ph.style.display='flex';}
        const exDiv=document.getElementById('existingAdvances'),exList=document.getElementById('existingList');
        if(emp.existing_advances&&emp.existing_advances.length>0){exDiv.style.display='block';exList.innerHTML=emp.existing_advances.map(a=>`<div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:0.82rem;"><div><span class="fw-semibold">${a.type}</span><br><code class="font-size-10 text-muted">${a.date}</code></div><div class="text-end"><strong>${ parseFloat(a.approved_amount||a.amount).toLocaleString() } BDT</strong><br><span class="font-size-10 text-muted">${a.installments}x ${ parseFloat(a.monthly_deduction||0).toLocaleString() }/mo</span></div></div>`).join('');}else{exDiv.style.display='none';}
    });
});
</script>
@endpush
