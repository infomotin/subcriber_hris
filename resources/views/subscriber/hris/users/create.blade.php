@extends('layouts.subscriber')

@section('title', 'Create User')

@section('content')
<style>
    .emp-info-card {
        background: linear-gradient(135deg, rgba(95,90,246,0.04), rgba(139,92,246,0.04));
        border: 1px solid rgba(95,90,246,0.12);
        border-radius: 12px;
        padding: 16px 20px;
        display: none;
    }
    .emp-info-card.show { display: block; }
    .emp-detail { font-size: 0.8rem; color: #64748b; }
    .emp-detail strong { color: #334155; }
    .field-locked { background: #f8fafc !important; }
    .already-linked { color: #94a3b8; font-style: italic; }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-user-plus text-primary me-1.5 align-middle font-size-26"></i>Create New User
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.users.store') }}" id="createUserForm">
            @csrf
            <input type="hidden" name="employee_profile_id" id="employee_profile_id" value="{{ old('employee_profile_id') }}">

            {{-- Step 1: Select Employee --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-1 font-size-11 fw-bold">Step 1</span>
                    <h6 class="fw-bold text-slate-800 mb-0" style="font-family:'Poppins',sans-serif;">Select Employee</h6>
                </div>
                <label class="form-label fw-semibold text-slate-700">Employee ID <span class="text-danger">*</span></label>
                <select class="form-select @error('employee_profile_id') is-invalid @enderror" id="employee_select" required>
                    <option value="">-- Select an Employee --</option>
                    @foreach($employees as $emp)
                        @php $hasUser = !is_null($emp->user_id); @endphp
                        <option value="{{ $emp->id }}" {{ old('employee_profile_id') == $emp->id ? 'selected' : '' }}
                            {{ $hasUser ? 'disabled' : '' }}
                            data-name="{{ $emp->user?->name ?? '' }}"
                            data-email="{{ $emp->user?->email ?? '' }}"
                            data-department="{{ $emp->department?->name ?? 'N/A' }}"
                            data-designation="{{ $emp->designation?->title ?? 'N/A' }}"
                            data-phone="{{ $emp->phone_number ?? '' }}"
                            data-gender="{{ $emp->gender ?? '' }}"
                            data-status="{{ $emp->status ?? '' }}"
                            data-has-user="{{ $hasUser ? '1' : '0' }}">
                            {{ $emp->employee_id }} — {{ $emp->user?->name ?? 'N/A' }} ({{ $emp->department?->name ?? 'N/A' }}){{ $hasUser ? ' ✓ Linked' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('employee_profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted mt-1 d-block font-size-11">
                    <i class="bx bx-info-circle me-1"></i> Employees marked <span class="text-success fw-semibold">✓ Linked</span> already have a user account.
                </small>
            </div>

            {{-- Employee Info Card --}}
            <div class="emp-info-card mb-4" id="empInfoCard">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="https://ui-avatars.com/api/?name=U&background=5f5af6&color=fff&size=40" class="rounded-circle border" width="40" height="40" id="empAvatar">
                    <div>
                        <h6 class="fw-bold mb-0 text-slate-800" id="empName" style="font-family:'Poppins',sans-serif;">—</h6>
                        <code class="font-size-11 text-muted" id="empCode">—</code>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <span class="emp-detail"><strong>Dept:</strong> <span id="empDept">—</span></span>
                    <span class="emp-detail"><strong>Designation:</strong> <span id="empDesig">—</span></span>
                    <span class="emp-detail"><strong>Phone:</strong> <span id="empPhone">—</span></span>
                    <span class="emp-detail"><strong>Gender:</strong> <span id="empGender">—</span></span>
                </div>
                <div id="empAlreadyLinked" class="mt-2" style="display:none;">
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 font-size-11">
                        <i class="bx bx-link-alt me-1"></i> This employee already has a user account.
                    </span>
                </div>
            </div>

            {{-- Step 2: User Details --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-1 font-size-11 fw-bold">Step 2</span>
                    <h6 class="fw-bold text-slate-800 mb-0" style="font-family:'Poppins',sans-serif;">Account Details</h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-slate-700">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror field-locked" name="name" id="input_name" value="{{ old('name') }}" required readonly placeholder="Auto-filled from employee">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-slate-700">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror field-locked" name="email" id="input_email" value="{{ old('email') }}" required readonly placeholder="Auto-filled from employee">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Step 3: Security & Role --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-1 font-size-11 fw-bold">Step 3</span>
                    <h6 class="fw-bold text-slate-800 mb-0" style="font-family:'Poppins',sans-serif;">Security & Role</h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="input_password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-slate-700">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-slate-700">Assign Role</label>
                        <select class="form-select" name="role">
                            <option value="">-- No Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('subscriber.hris.users.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5" id="submitBtn" disabled>
                    <i class="bx bx-user-plus me-1"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('employee_select');
    const hiddenInput = document.getElementById('employee_profile_id');
    const nameInput = document.getElementById('input_name');
    const emailInput = document.getElementById('input_email');
    const infoCard = document.getElementById('empInfoCard');
    const submitBtn = document.getElementById('submitBtn');
    const alreadyLinked = document.getElementById('empAlreadyLinked');

    function clearFields() {
        nameInput.value = '';
        emailInput.value = '';
        nameInput.readOnly = true;
        emailInput.readOnly = true;
        nameInput.classList.add('field-locked');
        emailInput.classList.add('field-locked');
        infoCard.classList.remove('show');
        submitBtn.disabled = true;
        hiddenInput.value = '';
        alreadyLinked.style.display = 'none';
    }

    function loadEmployeeFromOption(option) {
        if (!option || !option.value) {
            clearFields();
            return;
        }

        var hasUser = option.dataset.hasUser === '1';
        var empId = option.value;
        hiddenInput.value = empId;

        document.getElementById('empAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(option.dataset.name || 'U') + '&background=5f5af6&color=fff&size=40';
        document.getElementById('empName').textContent = option.dataset.name || '—';
        document.getElementById('empCode').textContent = empId;
        document.getElementById('empDept').textContent = option.dataset.department || '—';
        document.getElementById('empDesig').textContent = option.dataset.designation || '—';
        document.getElementById('empPhone').textContent = option.dataset.phone || '—';
        document.getElementById('empGender').textContent = option.dataset.gender || '—';

        nameInput.value = option.dataset.name || '';
        emailInput.value = option.dataset.email || '';
        nameInput.readOnly = false;
        emailInput.readOnly = false;
        nameInput.classList.remove('field-locked');
        emailInput.classList.remove('field-locked');

        infoCard.classList.add('show');

        if (hasUser) {
            submitBtn.disabled = true;
            alreadyLinked.style.display = 'block';
        } else {
            submitBtn.disabled = false;
            alreadyLinked.style.display = 'none';
        }
    }

    select.addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        loadEmployeeFromOption(selected);
    });

    if (select.value) {
        loadEmployeeFromOption(select.options[select.selectedIndex]);
    }
});
</script>
@endpush
