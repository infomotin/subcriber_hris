@extends('layouts.subscriber')

@section('title', 'New Promotion')

@section('content')
<style>
    .search-result-card {
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(95,90,246,0.04), rgba(139,92,246,0.04));
        border: 1px solid rgba(95,90,246,0.12);
        transition: all 0.2s;
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        font-weight: 600;
    }
    .info-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
    }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-trending-up text-primary me-1.5 align-middle font-size-26"></i>New Promotion
        </h4>
    </div>
    <div>
        <a href="{{ route('subscriber.hris.promotions.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-4">
        <form id="promotionForm" method="POST" action="{{ route('subscriber.hris.promotions.store') }}">
            @csrf

            <input type="hidden" name="employee_profile_id" id="employee_profile_id">

            <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                <i class="bx bx-search-alt-2 text-primary me-1.5 align-middle font-size-20"></i> Step 1: Search Employee
            </h5>
            <div class="row g-2 mb-4">
                <div class="col-lg-5">
                    <input type="text" class="form-control rounded-pill px-4" id="search_employee_id" placeholder="Search by Employee ID or Name..." style="height: 42px;">
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-primary rounded-pill w-100" id="searchBtn" style="height: 42px; font-size: 0.85rem;">
                        <i class="bx bx-search me-1"></i> Search
                    </button>
                </div>
            </div>

            <div id="employeeResult" style="display: none;">
                <div class="search-result-card p-4 mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="" id="empAvatar" class="rounded-circle border" width="48" height="48">
                        <div>
                            <h6 class="fw-bold mb-0.5 text-slate-800" id="empName" style="font-family: 'Poppins', sans-serif;"></h6>
                            <code class="font-size-12 text-muted" id="empId"></code>
                        </div>
                    </div>
                    <hr class="my-3 opacity-25">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="info-label">Current Department</div>
                            <div class="info-value" id="empDepartment"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Current Designation</div>
                            <div class="info-value" id="empDesignation"></div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-slate-800 mt-4 mb-3" style="font-family: 'Poppins', sans-serif;">
                    <i class="bx bx-transfer-alt text-primary me-1.5 align-middle font-size-20"></i> Step 2: Promotion Details
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700">New Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="new_department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('new_department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700">New Designation <span class="text-danger">*</span></label>
                        <select class="form-select" name="new_designation_id" required>
                            <option value="">Select Designation</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('new_designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Promotion Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="promotion_type" required>
                            <option value="">Select Type</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('promotion_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Effective Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="effective_date" value="{{ old('effective_date') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-slate-700">Notes / Reason</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Reason for promotion, additional remarks...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5" style="height: 44px; font-size: 0.9rem;">
                        <i class="bx bx-save me-1.5 align-middle font-size-18"></i> Save & Generate Letter
                    </button>
                </div>
            </div>

            <div id="employeeNotFound" class="text-center py-4" style="display: none;">
                <i class="bx bx-user-x text-muted font-size-40 d-block mb-2"></i>
                <p class="text-muted mb-0">Employee not found with this ID.</p>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const empId = document.getElementById('search_employee_id').value.trim();
    if (!empId) return;

    document.getElementById('employeeResult').style.display = 'none';
    document.getElementById('employeeNotFound').style.display = 'none';

    fetch('{{ route("subscriber.hris.promotions.employee-search") }}?q=' + encodeURIComponent(empId))
        .then(res => res.json())
        .then(data => {
            if (data.found) {
                document.getElementById('employee_profile_id').value = data.id;
                document.getElementById('empAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.name) + '&background=5f5af6&color=fff&size=48';
                document.getElementById('empName').textContent = data.name;
                document.getElementById('empId').textContent = data.employee_id;
                document.getElementById('empDepartment').textContent = data.department;
                document.getElementById('empDesignation').textContent = data.designation;
                document.getElementById('employeeResult').style.display = 'block';
            } else {
                document.getElementById('employeeNotFound').style.display = 'block';
            }
        });
});

document.getElementById('search_employee_id').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('searchBtn').click();
    }
});
</script>
@endpush
