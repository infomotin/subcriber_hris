@extends('layouts.subscriber')

@section('title', 'Add New Employee')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="bx bx-user-plus text-primary me-2"></i> Add New Employee Profile</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Wizard Steps Tracker -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between position-relative wizard-progress-container" style="max-width: 800px; margin: 0 auto;">
                    <!-- Line connector -->
                    <div class="position-absolute top-50 start-0 end-0 translate-middle-y bg-light-300" style="height: 4px; z-index: 1;">
                        <div id="wizard-progress-bar" class="bg-primary h-100 transition-all" style="width: 0%; transition: width 0.3s ease;"></div>
                    </div>

                    <!-- Step indicators -->
                    <div class="step-indicator active text-center position-relative" data-step="1" style="z-index: 2; width: 100px;">
                        <div class="step-circle mx-auto bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 40px; height: 40px; font-weight: bold; transition: all 0.3s;">1</div>
                        <div class="step-label mt-2 font-size-12 fw-medium text-primary">Basic Info</div>
                    </div>

                    <div class="step-indicator text-center position-relative" data-step="2" style="z-index: 2; width: 100px;">
                        <div class="step-circle mx-auto bg-light text-muted d-flex align-items-center justify-content-center rounded-circle border" style="width: 40px; height: 40px; font-weight: bold; transition: all 0.3s;">2</div>
                        <div class="step-label mt-2 font-size-12 fw-medium text-muted">Official Info</div>
                    </div>

                    <div class="step-indicator text-center position-relative" data-step="3" style="z-index: 2; width: 100px;">
                        <div class="step-circle mx-auto bg-light text-muted d-flex align-items-center justify-content-center rounded-circle border" style="width: 40px; height: 40px; font-weight: bold; transition: all 0.3s;">3</div>
                        <div class="step-label mt-2 font-size-12 fw-medium text-muted">Salary Info</div>
                    </div>

                    <div class="step-indicator text-center position-relative" data-step="4" style="z-index: 2; width: 100px;">
                        <div class="step-circle mx-auto bg-light text-muted d-flex align-items-center justify-content-center rounded-circle border" style="width: 40px; height: 40px; font-weight: bold; transition: all 0.3s;">4</div>
                        <div class="step-label mt-2 font-size-12 fw-medium text-muted">Education Info</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <form id="employee-wizard-form" action="{{ route('subscriber.hris.employees.store') }}" method="POST">
            @csrf

            <!-- STEP 1: Basic Info -->
            <div class="wizard-step" id="step-1">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-user text-primary me-2"></i> Step 1: Basic & Personal Info</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Personal info -->
                            <div class="col-lg-6">
                                <h6 class="fw-bold text-secondary mb-3">Personal Details</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}" placeholder="e.g. John Doe">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium">Email Address (Login Username) <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}" placeholder="e.g. john@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-medium">Account Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Min 8 characters">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" required value="{{ old('phone_number') }}" placeholder="e.g. +8801700000000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dob" class="form-label fw-medium">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="dob" name="dob" required value="{{ old('dob') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            @foreach($genders as $gender)
                                                <option value="{{ $gender->name }}" {{ old('gender') === $gender->name ? 'selected' : '' }}>{{ $gender->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="blood_group" class="form-label fw-medium">Blood Group</label>
                                        <input type="text" class="form-control" id="blood_group" name="blood_group" value="{{ old('blood_group') }}" placeholder="e.g. O+">
                                    </div>
                                </div>
                            </div>

                            <!-- Address info -->
                            <div class="col-lg-6">
                                <h6 class="fw-bold text-secondary mb-3">Current Address History</h6>
                                <div class="mb-3">
                                    <label for="address_line_1" class="form-label fw-medium">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" required value="{{ old('address_line_1') }}" placeholder="e.g. House 12, Road 4">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="division_select" class="form-label fw-medium">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="division_select" required>
                                            <option value="">Select Division</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}" data-name="{{ $div->name }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="state" name="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="district_select" class="form-label fw-medium">District <span class="text-danger">*</span></label>
                                        <select class="form-select" id="district_select" required disabled>
                                            <option value="">Select District</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="thana_select" class="form-label fw-medium">Thana / Upazila <span class="text-danger">*</span></label>
                                        <select class="form-select" id="thana_select" required disabled>
                                            <option value="">Select Thana</option>
                                        </select>
                                        <input type="hidden" id="city" name="city" value="{{ old('city') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="zip_code" class="form-label fw-medium">Zip Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" required value="{{ old('zip_code') }}" placeholder="e.g. 1209">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="country" class="form-label fw-medium">Country <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="country" name="country" value="Bangladesh" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Official Info -->
            <div class="wizard-step" id="step-2" style="display: none;">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-briefcase text-primary me-2"></i> Step 2: Official & Employment Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label fw-medium">Employee Card ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" required value="{{ old('employee_id') }}" placeholder="e.g. EMP2026001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joining_date" class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date" required value="{{ old('joining_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label fw-medium">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="designation_id" class="form-label fw-medium">Designation <span class="text-danger">*</span></label>
                                <select class="form-select" id="designation_id" name="designation_id" required>
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $desig)
                                        <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shift_id" class="form-label fw-medium">Assigned Work Shift</label>
                                <select class="form-select" id="shift_id" name="shift_id">
                                    <option value="">Select Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-medium">Employment Status <span class="text-danger">*</span></label>
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

            <!-- STEP 3: Salary Info -->
            <div class="wizard-step" id="step-3" style="display: none;">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-money text-primary me-2"></i> Step 3: Salary & Bank Structure Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Salary calculation helper -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <label for="gross_salary_input" class="form-label fw-bold">Gross Salary (BDT) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" class="form-control form-control-lg fw-bold" id="gross_salary_input" placeholder="Enter Gross Salary (e.g. 50000)">
                                    </div>
                                    <span class="form-text text-muted">
                                        @if($activeSalaryRelation)
                                            Active formula: <strong>{{ $activeSalaryRelation->name }}</strong> <br>
                                            Basic: {{ (int)$activeSalaryRelation->basic_percent }}% &middot; 
                                            House Rent: {{ (int)$activeSalaryRelation->house_rent_percent }}% &middot; 
                                            Medical: {{ (int)$activeSalaryRelation->medical_percent }}% &middot; 
                                            TA/DA: {{ (int)$activeSalaryRelation->tada_percent }}%
                                        @else
                                            No active split formula. Please enter manually.
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-3">Bank Transfer details</h6>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <input type="text" class="form-control" name="bank_name" required placeholder="Bank Name (e.g. DBBL)">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <input type="text" class="form-control" name="branch_name" required placeholder="Branch Name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <input type="text" class="form-control" name="account_name" required placeholder="Account Holder Name">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <input type="text" class="form-control" name="account_number" required placeholder="Account Number">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control" name="routing_number" placeholder="Routing Number">
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" name="payment_mode" required>
                                                <option value="bank">Bank Transfer</option>
                                                <option value="cash">Cash</option>
                                                <option value="mobile_banking">Mobile Banking</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Concrete breakdowns -->
                        <h6 class="fw-bold mb-3 text-secondary">Salary Structure breakdown</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="basic_salary" class="form-label fw-medium">Basic Salary (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control salary-breakdown" id="basic_salary" name="basic_salary" required placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="house_rent" class="form-label fw-medium">House Rent Allowance (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control salary-breakdown" id="house_rent" name="house_rent" required placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="medical_allowance" class="form-label fw-medium">Medical Allowance (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control salary-breakdown" id="medical_allowance" name="medical_allowance" required placeholder="0.00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="conveyance_allowance" class="form-label fw-medium">TA / DA Allowance (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control salary-breakdown" id="conveyance_allowance" name="conveyance_allowance" required placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="other_allowances" class="form-label fw-medium">Other Allowances (৳)</label>
                                <input type="number" class="form-control" id="other_allowances" name="other_allowances" value="0" placeholder="0.00">
                            </div>
                        </div>

                        <h6 class="fw-bold my-3 text-danger">Monthly Deductions</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provident_fund_deduction" class="form-label fw-medium">Provident Fund Deduction (৳)</label>
                                <input type="number" class="form-control" id="provident_fund_deduction" name="provident_fund_deduction" value="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tax_deduction" class="form-label fw-medium">Income Tax Deduction (৳)</label>
                                <input type="number" class="form-control" id="tax_deduction" name="tax_deduction" value="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Education Info -->
            <div class="wizard-step" id="step-4" style="display: none;">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-book-bookmark text-primary me-2"></i> Step 4: Academic & Qualification Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="degree_name" class="form-label fw-medium">Degree / Certificate Name <span class="text-danger">*</span></label>
                                <select class="form-select" id="degree_name" name="degree_name" required>
                                    <option value="">Choose Degree</option>
                                    <option value="Secondary School Certificate (SSC)">Secondary School Certificate (SSC)</option>
                                    <option value="Higher Secondary Certificate (HSC)">Higher Secondary Certificate (HSC)</option>
                                    <option value="Bachelor of Science (B.Sc.)">Bachelor of Science (B.Sc.)</option>
                                    <option value="Bachelor of Business Administration (BBA)">Bachelor of Business Administration (BBA)</option>
                                    <option value="Bachelor of Arts (BA)">Bachelor of Arts (BA)</option>
                                    <option value="Master of Science (M.Sc.)">Master of Science (M.Sc.)</option>
                                    <option value="Master of Business Administration (MBA)">Master of Business Administration (MBA)</option>
                                    <option value="Diploma in Engineering">Diploma in Engineering</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="board_select" class="form-label fw-medium">Education Board <span class="text-danger">*</span></label>
                                <select class="form-select" id="board_select" required>
                                    <option value="">Choose Board</option>
                                    @foreach($boards as $board)
                                        <option value="{{ $board->name }}">{{ $board->name }} Board</option>
                                    @endforeach
                                    <option value="other">Other / Not Applicable</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="institution_select" class="form-label fw-medium">Institution Name <span class="text-danger">*</span></label>
                                <select class="form-select" id="institution_select" required>
                                    <option value="">Choose Institution</option>
                                    @foreach($institutions as $inst)
                                        <option value="{{ $inst->name }}">{{ $inst->name }}</option>
                                    @endforeach
                                    <option value="custom">-- Write custom institution name --</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none" id="institution_custom" placeholder="Type institution name manually">
                                <input type="hidden" id="institution" name="institution">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="passing_year" class="form-label fw-medium">Passing Year <span class="text-danger">*</span></label>
                                <select class="form-select" id="passing_year" name="passing_year" required>
                                    <option value="">Year</option>
                                    @for($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="result" class="form-label fw-medium">Result (GPA / CGPA / Grade) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="result" name="result" required placeholder="e.g. 3.85 or GPA 5.0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="certification_type" class="form-label fw-medium">Certification Type</label>
                            <select class="form-select" id="certification_type" name="certification_type">
                                <option value="education">Academic Degree / Education</option>
                                <option value="training">Professional Training / Certification</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Controls -->
            <div class="d-flex justify-content-between mt-4 mb-5">
                <button type="button" id="prev-btn" class="btn btn-outline-secondary px-4 py-2.5 rounded-pill shadow-sm" style="display: none;">
                    <i class="bx bx-chevron-left align-middle font-size-18"></i> Previous Step
                </button>
                <div></div> <!-- Spacer -->
                <button type="button" id="next-btn" class="btn btn-primary px-5 py-2.5 rounded-pill shadow-sm">
                    Next Step <i class="bx bx-chevron-right align-middle font-size-18 ms-1"></i>
                </button>
                <button type="submit" id="submit-btn" class="btn btn-success px-5 py-2.5 rounded-pill shadow-sm" style="display: none;">
                    <i class="bx bx-check-circle align-middle font-size-18 me-1"></i> Save Employee Profile
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // 1. Wizard Steps Logic
    let currentStep = 1;
    const totalSteps = 4;

    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const progressBar = document.getElementById('wizard-progress-bar');
    const form = document.getElementById('employee-wizard-form');

    function updateStepIndicator() {
        // Update bar width
        const pct = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = pct + '%';

        // Update step circles
        document.querySelectorAll('.step-indicator').forEach(ind => {
            const stepNum = parseInt(ind.getAttribute('data-step'));
            const circle = ind.querySelector('.step-circle');
            const label = ind.querySelector('.step-label');

            if (stepNum === currentStep) {
                circle.className = "step-circle mx-auto bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm";
                label.className = "step-label mt-2 font-size-12 fw-medium text-primary";
            } else if (stepNum < currentStep) {
                circle.className = "step-circle mx-auto bg-success text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm";
                circle.innerHTML = '<i class="bx bx-check"></i>';
                label.className = "step-label mt-2 font-size-12 fw-medium text-success";
            } else {
                circle.className = "step-circle mx-auto bg-light text-muted d-flex align-items-center justify-content-center rounded-circle border";
                circle.textContent = stepNum;
                label.className = "step-label mt-2 font-size-12 fw-medium text-muted";
            }
        });
    }

    function showStep(step) {
        document.querySelectorAll('.wizard-step').forEach(stepDiv => {
            stepDiv.style.display = 'none';
        });
        document.getElementById('step-' + step).style.display = 'block';

        // Toggle buttons
        prevBtn.style.display = step === 1 ? 'none' : 'block';
        if (step === totalSteps) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }

        currentStep = step;
        updateStepIndicator();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Step-by-step form validation
    function validateStep(step) {
        const stepDiv = document.getElementById('step-' + step);
        const requiredFields = stepDiv.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim() && field.disabled === false) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Specific checks
        if (step === 1) {
            const email = document.getElementById('email');
            if (email.value && !email.value.includes('@')) {
                email.classList.add('is-invalid');
                isValid = false;
            }
        }

        return isValid;
    }

    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            showStep(currentStep + 1);
        } else {
            alert('Please fill out all required fields marked with * correctly before moving to the next step.');
        }
    });

    prevBtn.addEventListener('click', function() {
        showStep(currentStep - 1);
    });

    // Handle final submission validation
    form.addEventListener('submit', function(e) {
        if (!validateStep(4)) {
            e.preventDefault();
            alert('Please fill out all required fields in the final step.');
        }
    });


    // 2. Geographic Addresses Cascade Logic
    const divisionsData = @json($divisions);

    const divisionSelect = document.getElementById('division_select');
    const districtSelect = document.getElementById('district_select');
    const thanaSelect = document.getElementById('thana_select');
    const stateInput = document.getElementById('state');
    const cityInput = document.getElementById('city');

    divisionSelect.addEventListener('change', function() {
        const divId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        stateInput.value = selectedOption ? selectedOption.getAttribute('data-name') : '';

        // Reset
        districtSelect.innerHTML = '<option value="">Select District</option>';
        districtSelect.disabled = true;
        thanaSelect.innerHTML = '<option value="">Select Thana</option>';
        thanaSelect.disabled = true;
        cityInput.value = '';

        if (!divId) return;

        const division = divisionsData.find(d => d.id == divId);
        if (division && division.districts) {
            division.districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist.id;
                opt.textContent = dist.name;
                opt.setAttribute('data-name', dist.name);
                districtSelect.appendChild(opt);
            });
            districtSelect.disabled = false;
        }
    });

    districtSelect.addEventListener('change', function() {
        const distId = this.value;
        const selectedDistOption = this.options[this.selectedIndex];
        const distName = selectedDistOption ? selectedDistOption.getAttribute('data-name') : '';

        // Reset
        thanaSelect.innerHTML = '<option value="">Select Thana</option>';
        thanaSelect.disabled = true;
        cityInput.value = '';

        if (!distId) return;

        const divId = divisionSelect.value;
        const division = divisionsData.find(d => d.id == divId);
        if (division) {
            const district = division.districts.find(d => d.id == distId);
            if (district && district.thanas) {
                district.thanas.forEach(thana => {
                    const opt = document.createElement('option');
                    opt.value = thana.id;
                    opt.textContent = thana.name;
                    opt.setAttribute('data-name', thana.name);
                    thanaSelect.appendChild(opt);
                });
                thanaSelect.disabled = false;
            }
        }
    });

    thanaSelect.addEventListener('change', function() {
        const thanaId = this.value;
        const selectedThanaOption = this.options[this.selectedIndex];
        const thanaName = selectedThanaOption ? selectedThanaOption.getAttribute('data-name') : '';

        const selectedDistOption = districtSelect.options[districtSelect.selectedIndex];
        const distName = selectedDistOption ? selectedDistOption.getAttribute('data-name') : '';

        if (thanaName && distName) {
            cityInput.value = thanaName + ', ' + distName;
        } else {
            cityInput.value = '';
        }
    });


    // 3. Salary Relation Formula Auto-Split Calculator
    const activeSalaryRelation = @json($activeSalaryRelation);
    const grossSalaryInput = document.getElementById('gross_salary_input');

    if (activeSalaryRelation) {
        grossSalaryInput.addEventListener('input', function() {
            const gross = parseFloat(this.value) || 0;
            
            const basic = (gross * (parseFloat(activeSalaryRelation.basic_percent) / 100)).toFixed(2);
            const house = (gross * (parseFloat(activeSalaryRelation.house_rent_percent) / 100)).toFixed(2);
            const medical = (gross * (parseFloat(activeSalaryRelation.medical_percent) / 100)).toFixed(2);
            const tada = (gross * (parseFloat(activeSalaryRelation.tada_percent) / 100)).toFixed(2);

            document.getElementById('basic_salary').value = basic;
            document.getElementById('house_rent').value = house;
            document.getElementById('medical_allowance').value = medical;
            document.getElementById('conveyance_allowance').value = tada;
        });
    }


    // 4. Custom Institution Entry Logic
    const instSelect = document.getElementById('institution_select');
    const instCustom = document.getElementById('institution_custom');
    const instHidden = document.getElementById('institution');

    instSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            instCustom.classList.remove('d-none');
            instCustom.required = true;
            instHidden.value = instCustom.value;
        } else {
            instCustom.classList.add('d-none');
            instCustom.required = false;
            instHidden.value = this.value;
        }
    });

    instCustom.addEventListener('input', function() {
        instHidden.value = this.value;
    });

    // Initialize Institution hidden input on page load
    instHidden.value = instSelect.value;
</script>
@endpush
@endsection
