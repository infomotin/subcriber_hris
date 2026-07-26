@extends('layouts.subscriber')

@section('title', 'HRIS Master Setup')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="bx bx-slider-alt text-primary me-2"></i> HRIS Master Setup & Configurations</h4>
        </div>
        <p class="text-muted">Configure organization-wide master settings, geographic hierarchies, leave rules, and salary relations.</p>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-3">
        <!-- Sidebar Navigation Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link mb-2 py-3 px-4 d-flex align-items-center {{ $tab == 'sex' ? 'active' : '' }}" 
                       href="?tab=sex" role="tab">
                        <i class="bx bx-user me-3 font-size-18"></i>
                        <span>Sex / Gender</span>
                    </a>
                    
                    <a class="nav-link mb-2 py-3 px-4 d-flex align-items-center {{ $tab == 'geo' ? 'active' : '' }}" 
                       href="?tab=geo" role="tab">
                        <i class="bx bx-map-pin me-3 font-size-18"></i>
                        <span>Address Hierarchy</span>
                    </a>

                    <a class="nav-link mb-2 py-3 px-4 d-flex align-items-center {{ $tab == 'education' ? 'active' : '' }}" 
                       href="?tab=education" role="tab">
                        <i class="bx bx-book-bookmark me-3 font-size-18"></i>
                        <span>Boards & Institutions</span>
                    </a>

                    <a class="nav-link mb-2 py-3 px-4 d-flex align-items-center {{ $tab == 'leave_reason' ? 'active' : '' }}" 
                       href="?tab=leave_reason" role="tab">
                        <i class="bx bx-message-alt-detail me-3 font-size-18"></i>
                        <span>Leave Reasons</span>
                    </a>

                    <a class="nav-link mb-2 py-3 px-4 d-flex align-items-center {{ $tab == 'leave_balance' ? 'active' : '' }}" 
                       href="?tab=leave_balance" role="tab">
                        <i class="bx bx-calendar-exclamation me-3 font-size-18"></i>
                        <span>Leave Balances</span>
                    </a>

                    <a class="nav-link mb-0 py-3 px-4 d-flex align-items-center {{ $tab == 'salary' ? 'active' : '' }}" 
                       href="?tab=salary" role="tab">
                        <i class="bx bx-money me-3 font-size-18"></i>
                        <span>Salary Relation formula</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="tab-content" id="v-pills-tabContent">
            <!-- 1. Sex / Gender Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'sex' ? 'block' : 'none' }}">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Add New Gender option</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.store', 'sex') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="gender_name" class="form-label fw-medium">Gender Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="gender_name" name="name" required placeholder="e.g. Male, Female, Other">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Save Gender</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Gender List</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Gender</th>
                                                <th class="text-end px-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($genders as $gender)
                                                <tr>
                                                    <td><code>#{{ $gender->id }}</code></td>
                                                    <td><strong>{{ $gender->name }}</strong></td>
                                                    <td class="text-end px-4">
                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'sex', 'id' => $gender->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center py-4 text-muted">No genders defined.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Geographic Hierarchy Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'geo' ? 'block' : 'none' }}">
                <!-- Expandable Address Hierarchy Tree -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-map text-primary me-2 font-size-18"></i> Bangladesh Geographic Address Hierarchy</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-2">1. Add Division</h6>
                                    <form action="{{ route('subscriber.hris.master.store', 'division') }}" method="POST">
                                        @csrf
                                        <input type="text" class="form-control mb-2" name="name" required placeholder="Division Name">
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Save Division</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-2">2. Add District</h6>
                                    <form action="{{ route('subscriber.hris.master.store', 'district') }}" method="POST">
                                        @csrf
                                        <select class="form-select mb-2" name="division_id" required>
                                            <option value="">Select Division</option>
                                            @foreach($allDivisions as $div)
                                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mb-2" name="name" required placeholder="District Name">
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Save District</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-2">3. Add Thana / Upazila</h6>
                                    <form action="{{ route('subscriber.hris.master.store', 'thana') }}" method="POST">
                                        @csrf
                                        <select class="form-select mb-2" name="district_id" required>
                                            <option value="">Select District</option>
                                            @foreach($allDistricts as $dist)
                                                <option value="{{ $dist->id }}">{{ $dist->name }} ({{ $dist->division->name }})</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mb-2" name="name" required placeholder="Thana Name">
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Save Thana</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Address Explorer Hierarchy (Division &rarr; District &rarr; Thana)</h6>
                        <div class="accordion" id="divisionsAccordion">
                            @foreach($divisions as $div)
                                <div class="accordion-item border-0 shadow-sm mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingDiv{{ $div->id }}">
                                        <button class="accordion-button collapsed fw-bold py-3 bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv{{ $div->id }}">
                                            <i class="bx bx-folder me-2 text-warning"></i> {{ $div->name }} Division ({{ $div->districts->count() }} Districts)
                                        </button>
                                    </h2>
                                    <div id="collapseDiv{{ $div->id }}" class="accordion-collapse collapse" data-bs-parent="#divisionsAccordion">
                                        <div class="accordion-body p-4 bg-white">
                                            <div class="row g-3">
                                                @forelse($div->districts as $dist)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="p-3 border rounded">
                                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                <strong class="text-indigo-600"><i class="bx bx-map-pin me-1"></i> {{ $dist->name }}</strong>
                                                                <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'district', 'id' => $dist->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this district and all associated thanas?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0"><i class="bx bx-trash font-size-14"></i></button>
                                                                </form>
                                                            </div>
                                                            <ul class="list-unstyled ps-2 mb-0 font-size-13 text-secondary">
                                                                @forelse($dist->thanas as $thana)
                                                                    <li class="d-flex justify-content-between align-items-center py-1">
                                                                        <span>&bull; {{ $thana->name }}</span>
                                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'thana', 'id' => $thana->id]) }}" method="POST" class="d-inline">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="btn btn-link text-danger p-0 border-0"><i class="bx bx-x font-size-14"></i></button>
                                                                        </form>
                                                                    </li>
                                                                @empty
                                                                    <li class="text-muted">No thanas registered.</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-muted">No districts in this division.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Education Boards & Institutions Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'education' ? 'block' : 'none' }}">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">Education Boards</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.store', 'board') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" required placeholder="Add New Board (e.g. Dhaka Board)">
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </div>
                                </form>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Board Name</th>
                                                <th class="text-end px-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($boards as $board)
                                                <tr>
                                                    <td><strong>{{ $board->name }}</strong></td>
                                                    <td class="text-end px-3">
                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'board', 'id' => $board->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-center text-muted">No education boards found.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">Institutions Name Entry</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.store', 'institution') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" required placeholder="Add Institution Name (e.g. Dhaka University)">
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </div>
                                </form>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Institution Name</th>
                                                <th class="text-end px-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($institutions as $inst)
                                                <tr>
                                                    <td><strong>{{ $inst->name }}</strong></td>
                                                    <td class="text-end px-3">
                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'institution', 'id' => $inst->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-center text-muted">No institutions registered.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Leave Reasons Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'leave_reason' ? 'block' : 'none' }}">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Add Leave Reason</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.store', 'leave_reason') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="leave_reason" class="form-label fw-medium">Reason <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="leave_reason" name="reason" required placeholder="e.g. Personal Health Checkup">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Save Reason</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Leave Reasons</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Reason</th>
                                                <th class="text-end px-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leaveReasons as $reason)
                                                <tr>
                                                    <td><strong>{{ $reason->reason }}</strong></td>
                                                    <td class="text-end px-4">
                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'leave_reason', 'id' => $reason->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-center py-4 text-muted">No custom leave reasons added yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Leave Balances Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'leave_balance' ? 'block' : 'none' }}">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Set Employee Leave Balance</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.leave-balance') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="emp_id" class="form-label fw-medium">Select Employee <span class="text-danger">*</span></label>
                                        <select class="form-select" id="emp_id" name="employee_profile_id" required>
                                            <option value="">Choose Employee</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->user->name ?? 'N/A' }} ({{ $emp->employee_id }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="leave_type_id" class="form-label fw-medium">Leave Type <span class="text-danger">*</span></label>
                                        <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                            <option value="">Choose Leave Type</option>
                                            @foreach($leaveTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }} (CL/SL/EL)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="calendar_year" class="form-label fw-medium">Year <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="calendar_year" name="calendar_year" value="{{ date('Y') }}" required>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="allocated_days" class="form-label fw-medium">Allocated Days <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="allocated_days" name="allocated_days" value="15" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="earned_days" class="form-label fw-medium">Earned Days (Bonus/Carried)</label>
                                        <input type="number" class="form-control" id="earned_days" name="earned_days" value="0">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Configure Balance</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Leave Balance Registry</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employee</th>
                                                <th>Leave Type</th>
                                                <th>Year</th>
                                                <th>Allocated</th>
                                                <th>Spent</th>
                                                <th>Earned</th>
                                                <th>Total Available</th>
                                                <th class="text-end px-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leaveBalances as $balance)
                                                <tr>
                                                    <td><strong>{{ $balance->employee->user->name ?? 'N/A' }}</strong> <br><small class="text-muted">ID: {{ $balance->employee->employee_id }}</small></td>
                                                    <td><span class="badge bg-soft-primary text-primary">{{ $balance->leaveType->name ?? 'N/A' }}</span></td>
                                                    <td><code>{{ $balance->calendar_year }}</code></td>
                                                    <td>{{ $balance->allocated_days }} days</td>
                                                    <td>{{ $balance->spent_days }} days</td>
                                                    <td>{{ $balance->earned_days }} days</td>
                                                    <td><strong class="text-success">{{ ($balance->allocated_days + $balance->earned_days) - $balance->spent_days }} days</strong></td>
                                                    <td class="text-end px-4">
                                                        <form action="{{ route('subscriber.hris.master.destroy', ['type' => 'leave_balance', 'id' => $balance->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="text-center py-4 text-muted">No employee leave balances defined.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Salary Relation formula Tab -->
            <div class="tab-pane fade show active" style="display: {{ $tab == 'salary' ? 'block' : 'none' }}">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Define HR Option Salary Relation Formula</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('subscriber.hris.master.salary-relation') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="relation_name" class="form-label fw-medium">Formula Title / Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="relation_name" name="name" required placeholder="e.g. Standard 50-25-10-15 Split" value="{{ $activeSalaryRelation->name ?? '' }}">
                                    </div>
                                    
                                    <div class="alert alert-info border-0 shadow-sm font-size-13 py-2.5 mb-4">
                                        <i class="bx bx-info-circle me-1"></i> Configure percentage relations based on <strong>Gross Salary</strong>. The sum of basic, house rent, medical, and tada (travel/daily allowances) must total exactly <strong>100%</strong>.
                                    </div>

                                    <div class="mb-3">
                                        <label for="basic_percent" class="form-label fw-medium">Basic Salary (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="basic_percent" name="basic_percent" value="{{ old('basic_percent', $activeSalaryRelation->basic_percent ?? 50.00) }}" step="0.01" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="house_rent_percent" class="form-label fw-medium">House Rent (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="house_rent_percent" name="house_rent_percent" value="{{ old('house_rent_percent', $activeSalaryRelation->house_rent_percent ?? 25.00) }}" step="0.01" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="medical_percent" class="form-label fw-medium">Medical Allowance (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="medical_percent" name="medical_percent" value="{{ old('medical_percent', $activeSalaryRelation->medical_percent ?? 10.00) }}" step="0.01" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tada_percent" class="form-label fw-medium">Conveyance / TA-DA (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="tada_percent" name="tada_percent" value="{{ old('tada_percent', $activeSalaryRelation->tada_percent ?? 15.00) }}" step="0.01" required>
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Activate Formula</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">Salary Relation Registry</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Formula Name</th>
                                                <th>Split (B - HR - M - TA/DA)</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salaryRelations as $relation)
                                                <tr>
                                                    <td><strong>{{ $relation->name }}</strong></td>
                                                    <td>
                                                        <span class="text-dark fw-medium">
                                                            {{ (int)$relation->basic_percent }}% &middot; 
                                                            {{ (int)$relation->house_rent_percent }}% &middot; 
                                                            {{ (int)$relation->medical_percent }}% &middot; 
                                                            {{ (int)$relation->tada_percent }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($relation->is_active)
                                                            <span class="badge bg-soft-success text-success px-2 py-1"><i class="bx bx-check-circle me-1"></i> Active Structure</span>
                                                        @else
                                                            <span class="badge bg-soft-secondary text-secondary px-2 py-1">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center py-4 text-muted">No salary relations defined.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
