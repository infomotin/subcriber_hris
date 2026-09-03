@extends('layouts.subscriber')

@section('title', 'Add New Employee')

@section('content')
<style>
    :root {
        --wizard-primary: var(--primary, #5f5af6);
        --wizard-primary-light: color-mix(in srgb, var(--wizard-primary) 8%, transparent);
        --wizard-primary-border: color-mix(in srgb, var(--wizard-primary) 25%, transparent);
        --wizard-success: #10b981;
        --wizard-success-light: #ecfdf5;
        --wizard-glass: rgba(255,255,255,0.85);
        --wizard-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.03);
        --wizard-shadow-hover: 0 2px 6px rgba(0,0,0,.06), 0 8px 20px rgba(0,0,0,.05);
    }

    .ew-page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1rem;
    }
    .ew-page-header h4 {
        font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1rem;
        color: #0f172a; margin: 0;
    }
    .ew-page-header h4 i { margin-right: 0.5rem; color: var(--wizard-primary); }
    .ew-back-btn {
        font-size: 0.72rem; padding: 0.3rem 0.8rem; border-radius: 20px;
        border: 1px solid #e2e8f0; color: #64748b; background: #fff;
        transition: all .2s;
    }
    .ew-back-btn:hover { border-color: var(--wizard-primary); color: var(--wizard-primary); }

    /* Stepper */
    .ew-stepper-card {
        background: var(--wizard-glass); backdrop-filter: blur(8px);
        border: 1px solid #f1f5f9; border-radius: 14px;
        box-shadow: var(--wizard-shadow); padding: 1.2rem 1.5rem;
        margin-bottom: 1rem;
    }
    .ew-stepper {
        display: flex; align-items: center; justify-content: space-between;
        position: relative; max-width: 780px; margin: 0 auto;
    }
    .ew-stepper::before {
        content: ''; position: absolute; top: 18px; left: 40px; right: 40px;
        height: 3px; background: #e2e8f0; border-radius: 2px; z-index: 1;
    }
    .ew-stepper-progress {
        position: absolute; top: 18px; left: 40px; height: 100%;
        background: var(--wizard-primary); border-radius: 2px; z-index: 2;
        transition: width .4s cubic-bezier(.4,0,.2,1); width: 0%;
    }
    .ew-step {
        z-index: 3; text-align: center; cursor: pointer; position: relative;
        flex: 0 0 auto; width: 110px;
    }
    .ew-step-dot {
        width: 38px; height: 38px; border-radius: 50%;
        background: #fff; border: 2.5px solid #cbd5e1;
        color: #94a3b8; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem; margin: 0 auto;
        transition: all .3s cubic-bezier(.4,0,.2,1);
        position: relative;
    }
    .ew-step.active .ew-step-dot {
        border-color: var(--wizard-primary); background: var(--wizard-primary);
        color: #fff; box-shadow: 0 0 0 4px var(--wizard-primary-light);
    }
    .ew-step.completed .ew-step-dot {
        border-color: var(--wizard-success); background: var(--wizard-success);
        color: #fff; box-shadow: 0 0 0 4px var(--wizard-success-light);
    }
    .ew-step-dot .ew-lock {
        position: absolute; top: -4px; right: -4px; width: 16px; height: 16px;
        background: var(--wizard-success); border-radius: 50%;
        display: none; align-items: center; justify-content: center;
        border: 2px solid #fff;
    }
    .ew-step.completed .ew-step-dot .ew-lock { display: flex; }
    .ew-step-dot .ew-lock i { font-size: 8px; color: #fff; }
    .ew-step-label {
        font-size: 0.65rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.04em; color: #94a3b8; margin-top: 8px;
        transition: color .3s;
    }
    .ew-step.active .ew-step-label { color: var(--wizard-primary); font-weight: 700; }
    .ew-step.completed .ew-step-label { color: var(--wizard-success); }
    .ew-step-draft-badge {
        font-size: 0.5rem; background: var(--wizard-primary-light);
        color: var(--wizard-primary); padding: 1px 5px; border-radius: 8px;
        font-weight: 600; margin-top: 3px; display: none;
    }
    .ew-step.has-draft .ew-step-draft-badge { display: inline-block; }

    /* Step Panels */
    .ew-panel {
        display: none; animation: ewFadeIn .35s ease;
    }
    .ew-panel.active { display: block; }
    @keyframes ewFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    .ew-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 14px;
        box-shadow: var(--wizard-shadow); overflow: hidden;
        transition: box-shadow .2s;
    }
    .ew-card:hover { box-shadow: var(--wizard-shadow-hover); }
    .ew-card-header {
        padding: 0.7rem 1rem; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .ew-card-header i {
        font-size: 1.1rem; color: var(--wizard-primary);
    }
    .ew-card-header h6 {
        font-family: 'Poppins', sans-serif; font-weight: 700;
        font-size: 0.8rem; color: #1e293b; margin: 0;
    }
    .ew-card-body { padding: 1rem; }

    .ew-section-title {
        font-weight: 700; font-size: 0.72rem; text-transform: uppercase;
        letter-spacing: 0.04em; color: var(--wizard-primary);
        margin-bottom: 0.7rem; padding-bottom: 0.4rem;
        border-bottom: 1.5px solid var(--wizard-primary-light);
    }

    /* Form compact overrides */
    .ew-card .form-label { font-size: 0.68rem; font-weight: 600; color: #475569; margin-bottom: 0.15rem; }
    .ew-card .form-control, .ew-card .form-select {
        font-size: 0.72rem; padding: 0.3rem 0.5rem; border-radius: 8px;
        border: 1px solid #e2e8f0; transition: border-color .2s, box-shadow .2s;
    }
    .ew-card .form-control:focus, .ew-card .form-select:focus {
        border-color: var(--wizard-primary);
        box-shadow: 0 0 0 3px var(--wizard-primary-light);
    }
    .ew-card .form-control.is-invalid { border-color: #ef4444; }
    .ew-card .form-text { font-size: 0.62rem; color: #94a3b8; }

    .ew-profile-upload {
        width: 90px; height: 90px; border-radius: 50%; overflow: hidden;
        border: 2.5px dashed var(--wizard-primary-border); cursor: pointer;
        position: relative; margin: 0 auto 0.5rem;
        transition: border-color .2s, transform .2s;
    }
    .ew-profile-upload:hover { border-color: var(--wizard-primary); transform: scale(1.03); }
    .ew-profile-upload img { width: 100%; height: 100%; object-fit: cover; }
    .ew-profile-upload .ew-camera-icon {
        position: absolute; bottom: 0; right: 0; width: 26px; height: 26px;
        background: var(--wizard-primary); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff;
    }
    .ew-profile-upload .ew-camera-icon i { font-size: 12px; color: #fff; }

    .ew-salary-box {
        background: linear-gradient(135deg, var(--wizard-primary-light), rgba(139,92,246,.04));
        border: 1.5px dashed var(--wizard-primary-border) !important;
        border-radius: 12px; padding: 1rem;
    }
    .ew-salary-box .input-group-text {
        font-size: 0.75rem; font-weight: 700; border-radius: 8px 0 0 8px;
    }
    .ew-salary-box .form-control { font-weight: 600; font-size: 0.8rem; }

    .ew-dynamic-row {
        background: #f8fafc; border: 1px solid #e9ecef; border-radius: 10px;
        padding: 0.75rem; margin-bottom: 0.6rem; transition: all .2s;
    }
    .ew-dynamic-row:hover { border-color: #cbd5e1; box-shadow: 0 1px 4px rgba(0,0,0,.02); }
    .ew-remove-btn {
        width: 26px; height: 26px; border-radius: 50% !important;
        min-height: auto !important; padding: 0 !important;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px;
    }

    /* Action buttons */
    .ew-actions {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;
    }
    .ew-btn {
        font-size: 0.72rem; font-weight: 600; padding: 0.45rem 1.2rem;
        border-radius: 20px; transition: all .2s; border: none;
    }
    .ew-btn-prev {
        background: #f1f5f9; color: #64748b;
    }
    .ew-btn-prev:hover { background: #e2e8f0; color: #334155; }
    .ew-btn-next {
        background: var(--wizard-primary); color: #fff;
        box-shadow: 0 2px 8px color-mix(in srgb, var(--wizard-primary) 30%, transparent);
    }
    .ew-btn-next:hover { box-shadow: 0 4px 14px color-mix(in srgb, var(--wizard-primary) 40%, transparent); transform: translateY(-1px); }
    .ew-btn-submit {
        background: linear-gradient(135deg, #10b981, #059669); color: #fff;
        box-shadow: 0 2px 8px rgba(16,185,129,.3);
    }
    .ew-btn-submit:hover { box-shadow: 0 4px 14px rgba(16,185,129,.4); transform: translateY(-1px); }

    .ew-draft-toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
        background: #1e293b; color: #fff; padding: 0.5rem 1rem;
        border-radius: 10px; font-size: 0.7rem; font-weight: 600;
        box-shadow: 0 4px 16px rgba(0,0,0,.15);
        transform: translateY(20px); opacity: 0; transition: all .3s;
        pointer-events: none;
    }
    .ew-draft-toast.show { transform: translateY(0); opacity: 1; }
</style>

<div class="ew-page-header">
    <h4><i class="bx bx-user-plus"></i> Add New Employee</h4>
    <a href="{{ route('subscriber.hris.employees.index') }}" class="ew-back-btn">
        <i class="bx bx-arrow-back me-1"></i> Back to List
    </a>
</div>

<div class="ew-stepper-card">
    <div class="ew-stepper">
        <div class="ew-stepper-progress" id="ew-progress-bar"></div>
        <div class="ew-step active" data-step="1" onclick="ewGoToStep(1)">
            <div class="ew-step-dot">
                1
                <span class="ew-lock"><i class="bx bx-check"></i></span>
            </div>
            <div class="ew-step-label">Basic Info</div>
            <div class="ew-step-draft-badge">Draft</div>
        </div>
        <div class="ew-step" data-step="2" onclick="ewGoToStep(2)">
            <div class="ew-step-dot">
                2
                <span class="ew-lock"><i class="bx bx-check"></i></span>
            </div>
            <div class="ew-step-label">Employment</div>
            <div class="ew-step-draft-badge">Draft</div>
        </div>
        <div class="ew-step" data-step="3" onclick="ewGoToStep(3)">
            <div class="ew-step-dot">
                3
                <span class="ew-lock"><i class="bx bx-check"></i></span>
            </div>
            <div class="ew-step-label">Compensation</div>
            <div class="ew-step-draft-badge">Draft</div>
        </div>
        <div class="ew-step" data-step="4" onclick="ewGoToStep(4)">
            <div class="ew-step-dot">
                4
                <span class="ew-lock"><i class="bx bx-check"></i></span>
            </div>
            <div class="ew-step-label">Qualifications</div>
            <div class="ew-step-draft-badge">Draft</div>
        </div>
        <div class="ew-step" data-step="5" onclick="ewGoToStep(5)">
            <div class="ew-step-dot">
                5
                <span class="ew-lock"><i class="bx bx-check"></i></span>
            </div>
            <div class="ew-step-label">Personal</div>
            <div class="ew-step-draft-badge">Draft</div>
        </div>
    </div>
</div>

<form id="ew-form" action="{{ route('subscriber.hris.employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="form_token" id="ew-form-token" value="{{ bin2hex(random_bytes(32)) }}">

    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-3" style="font-size:0.78rem;">
        <i class="bx bx-error-circle me-1"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1" style="list-style:disc;padding-left:1.2rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" style="font-size:0.78rem;">
        <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ==================== STEP 1: BASIC INFO ==================== --}}
    <div class="ew-panel active" id="ew-step-1">
        <div class="ew-card">
            <div class="ew-card-header">
                <i class="bx bx-user"></i>
                <h6>Personal & Primary Contact</h6>
            </div>
            <div class="ew-card-body">
                <div class="row g-3">
                    <div class="col-lg-5 text-center border-end">
                        <div class="ew-section-title"><i class="bx bx-id-card me-1"></i> Profile Photo</div>
                        <div class="ew-profile-upload" onclick="document.getElementById('ew-profile-photo').click()">
                            <img id="ew-profile-preview" src="https://ui-avatars.com/api/?name=Employee&background=e2e8f0&color=94a3b8&size=180&bold=true" alt="Profile">
                            <div class="ew-camera-icon"><i class="bx bx-camera"></i></div>
                        </div>
                        <input type="file" class="d-none" id="ew-profile-photo" name="profile_photo" accept=".jpg,.jpeg,.png" onchange="ewPreviewPhoto(this)">
                        <div class="form-text">JPG/PNG, max 2MB</div>

                        <div class="mt-3">
                            <div class="mb-2">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required value="{{ old('name') }}" placeholder="e.g. Rahim Ahmed">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email (Login) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required value="{{ old('email') }}" placeholder="rahim@example.com">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required autocomplete="new-password" placeholder="Min 8 characters">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="ew-section-title"><i class="bx bx-map-pin me-1"></i> Current Address</div>
                        <div class="mb-2">
                            <label class="form-label">Address Line <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address_line_1" required value="{{ old('address_line_1') }}" placeholder="Apt 4B, House 12, Road 4">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="country" value="Bangladesh" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">State / Division <span class="text-danger">*</span></label>
                                <select class="form-select" id="ew-division" required>
                                    <option value="">Select</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}" data-name="{{ $div->name }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="ew-state" name="state" value="{{ old('state') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">District <span class="text-danger">*</span></label>
                                <select class="form-select" id="ew-district" required disabled>
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Thana / Upazila <span class="text-danger">*</span></label>
                                <select class="form-select" id="ew-thana" required disabled>
                                    <option value="">Select</option>
                                </select>
                                <input type="hidden" id="ew-city" name="city" value="{{ old('city') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Zip Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="zip_code" required value="{{ old('zip_code') }}" placeholder="e.g. 1212">
                            </div>
                        </div>

                        <div class="ew-section-title mt-3"><i class="bx bx-phone me-1"></i> Contact & Demographics</div>
                        <div class="row g-2">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone_number" required value="{{ old('phone_number') }}" placeholder="01712345678">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dob" required value="{{ old('dob') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Blood Group</label>
                                <input type="text" class="form-control" name="blood_group" value="{{ old('blood_group') }}" placeholder="A+ / O-">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" required>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender->name }}" {{ old('gender') === $gender->name ? 'selected' : '' }}>{{ $gender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Religion</label>
                                <select class="form-select" id="ew-religion" name="religion">
                                    <option value="">Select</option>
                                    @foreach(['Islam','Hinduism','Christianity','Buddhism','Other'] as $r)
                                        <option value="{{ $r }}" {{ old('religion') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ew-actions">
            <div></div>
            <button type="button" class="ew-btn ew-btn-next" onclick="ewNextStep()">
                Continue <i class="bx bx-chevron-right ms-1"></i>
            </button>
        </div>
    </div>

    {{-- ==================== STEP 2: EMPLOYMENT ==================== --}}
    <div class="ew-panel" id="ew-step-2">
        <div class="ew-card">
            <div class="ew-card-header">
                <i class="bx bx-briefcase"></i>
                <h6>Employment & Official Details</h6>
            </div>
            <div class="ew-card-body">
                <div class="row g-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="employee_id" required value="{{ old('employee_id') }}" placeholder="EMP-1054">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="joining_date" required value="{{ old('joining_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Select</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <select class="form-select" name="designation_id" required>
                            <option value="">Select</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Work Shift</label>
                        <select class="form-select" name="shift_id">
                            <option value="">Select</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="probation" {{ old('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                            <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="resigned" {{ old('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Employee Type</label>
                        <select class="form-select" name="employee_type">
                            <option value="">Select</option>
                            <option value="worker" {{ old('employee_type') === 'worker' ? 'selected' : '' }}>Worker</option>
                            <option value="staff" {{ old('employee_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manager" {{ old('employee_type') === 'manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check mt-1">
                            <input type="checkbox" class="form-check-input" id="ew-overtime" name="overtime_eligible" value="1" {{ old('overtime_eligible') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="ew-overtime" style="font-size:0.72rem;">
                                <i class="bx bx-time-five text-primary me-1"></i> Overtime Eligible
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Overtime Rate (BDT/hr)</label>
                        <input type="number" class="form-control" name="overtime_rate" value="{{ old('overtime_rate') }}" placeholder="e.g. 250" step="0.01">
                    </div>
                </div>
            </div>
        </div>
        <div class="ew-actions">
            <button type="button" class="ew-btn ew-btn-prev" onclick="ewPrevStep()">
                <i class="bx bx-chevron-left me-1"></i> Previous
            </button>
            <button type="button" class="ew-btn ew-btn-next" onclick="ewNextStep()">
                Continue <i class="bx bx-chevron-right ms-1"></i>
            </button>
        </div>
    </div>

    {{-- ==================== STEP 3: COMPENSATION ==================== --}}
    <div class="ew-panel" id="ew-step-3">
        <div class="ew-card mb-3">
            <div class="ew-card-header">
                <i class="bx bx-money"></i>
                <h6>Salary Structure & Bank Details</h6>
            </div>
            <div class="ew-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <div class="ew-salary-box">
                            <label class="form-label" style="color:var(--wizard-primary);font-weight:700;">Gross Salary (BDT) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">&#2547;</span>
                                <input type="number" class="form-control" id="ew-gross" placeholder="e.g. 50000">
                            </div>
                            @if($activeSalaryRelation)
                                <div class="mt-2">
                                    <span class="badge" style="background:var(--wizard-primary-light);color:var(--wizard-primary);font-size:0.6rem;">Basic: {{ (int)$activeSalaryRelation->basic_percent }}%</span>
                                    <span class="badge" style="background:var(--wizard-primary-light);color:var(--wizard-primary);font-size:0.6rem;">House: {{ (int)$activeSalaryRelation->house_rent_percent }}%</span>
                                    <span class="badge" style="background:var(--wizard-primary-light);color:var(--wizard-primary);font-size:0.6rem;">Medical: {{ (int)$activeSalaryRelation->medical_percent }}%</span>
                                    <span class="badge" style="background:var(--wizard-primary-light);color:var(--wizard-primary);font-size:0.6rem;">TA/DA: {{ (int)$activeSalaryRelation->tada_percent }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ew-section-title"><i class="bx bx-credit-card me-1"></i> Bank Transfer</div>
                        <div class="row g-2">
                            <div class="col-6 mb-2"><input type="text" class="form-control" name="bank_name" required placeholder="Bank Name"></div>
                            <div class="col-6 mb-2"><input type="text" class="form-control" name="branch_name" required placeholder="Branch"></div>
                            <div class="col-6 mb-2"><input type="text" class="form-control" name="account_name" required placeholder="Account Holder"></div>
                            <div class="col-6 mb-2"><input type="text" class="form-control" name="account_number" required placeholder="Account No"></div>
                            <div class="col-6 mb-2"><input type="text" class="form-control" name="routing_number" placeholder="Routing No"></div>
                            <div class="col-6 mb-2">
                                <select class="form-select" name="payment_mode" required>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="mobile_banking">Mobile Banking</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ew-section-title"><i class="bx bx-slider-alt me-1"></i> Salary Breakdown</div>
                <div class="row g-2">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Basic (&#2547;) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control ew-salary-field" id="ew-basic" name="basic_salary" placeholder="0.00">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">House Rent (&#2547;) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control ew-salary-field" id="ew-house" name="house_rent" placeholder="0.00">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Medical (&#2547;) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control ew-salary-field" id="ew-medical" name="medical_allowance" placeholder="0.00">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">TA/DA (&#2547;) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control ew-salary-field" id="ew-tada" name="conveyance_allowance" placeholder="0.00">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Other Allowances (&#2547;)</label>
                        <input type="number" class="form-control" name="other_allowances" value="0" placeholder="0.00">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label text-danger">Provident Fund (&#2547;)</label>
                        <input type="number" class="form-control" name="provident_fund_deduction" value="0" placeholder="0.00">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label text-danger">Tax Deduction (&#2547;)</label>
                        <input type="number" class="form-control" name="tax_deduction" value="0" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>
        <div class="ew-actions">
            <button type="button" class="ew-btn ew-btn-prev" onclick="ewPrevStep()">
                <i class="bx bx-chevron-left me-1"></i> Previous
            </button>
            <button type="button" class="ew-btn ew-btn-next" onclick="ewNextStep()">
                Continue <i class="bx bx-chevron-right ms-1"></i>
            </button>
        </div>
    </div>

    {{-- ==================== STEP 4: QUALIFICATIONS ==================== --}}
    <div class="ew-panel" id="ew-step-4">
        <div class="ew-card mb-3">
            <div class="ew-card-header">
                <i class="bx bx-book-bookmark"></i>
                <h6>Education & Experience</h6>
            </div>
            <div class="ew-card-body">
                <div class="ew-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-graduation me-1"></i> Academic Degrees</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" onclick="ewAddEdu()" style="font-size:0.65rem;height:26px;line-height:1;">
                        <i class="bx bx-plus me-1"></i> Add
                    </button>
                </div>
                <div id="ew-edu-container">
                    <div class="ew-dynamic-row ew-edu-row">
                        <input type="hidden" name="education[0][degree_name]" value="">
                        <input type="hidden" name="education[0][institution]" value="">
                        <input type="hidden" name="education[0][passing_year]" value="">
                        <input type="hidden" name="education[0][result]" value="">
                        <input type="hidden" name="education[0][certification_type]" value="education">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Degree <span class="text-danger">*</span></label>
                                <select class="form-select" onchange="ewEduHidden(this,'degree_name')">
                                    <option value="">Choose</option>
                                    <option value="Secondary School Certificate (SSC)">SSC</option>
                                    <option value="Higher Secondary Certificate (HSC)">HSC</option>
                                    <option value="Bachelor of Science (B.Sc.)">B.Sc.</option>
                                    <option value="Bachelor of Business Administration (BBA)">BBA</option>
                                    <option value="Bachelor of Arts (BA)">BA</option>
                                    <option value="Master of Science (M.Sc.)">M.Sc.</option>
                                    <option value="Master of Business Administration (MBA)">MBA</option>
                                    <option value="Diploma in Engineering">Diploma</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" oninput="ewEduHidden(this,'institution')" placeholder="Name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Year <span class="text-danger">*</span></label>
                                <select class="form-select" onchange="ewEduHidden(this,'passing_year')">
                                    <option value="">Year</option>
                                    @for($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Result <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" oninput="ewEduHidden(this,'result')" placeholder="3.85">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select class="form-select" onchange="ewEduHidden(this,'certification_type')">
                                    <option value="education">Academic</option>
                                    <option value="training">Training</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-12"><input type="file" class="form-control form-control-sm" name="education_doc[0]" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;"></div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="ew-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-briefcase me-1"></i> Work Experience</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" onclick="ewAddExp()" style="font-size:0.65rem;height:26px;line-height:1;">
                        <i class="bx bx-plus me-1"></i> Add
                    </button>
                </div>
                <div id="ew-exp-container">
                    <div class="ew-dynamic-row ew-exp-row">
                        <input type="hidden" name="experiences[0][company_name]" value="">
                        <input type="hidden" name="experiences[0][designation]" value="">
                        <input type="hidden" name="experiences[0][start_date]" value="">
                        <input type="hidden" name="experiences[0][end_date]" value="">
                        <input type="hidden" name="experiences[0][job_description]" value="">
                        <div class="row g-2">
                            <div class="col-md-4"><label class="form-label">Company</label><input type="text" class="form-control" oninput="ewExpHidden(this,'company_name')" placeholder="Company"></div>
                            <div class="col-md-3"><label class="form-label">Designation</label><input type="text" class="form-control" oninput="ewExpHidden(this,'designation')" placeholder="Role"></div>
                            <div class="col-md-2"><label class="form-label">Start</label><input type="date" class="form-control" onchange="ewExpHidden(this,'start_date')"></div>
                            <div class="col-md-3"><label class="form-label">End</label><input type="date" class="form-control" onchange="ewExpHidden(this,'end_date')"></div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-12"><label class="form-label">Responsibilities</label><textarea class="form-control" rows="2" oninput="ewExpHidden(this,'job_description')" placeholder="Key deliverables..." style="font-size:0.7rem;"></textarea></div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-12"><input type="file" class="form-control form-control-sm" name="experience_doc[0]" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ew-actions">
            <button type="button" class="ew-btn ew-btn-prev" onclick="ewPrevStep()">
                <i class="bx bx-chevron-left me-1"></i> Previous
            </button>
            <button type="button" class="ew-btn ew-btn-next" onclick="ewNextStep()">
                Continue <i class="bx bx-chevron-right ms-1"></i>
            </button>
        </div>
    </div>

    {{-- ==================== STEP 5: PERSONAL ==================== --}}
    <div class="ew-panel" id="ew-step-5">
        <div class="ew-card mb-3">
            <div class="ew-card-header">
                <i class="bx bx-user-detail"></i>
                <h6>Personal Information & Family</h6>
            </div>
            <div class="ew-card-body">
                <div class="ew-section-title"><i class="bx bx-home me-1"></i> Permanent Address</div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Address Line</label>
                        <input type="text" class="form-control" name="permanent_address_line_1" value="{{ old('permanent_address_line_1') }}" placeholder="Village, Road, House">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">State / Division</label>
                        <input type="text" class="form-control" name="permanent_state" value="{{ old('permanent_state') }}" placeholder="e.g. Dhaka">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">District</label>
                        <input type="text" class="form-control" name="permanent_city" value="{{ old('permanent_city') }}" placeholder="e.g. Dhaka">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Zip</label>
                        <input type="text" class="form-control" name="permanent_zip_code" value="{{ old('permanent_zip_code') }}" placeholder="1212">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control bg-light" name="permanent_country" value="Bangladesh" readonly>
                    </div>
                </div>

                <div class="ew-section-title"><i class="bx bx-id-card me-1"></i> Identity & Documents</div>
                <div class="row g-2 mb-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">NID Number</label>
                        <input type="text" class="form-control" name="nid" value="{{ old('nid') }}" placeholder="NID No">
                        <input type="file" class="form-control form-control-sm mt-1" name="doc_nid" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.6rem;">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Birth Certificate</label>
                        <input type="text" class="form-control" name="birth_certificate" value="{{ old('birth_certificate') }}" placeholder="Certificate No">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Marital Status</label>
                        <select class="form-select" name="marital_status">
                            <option value="">Select</option>
                            @foreach(['Single','Married','Divorced','Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Religion Doc</label>
                        <input type="file" class="form-control form-control-sm" name="doc_religion" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">NOC</label>
                        <input type="file" class="form-control form-control-sm" name="doc_noc" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Police Clearance</label>
                        <input type="file" class="form-control form-control-sm" name="doc_police_clearance" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;">
                    </div>
                </div>

                <div class="ew-section-title"><i class="bx bx-user-voice me-1"></i> Family</div>
                <div class="row g-2 mb-3">
                    <div class="col-md-3 mb-2"><label class="form-label">Father's Name</label><input type="text" class="form-control" name="father_name" value="{{ old('father_name') }}" placeholder="Name"></div>
                    <div class="col-md-3 mb-2"><label class="form-label">Father's Occupation</label><input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation') }}" placeholder="Occupation"></div>
                    <div class="col-md-3 mb-2"><label class="form-label">Mother's Name</label><input type="text" class="form-control" name="mother_name" value="{{ old('mother_name') }}" placeholder="Name"></div>
                    <div class="col-md-3 mb-2"><label class="form-label">Mother's Occupation</label><input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation') }}" placeholder="Occupation"></div>
                </div>

                <div class="ew-section-title"><i class="bx bx-shield-quarter me-1"></i> Guardian</div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4 mb-2"><label class="form-label">Guardian Name</label><input type="text" class="form-control" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Name"></div>
                    <div class="col-md-4 mb-2"><label class="form-label">Relation</label><input type="text" class="form-control" name="guardian_relation" value="{{ old('guardian_relation') }}" placeholder="e.g. Uncle"></div>
                    <div class="col-md-4 mb-2"><label class="form-label">Phone</label><input type="text" class="form-control" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="01712345678"></div>
                </div>

                <div class="ew-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-group me-1"></i> Dependents</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" onclick="ewAddDep()" style="font-size:0.65rem;height:26px;line-height:1;">
                        <i class="bx bx-plus me-1"></i> Add
                    </button>
                </div>
                <div id="ew-dep-container">
                    <div class="ew-dynamic-row ew-dep-row">
                        <input type="hidden" name="dependents[0][name]" value="">
                        <input type="hidden" name="dependents[0][relationship]" value="">
                        <input type="hidden" name="dependents[0][phone]" value="">
                        <div class="row g-2">
                            <div class="col-md-4"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewDepHidden(this,'name')" placeholder="Name"></div>
                            <div class="col-md-4"><label class="form-label">Relationship <span class="text-danger">*</span></label>
                                <select class="form-select" onchange="ewDepHidden(this,'relationship')">
                                    <option value="">Select</option>
                                    @foreach(['Father','Mother','Spouse','Brother','Sister','Son','Daughter','Other'] as $rel)
                                        <option value="{{ $rel }}">{{ $rel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label">Phone</label><input type="text" class="form-control" oninput="ewDepHidden(this,'phone')" placeholder="Phone"></div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="ew-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-user-check me-1"></i> Nominees</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" onclick="ewAddNom()" style="font-size:0.65rem;height:26px;line-height:1;">
                        <i class="bx bx-plus me-1"></i> Add
                    </button>
                </div>
                <div id="ew-nom-container">
                    <div class="ew-dynamic-row ew-nom-row">
                        <input type="hidden" name="nominees[0][name]" value="">
                        <input type="hidden" name="nominees[0][relationship]" value="">
                        <input type="hidden" name="nominees[0][share_percentage]" value="100">
                        <input type="hidden" name="nominees[0][identity_document_type]" value="">
                        <input type="hidden" name="nominees[0][identity_document_number]" value="">
                        <div class="row g-2">
                            <div class="col-md-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewNomHidden(this,'name')" placeholder="Name"></div>
                            <div class="col-md-2"><label class="form-label">Relation <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewNomHidden(this,'relationship')" placeholder="e.g. Spouse"></div>
                            <div class="col-md-2"><label class="form-label">Share %</label><input type="number" class="form-control" value="100" oninput="ewNomHidden(this,'share_percentage')" min="0" max="100"></div>
                            <div class="col-md-2"><label class="form-label">ID Type</label>
                                <select class="form-select" onchange="ewNomHidden(this,'identity_document_type')">
                                    <option value="">Select</option>
                                    <option value="NID">NID</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Birth Certificate">Birth Cert</option>
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label">ID Number</label><input type="text" class="form-control" oninput="ewNomHidden(this,'identity_document_number')" placeholder="ID No"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ew-actions">
            <button type="button" class="ew-btn ew-btn-prev" onclick="ewPrevStep()">
                <i class="bx bx-chevron-left me-1"></i> Previous
            </button>
            <button type="submit" class="ew-btn ew-btn-submit">
                <i class="bx bx-check-circle me-1"></i> Save Employee
            </button>
        </div>
    </div>
</form>

<div class="ew-draft-toast" id="ew-draft-toast">
    <i class="bx bx-save me-1"></i> Draft saved
</div>

@push('scripts')
<script>
(function() {
    const TOKEN = document.getElementById('ew-form-token').value;
    const TOTAL = 5;
    let current = 1;
    const completedSteps = new Set();
    const draftSteps = new Set();

    const progressBar = document.getElementById('ew-progress-bar');
    const form = document.getElementById('ew-form');

    // --- Stepper ---
    function updateStepper() {
        const pct = ((current - 1) / (TOTAL - 1)) * 100;
        progressBar.style.width = pct + '%';
        document.querySelectorAll('.ew-step').forEach(el => {
            const s = parseInt(el.dataset.step);
            el.classList.remove('active', 'completed');
            const dot = el.querySelector('.ew-step-dot');
            if (s === current) { el.classList.add('active'); dot.childNodes[0].textContent = s; }
            else if (completedSteps.has(s)) { el.classList.add('completed'); dot.childNodes[0].textContent = ''; }
            else { dot.childNodes[0].textContent = s; }
        });
    }

    function showStep(step) {
        document.querySelectorAll('.ew-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('ew-step-' + step).classList.add('active');
        current = step;
        updateStepper();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    window.ewGoToStep = function(step) {
        if (step <= current || completedSteps.has(step - 1) || completedSteps.has(step)) {
            autoSaveDraft(current);
            showStep(step);
        }
    };

    window.ewNextStep = function() {
        if (!validateStep(current)) { ewShowToast('Please fill required fields', '#ef4444'); return; }
        completedSteps.add(current);
        autoSaveDraft(current);
        if (current < TOTAL) showStep(current + 1);
    };

    window.ewPrevStep = function() {
        autoSaveDraft(current);
        if (current > 1) showStep(current - 1);
    };

    function validateStep(step) {
        const panel = document.getElementById('ew-step-' + step);
        let ok = true;
        panel.querySelectorAll('[required]').forEach(f => {
            if (!f.value.trim() && !f.disabled) { f.classList.add('is-invalid'); ok = false; }
            else { f.classList.remove('is-invalid'); }
        });
        return ok;
    }

    form.addEventListener('submit', function(e) {
        if (!validateStep(current)) { e.preventDefault(); ewShowToast('Please complete all required fields', '#ef4444'); return; }
        completedSteps.add(current);
        clearDraft();
    });

    // --- Draft Save/Load ---
    function getStepData(step) {
        const panel = document.getElementById('ew-step-' + step);
        const data = {};
        panel.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.name) {
                if (el.type === 'checkbox') { data[el.name] = el.checked ? '1' : '0'; }
                else if (el.type === 'file') { /* skip files */ }
                else { data[el.name] = el.value; }
            }
        });
        return data;
    }

    function autoSaveDraft(step) {
        const data = getStepData(step);
        fetch('{{ route("subscriber.hris.employees.draft.save") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ form_token: TOKEN, step: step, step_data: data })
        }).then(r => r.json()).then(() => {
            draftSteps.add(step);
            document.querySelector(`.ew-step[data-step="${step}"]`).classList.add('has-draft');
            ewShowToast('Draft saved');
        }).catch(() => {});
    }

    function loadDrafts() {
        fetch('{{ route("subscriber.hris.employees.draft.load") }}?form_token=' + TOKEN, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(r => r.json()).then(res => {
            if (res.drafts) {
                Object.keys(res.drafts).forEach(step => {
                    const data = res.drafts[step];
                    const panel = document.getElementById('ew-step-' + step);
                    if (!panel) return;
                    Object.keys(data).forEach(name => {
                        const el = panel.querySelector(`[name="${name}"]`);
                        if (el) {
                            if (el.type === 'checkbox') { el.checked = data[name] === '1'; }
                            else { el.value = data[name]; }
                        }
                    });
                    draftSteps.add(parseInt(step));
                    document.querySelector(`.ew-step[data-step="${step}"]`).classList.add('has-draft');
                });
            }
        }).catch(() => {});
    }

    function clearDraft() {
        fetch('{{ route("subscriber.hris.employees.draft.clear") }}?form_token=' + TOKEN, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).catch(() => {});
    }

    function ewShowToast(msg, bg) {
        const t = document.getElementById('ew-draft-toast');
        t.textContent = msg;
        if (bg) t.style.background = bg;
        t.classList.add('show');
        setTimeout(() => { t.classList.remove('show'); t.style.background = '#1e293b'; }, 2000);
    }

    // --- Address Cascading ---
    var divisionsData = {!! json_encode($divisions->map(fn($d) => [
        'id' => $d->id,
        'name' => $d->name,
        'districts' => $d->districts->map(fn($dist) => [
            'id' => $dist->id,
            'name' => $dist->name,
            'thanas' => $dist->thanas->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
            ])->values(),
        ])->values(),
    ])->values()) !!};
    var divSel = document.getElementById('ew-division');
    var distSel = document.getElementById('ew-district');
    var thanaSel = document.getElementById('ew-thana');
    var stateIn = document.getElementById('ew-state');
    var cityIn = document.getElementById('ew-city');

    if (divSel && distSel && thanaSel) {
        divSel.addEventListener('change', function() {
            var id = this.value;
            var opt = this.options[this.selectedIndex];
            stateIn.value = opt && opt.value ? opt.getAttribute('data-name') : '';
            distSel.innerHTML = '<option value="">Select District</option>';
            distSel.disabled = true;
            thanaSel.innerHTML = '<option value="">Select Thana</option>';
            thanaSel.disabled = true;
            cityIn.value = '';
            if (!id) return;
            var div = divisionsData.find(function(d) { return d.id == id; });
            if (div && div.districts && div.districts.length) {
                div.districts.forEach(function(d) {
                    var o = document.createElement('option');
                    o.value = d.id;
                    o.textContent = d.name;
                    o.setAttribute('data-name', d.name);
                    distSel.appendChild(o);
                });
                distSel.disabled = false;
            }
        });

        distSel.addEventListener('change', function() {
            var id = this.value;
            thanaSel.innerHTML = '<option value="">Select Thana</option>';
            thanaSel.disabled = true;
            cityIn.value = '';
            if (!id) return;
            var div = divisionsData.find(function(d) { return d.id == divSel.value; });
            if (div) {
                var dist = div.districts.find(function(d) { return d.id == id; });
                if (dist && dist.thanas && dist.thanas.length) {
                    dist.thanas.forEach(function(t) {
                        var o = document.createElement('option');
                        o.value = t.id;
                        o.textContent = t.name;
                        o.setAttribute('data-name', t.name);
                        thanaSel.appendChild(o);
                    });
                    thanaSel.disabled = false;
                }
            }
        });

        thanaSel.addEventListener('change', function() {
            var tName = this.options[this.selectedIndex] ? this.options[this.selectedIndex].getAttribute('data-name') : '';
            var dName = distSel.options[distSel.selectedIndex] ? distSel.options[distSel.selectedIndex].getAttribute('data-name') : '';
            cityIn.value = (tName && dName) ? tName + ', ' + dName : (tName || dName || '');
        });
    }

    // --- Salary Auto-calc ---
    var activeSR = {!! json_encode($activeSalaryRelation) !!};
    var grossIn = document.getElementById('ew-gross');
    if (activeSR && grossIn) {
        grossIn.addEventListener('input', function() {
            var g = parseFloat(this.value) || 0;
            var b = document.getElementById('ew-basic');
            var h = document.getElementById('ew-house');
            var m = document.getElementById('ew-medical');
            var t = document.getElementById('ew-tada');
            if (b) b.value = (g * activeSR.basic_percent / 100).toFixed(2);
            if (h) h.value = (g * activeSR.house_rent_percent / 100).toFixed(2);
            if (m) m.value = (g * activeSR.medical_percent / 100).toFixed(2);
            if (t) t.value = (g * activeSR.tada_percent / 100).toFixed(2);
        });
    }

    // --- Profile Photo Preview ---
    window.ewPreviewPhoto = function(input) {
        if (input.files && input.files[0]) {
            if (input.files[0].size > 2*1024*1024) { alert('Max 2MB'); input.value=''; return; }
            const r = new FileReader();
            r.onload = e => document.getElementById('ew-profile-preview').src = e.target.result;
            r.readAsDataURL(input.files[0]);
        }
    };

    // --- Dynamic Rows ---
    function ewEduHidden(el, field) {
        const h = el.closest('.ew-edu-row').querySelector(`input[name$="[${field}]"]`);
        if (h) h.value = el.value;
    }
    window.ewEduHidden = ewEduHidden;
    let eduIdx = 1;
    window.ewAddEdu = function() {
        const c = document.getElementById('ew-edu-container');
        const i = eduIdx++;
        const yOpts = '<option value="">Year</option>@for($y = date('Y'); $y >= 1980; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor';
        const d = document.createElement('div');
        d.className = 'ew-dynamic-row ew-edu-row';
        d.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><span style="font-size:0.68rem;font-weight:700;color:#475569;"><i class="bx bx-book me-1"></i>Degree #'+(i+1)+'</span><button type="button" class="btn btn-sm btn-light border text-danger ew-remove-btn" onclick="this.closest(\'.ew-edu-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="education['+i+'][degree_name]" value=""><input type="hidden" name="education['+i+'][institution]" value=""><input type="hidden" name="education['+i+'][passing_year]" value=""><input type="hidden" name="education['+i+'][result]" value=""><input type="hidden" name="education['+i+'][certification_type]" value="education"><div class="row g-2"><div class="col-md-3"><label class="form-label">Degree <span class="text-danger">*</span></label><select class="form-select" onchange="ewEduHidden(this,\'degree_name\')"><option value="">Choose</option><option value="Secondary School Certificate (SSC)">SSC</option><option value="Higher Secondary Certificate (HSC)">HSC</option><option value="Bachelor of Science (B.Sc.)">B.Sc.</option><option value="Bachelor of Business Administration (BBA)">BBA</option><option value="Bachelor of Arts (BA)">BA</option><option value="Master of Science (M.Sc.)">M.Sc.</option><option value="Master of Business Administration (MBA)">MBA</option><option value="Diploma in Engineering">Diploma</option></select></div><div class="col-md-3"><label class="form-label">Institution <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewEduHidden(this,\'institution\')" placeholder="Name"></div><div class="col-md-2"><label class="form-label">Year <span class="text-danger">*</span></label><select class="form-select" onchange="ewEduHidden(this,\'passing_year\')">'+yOpts+'</select></div><div class="col-md-2"><label class="form-label">Result <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewEduHidden(this,\'result\')" placeholder="3.85"></div><div class="col-md-2"><label class="form-label">Type</label><select class="form-select" onchange="ewEduHidden(this,\'certification_type\')"><option value="education">Academic</option><option value="training">Training</option></select></div></div><div class="row g-2 mt-1"><div class="col-12"><input type="file" class="form-control form-control-sm" name="education_doc['+i+']" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;"></div></div>';
        c.appendChild(d);
    };

    function ewExpHidden(el, field) {
        const h = el.closest('.ew-exp-row').querySelector(`input[name$="[${field}]"], textarea[name$="[${field}]"]`);
        if (h) h.value = el.value;
    }
    window.ewExpHidden = ewExpHidden;
    let expIdx = 1;
    window.ewAddExp = function() {
        const c = document.getElementById('ew-exp-container');
        const i = expIdx++;
        const d = document.createElement('div');
        d.className = 'ew-dynamic-row ew-exp-row';
        d.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><span style="font-size:0.68rem;font-weight:700;color:#475569;"><i class="bx bx-building me-1"></i>Experience #'+(i+1)+'</span><button type="button" class="btn btn-sm btn-light border text-danger ew-remove-btn" onclick="this.closest(\'.ew-exp-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="experiences['+i+'][company_name]" value=""><input type="hidden" name="experiences['+i+'][designation]" value=""><input type="hidden" name="experiences['+i+'][start_date]" value=""><input type="hidden" name="experiences['+i+'][end_date]" value=""><input type="hidden" name="experiences['+i+'][job_description]" value=""><div class="row g-2"><div class="col-md-4"><label class="form-label">Company</label><input type="text" class="form-control" oninput="ewExpHidden(this,\'company_name\')" placeholder="Company"></div><div class="col-md-3"><label class="form-label">Designation</label><input type="text" class="form-control" oninput="ewExpHidden(this,\'designation\')" placeholder="Role"></div><div class="col-md-2"><label class="form-label">Start</label><input type="date" class="form-control" onchange="ewExpHidden(this,\'start_date\')"></div><div class="col-md-3"><label class="form-label">End</label><input type="date" class="form-control" onchange="ewExpHidden(this,\'end_date\')"></div></div><div class="row g-2 mt-1"><div class="col-12"><label class="form-label">Responsibilities</label><textarea class="form-control" rows="2" oninput="ewExpHidden(this,\'job_description\')" placeholder="Deliverables..." style="font-size:0.7rem;"></textarea></div></div><div class="row g-2 mt-1"><div class="col-12"><input type="file" class="form-control form-control-sm" name="experience_doc['+i+']" accept=".jpg,.jpeg,.png,.pdf" style="font-size:0.65rem;"></div></div>';
        c.appendChild(d);
    };

    function ewDepHidden(el, field) {
        const h = el.closest('.ew-dep-row').querySelector(`input[name$="[${field}]"]`);
        if (h) h.value = el.value;
    }
    window.ewDepHidden = ewDepHidden;
    let depIdx = 1;
    window.ewAddDep = function() {
        const c = document.getElementById('ew-dep-container');
        const i = depIdx++;
        const d = document.createElement('div');
        d.className = 'ew-dynamic-row ew-dep-row';
        d.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><span style="font-size:0.68rem;font-weight:700;color:#475569;"><i class="bx bx-user me-1"></i>Member #'+(i+1)+'</span><button type="button" class="btn btn-sm btn-light border text-danger ew-remove-btn" onclick="this.closest(\'.ew-dep-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="dependents['+i+'][name]" value=""><input type="hidden" name="dependents['+i+'][relationship]" value=""><input type="hidden" name="dependents['+i+'][phone]" value=""><div class="row g-2"><div class="col-md-4"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewDepHidden(this,\'name\')" placeholder="Name"></div><div class="col-md-4"><label class="form-label">Relationship <span class="text-danger">*</span></label><select class="form-select" onchange="ewDepHidden(this,\'relationship\')"><option value="">Select</option>@foreach(['Father','Mother','Spouse','Brother','Sister','Son','Daughter','Other'] as $rel)<option value="{{ $rel }}">{{ $rel }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Phone</label><input type="text" class="form-control" oninput="ewDepHidden(this,\'phone\')" placeholder="Phone"></div></div>';
        c.appendChild(d);
    };

    function ewNomHidden(el, field) {
        const h = el.closest('.ew-nom-row').querySelector(`input[name$="[${field}]"]`);
        if (h) h.value = el.value;
    }
    window.ewNomHidden = ewNomHidden;
    let nomIdx = 1;
    window.ewAddNom = function() {
        const c = document.getElementById('ew-nom-container');
        const i = nomIdx++;
        const d = document.createElement('div');
        d.className = 'ew-dynamic-row ew-nom-row';
        d.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><span style="font-size:0.68rem;font-weight:700;color:#475569;"><i class="bx bx-user-check me-1"></i>Nominee #'+(i+1)+'</span><button type="button" class="btn btn-sm btn-light border text-danger ew-remove-btn" onclick="this.closest(\'.ew-nom-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="nominees['+i+'][name]" value=""><input type="hidden" name="nominees['+i+'][relationship]" value=""><input type="hidden" name="nominees['+i+'][share_percentage]" value="100"><input type="hidden" name="nominees['+i+'][identity_document_type]" value=""><input type="hidden" name="nominees['+i+'][identity_document_number]" value=""><div class="row g-2"><div class="col-md-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewNomHidden(this,\'name\')" placeholder="Name"></div><div class="col-md-2"><label class="form-label">Relation <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="ewNomHidden(this,\'relationship\')" placeholder="e.g. Spouse"></div><div class="col-md-2"><label class="form-label">Share %</label><input type="number" class="form-control" value="100" oninput="ewNomHidden(this,\'share_percentage\')" min="0" max="100"></div><div class="col-md-2"><label class="form-label">ID Type</label><select class="form-select" onchange="ewNomHidden(this,\'identity_document_type\')"><option value="">Select</option><option value="NID">NID</option><option value="Passport">Passport</option><option value="Birth Certificate">Birth Cert</option></select></div><div class="col-md-3"><label class="form-label">ID Number</label><input type="text" class="form-control" oninput="ewNomHidden(this,\'identity_document_number\')" placeholder="ID No"></div></div>';
        c.appendChild(d);
    };

    // --- Load drafts on page load ---
    loadDrafts();
})();
</script>
@endpush
@endsection
