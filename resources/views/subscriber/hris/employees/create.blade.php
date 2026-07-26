@extends('layouts.subscriber')

@section('title', 'Add New Employee')

@section('content')
<style>
    .wizard-progress-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
    }
    .wizard-step-line {
        position: absolute;
        top: 20px;
        left: 5%;
        right: 5%;
        height: 3px;
        background-color: #e2e8f0;
        z-index: 1;
    }
    .wizard-step-progress-line {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: var(--color-primary);
        width: 0%;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .step-indicator {
        z-index: 2;
        text-align: center;
        width: 120px;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 2px solid #cbd5e1;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        margin: 0 auto;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .step-indicator.active .step-circle {
        border-color: var(--color-primary);
        background-color: var(--color-primary);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(95, 90, 246, 0.25);
    }
    .step-indicator.completed .step-circle {
        border-color: #10b981;
        background-color: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
    }
    .step-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-top: 10px;
        transition: color 0.3s;
    }
    .step-indicator.active .step-label {
        color: var(--color-primary);
    }
    .step-indicator.completed .step-label {
        color: #10b981;
    }
    .salary-calc-box {
        background: linear-gradient(135deg, rgba(95, 90, 246, 0.04), rgba(139, 92, 246, 0.04));
        border: 1px dashed rgba(95, 90, 246, 0.25) !important;
        border-radius: 12px;
    }
    .form-section-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-primary);
        border-bottom: 2px solid rgba(95, 90, 246, 0.1);
        padding-bottom: 8px;
        margin-bottom: 20px;
    }
    .dynamic-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    .dynamic-row:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .remove-row-btn {
        width: 32px;
        height: 32px;
        border-radius: 50% !important;
        min-height: auto !important;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Directory</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;"><i class="bx bx-user-plus text-primary me-2 align-middle"></i>Add New Employee Profile</h4>
    </div>
    <div class="page-title-right">
        <a href="{{ route('subscriber.hris.employees.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-body py-4">
        <div class="wizard-progress-container">
            <div class="wizard-step-line">
                <div id="wizard-progress-bar" class="wizard-step-progress-line"></div>
            </div>
            <div class="step-indicator active" data-step="1"><div class="step-circle">1</div><div class="step-label">Basic Info</div></div>
            <div class="step-indicator" data-step="2"><div class="step-circle">2</div><div class="step-label">Official Details</div></div>
            <div class="step-indicator" data-step="3"><div class="step-circle">3</div><div class="step-label">Compensation</div></div>
            <div class="step-indicator" data-step="4"><div class="step-circle">4</div><div class="step-label">Qualifications</div></div>
            <div class="step-indicator" data-step="5"><div class="step-circle">5</div><div class="step-label">Personal Info</div></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form id="employee-wizard-form" action="{{ route('subscriber.hris.employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="wizard-step" id="step-1">
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom py-3.5">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-user text-primary me-2 font-size-22 align-middle"></i> Step 1: Personal & Primary Contact Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-6 border-end pe-lg-4">
                                <h6 class="form-section-title"><i class="bx bx-id-card me-1.5 align-middle"></i>Personal Details</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}" placeholder="e.g. Rahim Ahmed">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address (Login Username) <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}" placeholder="e.g. rahim@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Account Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 8 characters">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" required value="{{ old('phone_number') }}" placeholder="e.g. 01712345678">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="dob" name="dob" required value="{{ old('dob') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            @foreach($genders as $gender)
                                                <option value="{{ $gender->name }}" {{ old('gender') === $gender->name ? 'selected' : '' }}>{{ $gender->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="blood_group" class="form-label">Blood Group</label>
                                        <input type="text" class="form-control" id="blood_group" name="blood_group" value="{{ old('blood_group') }}" placeholder="e.g. A+ / O-">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 ps-lg-4">
                                <h6 class="form-section-title"><i class="bx bx-map-pin me-1.5 align-middle"></i>Current Address Details</h6>
                                <div class="mb-3">
                                    <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" required value="{{ old('address_line_1') }}" placeholder="e.g. Apt 4B, House 12, Road 4">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="division_select" class="form-label">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="division_select" required>
                                            <option value="">Select Division</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}" data-name="{{ $div->name }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="state" name="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="district_select" class="form-label">District <span class="text-danger">*</span></label>
                                        <select class="form-select" id="district_select" required disabled>
                                            <option value="">Select District</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="thana_select" class="form-label">Thana / Upazila <span class="text-danger">*</span></label>
                                        <select class="form-select" id="thana_select" required disabled>
                                            <option value="">Select Thana</option>
                                        </select>
                                        <input type="hidden" id="city" name="city" value="{{ old('city') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="zip_code" class="form-label">Zip Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" required value="{{ old('zip_code') }}" placeholder="e.g. 1212">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light" id="country" name="country" value="Bangladesh" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-step" id="step-2" style="display: none;">
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom py-3.5">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-briefcase text-primary me-2 font-size-22 align-middle"></i> Step 2: Official & Employment Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee ID (Card Number) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="employee_id" name="employee_id" required value="{{ old('employee_id') }}" placeholder="e.g. EMP-1054">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="joining_date" name="joining_date" required value="{{ old('joining_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
                                    <select class="form-select" id="designation_id" name="designation_id" required>
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $desig)
                                            <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">Assigned Work Shift</label>
                                    <select class="form-select" id="shift_id" name="shift_id">
                                        <option value="">Select Work Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Employment Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="probation" {{ old('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        <option value="resigned" {{ old('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-step" id="step-3" style="display: none;">
                <div class="card border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3.5">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-money text-primary me-2 font-size-22 align-middle"></i> Step 3: Salary Structure & Bank Configurations
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-lg-6">
                                <div class="p-4 salary-calc-box border">
                                    <label for="gross_salary_input" class="form-label text-indigo-700">Gross Salary Input (BDT) <span class="text-danger">*</span></label>
                                    <div class="input-group" style="box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                                        <span class="input-group-text bg-white border-end-0 text-slate-500 fw-bold">&#2547;</span>
                                        <input type="number" class="form-control form-control-lg border-start-0 fw-bold text-indigo-900" id="gross_salary_input" placeholder="e.g. 50000" style="height: 52px !important;">
                                    </div>
                                    <div class="form-text text-slate-500 mt-3 font-size-12">
                                        @if($activeSalaryRelation)
                                            <i class="bx bx-info-circle text-primary me-1"></i> Active Split Formula: <strong>{{ $activeSalaryRelation->name }}</strong> <br>
                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                <span class="badge bg-soft-primary text-primary">Basic: {{ (int)$activeSalaryRelation->basic_percent }}%</span>
                                                <span class="badge bg-soft-primary text-primary">House Rent: {{ (int)$activeSalaryRelation->house_rent_percent }}%</span>
                                                <span class="badge bg-soft-primary text-primary">Medical: {{ (int)$activeSalaryRelation->medical_percent }}%</span>
                                                <span class="badge bg-soft-primary text-primary">TA/DA: {{ (int)$activeSalaryRelation->tada_percent }}%</span>
                                            </div>
                                        @else
                                            <i class="bx bx-info-circle text-warning me-1"></i> No active split formula configured.
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-4 border rounded-4 bg-light">
                                    <h6 class="form-section-title"><i class="bx bx-credit-card me-1.5 align-middle"></i>Bank Transfer Details</h6>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><input type="text" class="form-control" name="bank_name" required placeholder="Bank Name"></div>
                                        <div class="col-6"><input type="text" class="form-control" name="branch_name" required placeholder="Branch Name"></div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><input type="text" class="form-control" name="account_name" required placeholder="Account Holder Name"></div>
                                        <div class="col-6"><input type="text" class="form-control" name="account_number" required placeholder="Account Number"></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><input type="text" class="form-control" name="routing_number" placeholder="Routing Number"></div>
                                        <div class="col-6">
                                            <select class="form-select" name="payment_mode" required>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="cash">Cash Payment</option>
                                                <option value="mobile_banking">Mobile Banking</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="form-section-title"><i class="bx bx-slider-alt me-1.5 align-middle"></i>Salary Structure Breakdown</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="basic_salary" class="form-label">Basic Salary (&#2547;) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control salary-breakdown" id="basic_salary" name="basic_salary" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="house_rent" class="form-label">House Rent (&#2547;) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control salary-breakdown" id="house_rent" name="house_rent" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="medical_allowance" class="form-label">Medical Allowance (&#2547;) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control salary-breakdown" id="medical_allowance" name="medical_allowance" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="conveyance_allowance" class="form-label">TA / DA (&#2547;) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control salary-breakdown" id="conveyance_allowance" name="conveyance_allowance" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="other_allowances" class="form-label">Other Allowances (&#2547;)</label>
                                    <input type="number" class="form-control" id="other_allowances" name="other_allowances" value="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="provident_fund_deduction" class="form-label text-danger">Provident Fund (&#2547;)</label>
                                    <input type="number" class="form-control" id="provident_fund_deduction" name="provident_fund_deduction" value="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_deduction" class="form-label text-danger">Tax Deduction (&#2547;)</label>
                                    <input type="number" class="form-control" id="tax_deduction" name="tax_deduction" value="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-step" id="step-4" style="display: none;">
                <div class="card border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3.5">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-book-bookmark text-primary me-2 font-size-22 align-middle"></i> Step 4: Academic Qualifications & Ex-Employment
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-graduation me-1.5 align-middle"></i>Academic Degrees / Certificates</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addEducationRow()" style="min-height: auto; height: 32px;">
                                <i class="bx bx-plus me-1"></i> Add Degree
                            </button>
                        </h6>

                        <div id="education-container">
                            <div class="dynamic-row education-row">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-book me-1"></i> Degree #1</span>
                                </div>
                                <input type="hidden" name="education[0][degree_name]" value="">
                                <input type="hidden" name="education[0][institution]" value="">
                                <input type="hidden" name="education[0][passing_year]" value="">
                                <input type="hidden" name="education[0][result]" value="">
                                <input type="hidden" name="education[0][certification_type]" value="education">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Degree / Certificate <span class="text-danger">*</span></label>
                                        <select class="form-select edu-degree" onchange="updateEduHidden(this, 'degree_name')">
                                            <option value="">Choose Degree</option>
                                            <option value="Secondary School Certificate (SSC)">SSC</option>
                                            <option value="Higher Secondary Certificate (HSC)">HSC</option>
                                            <option value="Bachelor of Science (B.Sc.)">B.Sc.</option>
                                            <option value="Bachelor of Business Administration (BBA)">BBA</option>
                                            <option value="Bachelor of Arts (BA)">BA</option>
                                            <option value="Master of Science (M.Sc.)">M.Sc.</option>
                                            <option value="Master of Business Administration (MBA)">MBA</option>
                                            <option value="Diploma in Engineering">Diploma in Engineering</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Institution <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" oninput="updateEduHidden(this, 'institution')" placeholder="Institution name">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Passing Year <span class="text-danger">*</span></label>
                                        <select class="form-select" onchange="updateEduHidden(this, 'passing_year')">
                                            <option value="">Year</option>
                                            @for($y = date('Y'); $y >= 1980; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Result <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" oninput="updateEduHidden(this, 'result')" placeholder="e.g. 3.85">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" onchange="updateEduHidden(this, 'certification_type')">
                                            <option value="education">Academic</option>
                                            <option value="training">Training</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <label class="form-label">Certificate Document</label>
                                        <input type="file" class="form-control form-control-sm" name="education_doc[0]" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-briefcase me-1.5 align-middle"></i>Previous Experience History</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addExperienceRow()" style="min-height: auto; height: 32px;">
                                <i class="bx bx-plus me-1"></i> Add Experience
                            </button>
                        </h6>

                        <div id="experiences-container">
                            <div class="dynamic-row experience-row">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-building me-1"></i> Experience #1</span>
                                </div>
                                <input type="hidden" name="experiences[0][company_name]" value="">
                                <input type="hidden" name="experiences[0][designation]" value="">
                                <input type="hidden" name="experiences[0][start_date]" value="">
                                <input type="hidden" name="experiences[0][end_date]" value="">
                                <input type="hidden" name="experiences[0][job_description]" value="">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" oninput="updateExpHidden(this, 'company_name')" placeholder="e.g. Nexozaint Tech">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Designation</label>
                                        <input type="text" class="form-control" oninput="updateExpHidden(this, 'designation')" placeholder="e.g. Junior Web Developer">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" onchange="updateExpHidden(this, 'start_date')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" onchange="updateExpHidden(this, 'end_date')">
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-12">
                                        <label class="form-label">Key Responsibilities</label>
                                        <textarea class="form-control" rows="2" oninput="updateExpHidden(this, 'job_description')" placeholder="Describe key deliverables..."></textarea>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <label class="form-label">Experience Letter / Document</label>
                                        <input type="file" class="form-control form-control-sm" name="experience_doc[0]" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-step" id="step-5" style="display: none;">
                <div class="card border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3.5">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-user-detail text-primary me-2 font-size-22 align-middle"></i> Step 5: Personal Information & Family Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="form-section-title"><i class="bx bx-home me-1.5 align-middle"></i>Present / Permanent Address</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="permanent_address_line_1" class="form-label">Present Address Line</label>
                                    <input type="text" class="form-control" id="permanent_address_line_1" name="permanent_address_line_1" value="{{ old('permanent_address_line_1') }}" placeholder="Village, Road, House No">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="permanent_city" class="form-label">City / District</label>
                                    <input type="text" class="form-control" id="permanent_city" name="permanent_city" value="{{ old('permanent_city') }}" placeholder="e.g. Dhaka">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="permanent_state" class="form-label">Division</label>
                                    <input type="text" class="form-control" id="permanent_state" name="permanent_state" value="{{ old('permanent_state') }}" placeholder="e.g. Dhaka">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="permanent_zip_code" class="form-label">Post Code</label>
                                    <input type="text" class="form-control" id="permanent_zip_code" name="permanent_zip_code" value="{{ old('permanent_zip_code') }}" placeholder="e.g. 1212">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="permanent_country" class="form-label">Country</label>
                                    <input type="text" class="form-control bg-light" id="permanent_country" name="permanent_country" value="Bangladesh" readonly>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="form-section-title"><i class="bx bx-id-card me-1.5 align-middle"></i>Identity & Personal Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nid" class="form-label">National ID (NID) Number</label>
                                    <input type="text" class="form-control" id="nid" name="nid" value="{{ old('nid') }}" placeholder="e.g. 1234567890">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 text-muted">NID Document</label>
                                    <input type="file" class="form-control form-control-sm" name="doc_nid" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_certificate" class="form-label">Birth Certificate Number</label>
                                    <input type="text" class="form-control" id="birth_certificate" name="birth_certificate" value="{{ old('birth_certificate') }}" placeholder="e.g. 1234567890">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="religion" class="form-label">Religion</label>
                                    <select class="form-select" id="religion" name="religion">
                                        <option value="">Select</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Hinduism">Hinduism</option>
                                        <option value="Christianity">Christianity</option>
                                        <option value="Buddhism">Buddhism</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 text-muted">Religion Document</label>
                                    <input type="file" class="form-control form-control-sm" name="doc_religion" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-select" id="marital_status" name="marital_status">
                                        <option value="">Select</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label font-size-12 text-muted">NOC Document</label>
                                    <input type="file" class="form-control form-control-sm" name="doc_noc" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label font-size-12 text-muted">Police Clearance Certificate</label>
                                    <input type="file" class="form-control form-control-sm" name="doc_police_clearance" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                        </div>

                        <h6 class="form-section-title"><i class="bx bx-user-voice me-1.5 align-middle"></i>Father & Mother Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="father_name" class="form-label">Father's Name</label>
                                    <input type="text" class="form-control" id="father_name" name="father_name" value="{{ old('father_name') }}" placeholder="Father's full name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="father_occupation" class="form-label">Father's Occupation</label>
                                    <input type="text" class="form-control" id="father_occupation" name="father_occupation" value="{{ old('father_occupation') }}" placeholder="e.g. Businessman">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="mother_name" class="form-label">Mother's Name</label>
                                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="{{ old('mother_name') }}" placeholder="Mother's full name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="mother_occupation" class="form-label">Mother's Occupation</label>
                                    <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" value="{{ old('mother_occupation') }}" placeholder="e.g. Homemaker">
                                </div>
                            </div>
                        </div>

                        <h6 class="form-section-title"><i class="bx bx-shield-quarter me-1.5 align-middle"></i>Guardian Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="guardian_name" class="form-label">Guardian Name</label>
                                    <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Guardian full name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="guardian_relation" class="form-label">Relation with Guardian</label>
                                    <input type="text" class="form-control" id="guardian_relation" name="guardian_relation" value="{{ old('guardian_relation') }}" placeholder="e.g. Uncle, Brother">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="guardian_phone" class="form-label">Guardian Phone</label>
                                    <input type="text" class="form-control" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="e.g. 01712345678">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-group me-1.5 align-middle"></i>Family Members / Dependents</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addDependentRow()" style="min-height: auto; height: 32px;">
                                <i class="bx bx-plus me-1"></i> Add Member
                            </button>
                        </h6>

                        <div id="dependents-container">
                            <div class="dynamic-row dependent-row">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-user me-1"></i> Family Member #1</span>
                                </div>
                                <input type="hidden" name="dependents[0][name]" value="">
                                <input type="hidden" name="dependents[0][relationship]" value="">
                                <input type="hidden" name="dependents[0][phone]" value="">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" oninput="updateDepHidden(this, 'name')" placeholder="Name">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                        <select class="form-select" onchange="updateDepHidden(this, 'relationship')">
                                            <option value="">Select</option>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Spouse">Spouse</option>
                                            <option value="Brother">Brother</option>
                                            <option value="Sister">Sister</option>
                                            <option value="Son">Son</option>
                                            <option value="Daughter">Daughter</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" oninput="updateDepHidden(this, 'phone')" placeholder="Phone number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-user-check me-1.5 align-middle"></i>Nominee Information</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addNomineeRow()" style="min-height: auto; height: 32px;">
                                <i class="bx bx-plus me-1"></i> Add Nominee
                            </button>
                        </h6>

                        <div id="nominees-container">
                            <div class="dynamic-row nominee-row">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-user-check me-1"></i> Nominee #1</span>
                                </div>
                                <input type="hidden" name="nominees[0][name]" value="">
                                <input type="hidden" name="nominees[0][relationship]" value="">
                                <input type="hidden" name="nominees[0][share_percentage]" value="100">
                                <input type="hidden" name="nominees[0][identity_document_type]" value="">
                                <input type="hidden" name="nominees[0][identity_document_number]" value="">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" oninput="updateNomHidden(this, 'name')" placeholder="Nominee name">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" oninput="updateNomHidden(this, 'relationship')" placeholder="e.g. Spouse, Son">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Share (%)</label>
                                        <input type="number" class="form-control" value="100" oninput="updateNomHidden(this, 'share_percentage')" min="0" max="100">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">ID Type</label>
                                        <select class="form-select" onchange="updateNomHidden(this, 'identity_document_type')">
                                            <option value="">Select</option>
                                            <option value="NID">NID</option>
                                            <option value="Passport">Passport</option>
                                            <option value="Birth Certificate">Birth Certificate</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">ID Number</label>
                                        <input type="text" class="form-control" oninput="updateNomHidden(this, 'identity_document_number')" placeholder="ID number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 mb-5">
                <button type="button" id="prev-btn" class="btn btn-secondary px-4 py-2.5 rounded-pill shadow-sm" style="display: none;">
                    <i class="bx bx-chevron-left align-middle font-size-18 me-1"></i> Previous Step
                </button>
                <div></div>
                <button type="button" id="next-btn" class="btn btn-primary px-5 py-2.5 rounded-pill shadow-sm">
                    Next Step <i class="bx bx-chevron-right align-middle font-size-18 ms-1"></i>
                </button>
                <button type="submit" id="submit-btn" class="btn btn-success px-5 py-2.5 rounded-pill shadow-sm" style="display: none; background: linear-gradient(135deg, #10b981, #059669) !important; border: 0 !important;">
                    <i class="bx bx-check-circle align-middle font-size-18 me-1"></i> Save Employee Profile
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 5;

    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const progressBar = document.getElementById('wizard-progress-bar');
    const form = document.getElementById('employee-wizard-form');

    function updateStepIndicator() {
        const pct = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = pct + '%';
        document.querySelectorAll('.step-indicator').forEach(ind => {
            const stepNum = parseInt(ind.getAttribute('data-step'));
            ind.classList.remove('active', 'completed');
            const circle = ind.querySelector('.step-circle');
            if (stepNum === currentStep) { ind.classList.add('active'); circle.textContent = stepNum; }
            else if (stepNum < currentStep) { ind.classList.add('completed'); circle.innerHTML = '<i class="bx bx-check"></i>'; }
            else { circle.textContent = stepNum; }
        });
    }

    function showStep(step) {
        document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';
        prevBtn.style.display = step === 1 ? 'none' : 'block';
        if (step === totalSteps) { nextBtn.style.display = 'none'; submitBtn.style.display = 'block'; }
        else { nextBtn.style.display = 'block'; submitBtn.style.display = 'none'; }
        currentStep = step;
        updateStepIndicator();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step) {
        const stepDiv = document.getElementById('step-' + step);
        const requiredFields = stepDiv.querySelectorAll('[required]');
        let isValid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim() && field.disabled === false) { field.classList.add('is-invalid'); isValid = false; }
            else { field.classList.remove('is-invalid'); }
        });
        if (step === 1) {
            const email = document.getElementById('email');
            if (email.value && !email.value.includes('@')) { email.classList.add('is-invalid'); isValid = false; }
        }
        return isValid;
    }

    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) showStep(currentStep + 1);
        else alert('Please fill out all required fields marked with * correctly.');
    });
    prevBtn.addEventListener('click', function() { showStep(currentStep - 1); });
    form.addEventListener('submit', function(e) {
        if (!validateStep(totalSteps)) { e.preventDefault(); alert('Please fill out all required fields in the final step.'); }
    });

    function updateEduHidden(el, field) {
        const row = el.closest('.education-row');
        const hidden = row.querySelector('input[name$="[' + field + ']"]');
        if (hidden) hidden.value = el.value;
    }
    let eduIndex = 1;

    function updateExpHidden(el, field) {
        const row = el.closest('.experience-row');
        const hidden = row.querySelector('input[name$="[' + field + ']"], textarea[name$="[' + field + ']"]');
        if (hidden) hidden.value = el.value;
    }
    let expIndex = 1;
    function addExperienceRow() {
        const container = document.getElementById('experiences-container');
        const idx = expIndex++;
        const row = document.createElement('div');
        row.className = 'dynamic-row experience-row';
        row.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-building me-1"></i> Experience #' + (idx + 1) + '</span><button type="button" class="btn btn-sm btn-light border text-danger remove-row-btn" onclick="this.closest(\'.experience-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="experiences[' + idx + '][company_name]" value=""><input type="hidden" name="experiences[' + idx + '][designation]" value=""><input type="hidden" name="experiences[' + idx + '][start_date]" value=""><input type="hidden" name="experiences[' + idx + '][end_date]" value=""><input type="hidden" name="experiences[' + idx + '][job_description]" value=""><div class="row g-3"><div class="col-md-4"><label class="form-label">Company Name</label><input type="text" class="form-control" oninput="updateExpHidden(this, \'company_name\')" placeholder="e.g. Nexozaint Tech"></div><div class="col-md-4"><label class="form-label">Designation</label><input type="text" class="form-control" oninput="updateExpHidden(this, \'designation\')" placeholder="e.g. Junior Web Developer"></div><div class="col-md-2"><label class="form-label">Start Date</label><input type="date" class="form-control" onchange="updateExpHidden(this, \'start_date\')"></div><div class="col-md-2"><label class="form-label">End Date</label><input type="date" class="form-control" onchange="updateExpHidden(this, \'end_date\')"></div></div><div class="row g-3 mt-1"><div class="col-12"><label class="form-label">Key Responsibilities</label><textarea class="form-control" rows="2" oninput="updateExpHidden(this, \'job_description\')" placeholder="Describe key deliverables..."></textarea></div></div><div class="row g-3 mt-2"><div class="col-12"><label class="form-label">Experience Letter / Document</label><input type="file" class="form-control form-control-sm" name="experience_doc[' + idx + ']" accept=".jpg,.jpeg,.png,.pdf"></div></div>';
        container.appendChild(row);
    }
    function addEducationRow() {
        const container = document.getElementById('education-container');
        const idx = eduIndex++;
        const row = document.createElement('div');
        row.className = 'dynamic-row education-row';
        row.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-book me-1"></i> Degree #' + (idx + 1) + '</span><button type="button" class="btn btn-sm btn-light border text-danger remove-row-btn" onclick="this.closest(\'.education-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="education[' + idx + '][degree_name]" value=""><input type="hidden" name="education[' + idx + '][institution]" value=""><input type="hidden" name="education[' + idx + '][passing_year]" value=""><input type="hidden" name="education[' + idx + '][result]" value=""><input type="hidden" name="education[' + idx + '][certification_type]" value="education"><div class="row g-3"><div class="col-md-3"><label class="form-label">Degree <span class="text-danger">*</span></label><select class="form-select" onchange="updateEduHidden(this, \'degree_name\')"><option value="">Choose Degree</option><option value="Secondary School Certificate (SSC)">SSC</option><option value="Higher Secondary Certificate (HSC)">HSC</option><option value="Bachelor of Science (B.Sc.)">B.Sc.</option><option value="Bachelor of Business Administration (BBA)">BBA</option><option value="Bachelor of Arts (BA)">BA</option><option value="Master of Science (M.Sc.)">M.Sc.</option><option value="Master of Business Administration (MBA)">MBA</option><option value="Diploma in Engineering">Diploma in Engineering</option></select></div><div class="col-md-3"><label class="form-label">Institution <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="updateEduHidden(this, \'institution\')" placeholder="Institution name"></div><div class="col-md-2"><label class="form-label">Passing Year <span class="text-danger">*</span></label><select class="form-select" onchange="updateEduHidden(this, \'passing_year\')"><option value="">Year</option>@for($y = date('Y'); $y >= 1980; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor</select></div><div class="col-md-2"><label class="form-label">Result <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="updateEduHidden(this, \'result\')" placeholder="e.g. 3.85"></div><div class="col-md-2"><label class="form-label">Type</label><select class="form-select" onchange="updateEduHidden(this, \'certification_type\')"><option value="education">Academic</option><option value="training">Training</option></select></div></div><div class="row g-3 mt-2"><div class="col-12"><label class="form-label">Certificate Document</label><input type="file" class="form-control form-control-sm" name="education_doc[' + idx + ']" accept=".jpg,.jpeg,.png,.pdf"></div></div>';
        container.appendChild(row);
    }

    function updateDepHidden(el, field) {
        const row = el.closest('.dependent-row');
        const hidden = row.querySelector('input[name$="[' + field + ']"]');
        if (hidden) hidden.value = el.value;
    }
    let depIndex = 1;
    function addDependentRow() {
        const container = document.getElementById('dependents-container');
        const idx = depIndex++;
        const row = document.createElement('div');
        row.className = 'dynamic-row dependent-row';
        row.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-user me-1"></i> Family Member #' + (idx + 1) + '</span><button type="button" class="btn btn-sm btn-light border text-danger remove-row-btn" onclick="this.closest(\'.dependent-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="dependents[' + idx + '][name]" value=""><input type="hidden" name="dependents[' + idx + '][relationship]" value=""><input type="hidden" name="dependents[' + idx + '][phone]" value=""><div class="row g-3"><div class="col-md-4"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="updateDepHidden(this, \'name\')" placeholder="Name"></div><div class="col-md-4"><label class="form-label">Relationship <span class="text-danger">*</span></label><select class="form-select" onchange="updateDepHidden(this, \'relationship\')"><option value="">Select</option><option value="Father">Father</option><option value="Mother">Mother</option><option value="Spouse">Spouse</option><option value="Brother">Brother</option><option value="Sister">Sister</option><option value="Son">Son</option><option value="Daughter">Daughter</option><option value="Other">Other</option></select></div><div class="col-md-4"><label class="form-label">Phone</label><input type="text" class="form-control" oninput="updateDepHidden(this, \'phone\')" placeholder="Phone number"></div></div>';
        container.appendChild(row);
    }

    function updateNomHidden(el, field) {
        const row = el.closest('.nominee-row');
        const hidden = row.querySelector('input[name$="[' + field + ']"]');
        if (hidden) hidden.value = el.value;
    }
    let nomIndex = 1;
    function addNomineeRow() {
        const container = document.getElementById('nominees-container');
        const idx = nomIndex++;
        const row = document.createElement('div');
        row.className = 'dynamic-row nominee-row';
        row.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold text-slate-700 font-size-12"><i class="bx bx-user-check me-1"></i> Nominee #' + (idx + 1) + '</span><button type="button" class="btn btn-sm btn-light border text-danger remove-row-btn" onclick="this.closest(\'.nominee-row\').remove()"><i class="bx bx-x"></i></button></div><input type="hidden" name="nominees[' + idx + '][name]" value=""><input type="hidden" name="nominees[' + idx + '][relationship]" value=""><input type="hidden" name="nominees[' + idx + '][share_percentage]" value="100"><input type="hidden" name="nominees[' + idx + '][identity_document_type]" value=""><input type="hidden" name="nominees[' + idx + '][identity_document_number]" value=""><div class="row g-3"><div class="col-md-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="updateNomHidden(this, \'name\')" placeholder="Nominee name"></div><div class="col-md-3"><label class="form-label">Relationship <span class="text-danger">*</span></label><input type="text" class="form-control" oninput="updateNomHidden(this, \'relationship\')" placeholder="e.g. Spouse, Son"></div><div class="col-md-2"><label class="form-label">Share (%)</label><input type="number" class="form-control" value="100" oninput="updateNomHidden(this, \'share_percentage\')" min="0" max="100"></div><div class="col-md-2"><label class="form-label">ID Type</label><select class="form-select" onchange="updateNomHidden(this, \'identity_document_type\')"><option value="">Select</option><option value="NID">NID</option><option value="Passport">Passport</option><option value="Birth Certificate">Birth Certificate</option></select></div><div class="col-md-2"><label class="form-label">ID Number</label><input type="text" class="form-control" oninput="updateNomHidden(this, \'identity_document_number\')" placeholder="ID number"></div></div>';
        container.appendChild(row);
    }

    const divisionsData = @json($divisions);
    const divisionSelect = document.getElementById('division_select');
    const districtSelect = document.getElementById('district_select');
    const thanaSelect = document.getElementById('thana_select');
    const stateInput = document.getElementById('state');
    const cityInput = document.getElementById('city');

    divisionSelect.addEventListener('change', function() {
        const divId = this.value;
        const opt = this.options[this.selectedIndex];
        stateInput.value = opt ? opt.getAttribute('data-name') : '';
        districtSelect.innerHTML = '<option value="">Select District</option>';
        districtSelect.disabled = true;
        thanaSelect.innerHTML = '<option value="">Select Thana</option>';
        thanaSelect.disabled = true;
        cityInput.value = '';
        if (!divId) return;
        const division = divisionsData.find(d => d.id == divId);
        if (division && division.districts) {
            division.districts.forEach(dist => {
                const o = document.createElement('option');
                o.value = dist.id; o.textContent = dist.name; o.setAttribute('data-name', dist.name);
                districtSelect.appendChild(o);
            });
            districtSelect.disabled = false;
        }
    });

    districtSelect.addEventListener('change', function() {
        const distId = this.value;
        thanaSelect.innerHTML = '<option value="">Select Thana</option>';
        thanaSelect.disabled = true;
        cityInput.value = '';
        if (!distId) return;
        const division = divisionsData.find(d => d.id == divisionSelect.value);
        if (division) {
            const district = division.districts.find(d => d.id == distId);
            if (district && district.thanas) {
                district.thanas.forEach(thana => {
                    const o = document.createElement('option');
                    o.value = thana.id; o.textContent = thana.name; o.setAttribute('data-name', thana.name);
                    thanaSelect.appendChild(o);
                });
                thanaSelect.disabled = false;
            }
        }
    });

    thanaSelect.addEventListener('change', function() {
        const thanaName = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
        const distName = districtSelect.options[districtSelect.selectedIndex]?.getAttribute('data-name') || '';
        cityInput.value = (thanaName && distName) ? thanaName + ', ' + distName : '';
    });

    const activeSalaryRelation = @json($activeSalaryRelation);
    const grossSalaryInput = document.getElementById('gross_salary_input');
    if (activeSalaryRelation) {
        grossSalaryInput.addEventListener('input', function() {
            const gross = parseFloat(this.value) || 0;
            document.getElementById('basic_salary').value = (gross * (activeSalaryRelation.basic_percent / 100)).toFixed(2);
            document.getElementById('house_rent').value = (gross * (activeSalaryRelation.house_rent_percent / 100)).toFixed(2);
            document.getElementById('medical_allowance').value = (gross * (activeSalaryRelation.medical_percent / 100)).toFixed(2);
            document.getElementById('conveyance_allowance').value = (gross * (activeSalaryRelation.tada_percent / 100)).toFixed(2);
        });
    }
</script>
@endpush
@endsection
