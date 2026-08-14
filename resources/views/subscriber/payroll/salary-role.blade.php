@extends('layouts.subscriber')

@section('title', 'Salary Role - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
</style>

<div class="page-title-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Setup</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Salary Role Configuration</h4>
    </div>
    <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="bx bx-plus me-1"></i> New Salary Role
    </button>
</div>

@if($errors->has('total'))
    <div class="alert alert-danger border-0 shadow-sm" style="background: #fff1f2; border-left: 4px solid #f43f5e; border-radius: 8px;">
        <i class="bx bx-error-circle me-2 align-middle"></i> {{ $errors->first('total') }}
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Total Roles</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $relations->count() }}</h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600">
                    <i class="bx bx-category"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Active Role</span>
                    <h3 class="mt-2 mb-0 fw-bold" style="font-size: 1rem;">{{ $activeRelation ? $activeRelation->name : 'None' }}</h3>
                </div>
                <div class="stat-icon bg-green-50 border border-green-100 text-green-600">
                    <i class="bx bx-check-shield"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Months with Assignments</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $months->count() }}</h3>
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600">
                    <i class="bx bx-calendar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">All Salary Roles</h5>
                @if($relations->count())
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="text-muted font-size-12 text-uppercase">
                                <tr>
                                    <th>Name</th>
                                    <th>Basic</th>
                                    <th>Rent</th>
                                    <th>Med</th>
                                    <th>TA/DA</th>
                                    <th>Policies</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relations as $relation)
                                    <tr>
                                        <td class="fw-semibold">{{ $relation->name }}</td>
                                        <td>{{ $relation->basic_percent }}%</td>
                                        <td>{{ $relation->house_rent_percent }}%</td>
                                        <td>{{ $relation->medical_percent }}%</td>
                                        <td>{{ $relation->tada_percent }}%</td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap font-size-10">
                                                @if($relation->is_ot_payable)
                                                    <span class="badge bg-info rounded-pill">OT</span>
                                                @endif
                                                @if($relation->is_late_deduction)
                                                    <span class="badge bg-warning text-dark rounded-pill">LateDeduct</span>
                                                @endif
                                                @if($relation->single_punch_full_day)
                                                    <span class="badge bg-primary rounded-pill">SingleFull</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($relation->is_active)
                                                <span class="badge bg-success font-size-11 px-3 py-1.5 rounded-pill">Active</span>
                                            @else
                                                <span class="badge bg-light text-muted font-size-11 px-3 py-1.5 rounded-pill">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $relation->id }}" title="Edit">
                                                    <i class="bx bx-pencil"></i>
                                                </button>
                                                @if(!$relation->is_active)
                                                    <form action="{{ route('subscriber.payroll.salary-role.activate', $relation->id) }}" method="POST">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-success rounded-pill px-2" type="submit" title="Activate">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('subscriber.payroll.salary-role.delete', $relation->id) }}" method="POST" onsubmit="return confirm('Delete this salary role?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-2" type="submit" title="Delete">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted font-size-12">—</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-category text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No salary roles yet. Create your first one.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bx bx-link text-primary me-1"></i> Assign Role to Department</h5>
                <p class="font-size-12 text-muted mb-3">Assign a role to one or more departments for a specific month. Leave departments empty to apply to all.</p>
                <form method="POST" action="{{ route('subscriber.payroll.salary-role.assignments.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Salary Role</label>
                        <select name="salary_role_id" class="form-select" required>
                            <option value="">— Select Role —</option>
                            @foreach($relations as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Applicable Month</label>
                        <input type="month" name="applicable_month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Departments <span class="text-muted font-size-11">(leave empty for all)</span></label>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px;">
                            @forelse($departments as $dept)
                                <div class="form-check py-1">
                                    <input class="form-check-input" type="checkbox" name="department_ids[]" value="{{ $dept->id }}" id="dept{{ $dept->id }}">
                                    <label class="form-check-label font-size-13" for="dept{{ $dept->id }}">{{ $dept->name }}</label>
                                </div>
                            @empty
                                <p class="text-muted font-size-12 mb-0">No departments found. Create departments first.</p>
                            @endforelse
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="bx bx-plus-circle me-2"></i> Assign Role
                    </button>
                </form>

                <hr class="my-4">

                <h6 class="fw-bold mb-3"><i class="bx bx-list-ul text-primary me-1"></i> Current Assignments</h6>
                @php $hasAssignments = $relations->some(fn($r) => $r->assignments->count()); @endphp
                @if($hasAssignments)
                    <div style="max-height: 350px; overflow-y: auto;">
                        @foreach($relations as $relation)
                            @foreach($relation->assignments as $assignment)
                                <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div>
                                        <span class="fw-semibold font-size-12">{{ $relation->name }}</span>
                                        <span class="badge bg-light text-muted font-size-10 ms-1">{{ $assignment->applicable_month }}</span>
                                        @if($assignment->department)
                                            <span class="badge bg-info font-size-10 ms-1">{{ $assignment->department->name }}</span>
                                        @else
                                            <span class="badge bg-secondary font-size-10 ms-1">All Departments</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('subscriber.payroll.salary-role.assignments.delete', $assignment->id) }}" method="POST" onsubmit="return confirm('Remove this assignment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" type="submit"><i class="bx bx-x"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @else
                    <p class="text-muted font-size-13 text-center py-3">No assignments yet. Assign a role above.</p>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bx bx-gift text-primary me-1"></i> Bonus Configuration</h5>
                <p class="font-size-12 text-muted mb-3">Set bonus calculation per role. Define eligibility slabs based on tenure (months from joining).</p>
                <form method="POST" action="{{ route('subscriber.payroll.salary-role.bonus-config.store') }}" id="bonusForm" onsubmit="return validateBonusForm()">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Salary Role</label>
                        <select name="salary_role_id" class="form-select" required>
                            <option value="">— Select Role —</option>
                            @foreach($relations as $r)
                                <option value="{{ $r->id }}" {{ $r->bonusConfig ? 'data-has-bonus=1' : '' }}>{{ $r->name }} {!! $r->bonusConfig ? '✓' : '' !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold font-size-13">Calculation Type</label>
                            <select name="calculation_type" class="form-select" required onchange="toggleCalcValue()">
                                <option value="basic_half">Basic × 0.5 (Half Basic)</option>
                                <option value="basic_percent">Basic %</option>
                                <option value="gross_1_5x">Gross × 1.5</option>
                                <option value="gross_percent">Gross %</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-5" id="calcValueGroup">
                            <label class="form-label fw-semibold font-size-13">Value <span class="text-muted font-size-10" id="calcValueHint">(%)</span></label>
                            <input type="number" name="calculation_value" class="form-control" value="0" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Eligibility Slabs <span class="text-muted font-size-11">(tenure in months)</span></label>
                        <div id="slabContainer">
                            <div class="slab-row d-flex gap-2 mb-2 align-items-center">
                                <input type="number" name="slabs[0][min_months]" class="form-control form-control-sm" style="width:70px;" placeholder="Min" value="0" required>
                                <span class="text-muted font-size-12">to</span>
                                <input type="number" name="slabs[0][max_months]" class="form-control form-control-sm" style="width:70px;" placeholder="Max">
                                <span class="text-muted font-size-12">mo →</span>
                                <input type="number" name="slabs[0][percent_of_bonus]" class="form-control form-control-sm" style="width:80px;" placeholder="%" value="0" required>
                                <span class="text-muted font-size-12">%</span>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="addSlab()"><i class="bx bx-plus"></i></button>
                            </div>
                        </div>
                        <small class="text-muted font-size-11">Example: 0-3mo=30%, 3-6mo=50%, 6-9mo=80%, 9+mo=100%. Leave Max empty for unlimited.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="bx bx-save me-2"></i> Save Bonus Config
                    </button>
                </form>

                <hr class="my-4">

                <h6 class="fw-bold mb-3"><i class="bx bx-gift text-primary me-1"></i> Current Bonus Configs</h6>
                @php $hasBonus = $relations->some(fn($r) => $r->bonusConfig); @endphp
                @if($hasBonus)
                    @foreach($relations as $relation)
                        @if($relation->bonusConfig)
                            @php $bc = $relation->bonusConfig; @endphp
                            <div class="p-3 mb-2 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="fw-semibold font-size-13">{{ $relation->name }}</span>
                                        <span class="badge bg-light text-muted font-size-10 ms-1">
                                            {{ str_replace(['_', 'basic_half', 'gross_1_5x', 'basic_percent', 'gross_percent', 'fixed_amount'], [' ', 'Basic×0.5', 'Gross×1.5', 'Basic%', 'Gross%', 'Fixed'], $bc->calculation_type) }}
                                        </span>
                                    </div>
                                    <form action="{{ route('subscriber.payroll.salary-role.bonus-config.delete', $bc->id) }}" method="POST" onsubmit="return confirm('Remove bonus config for {{ $relation->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0"><i class="bx bx-trash"></i></button>
                                    </form>
                                </div>
                                @if($bc->slabs->count())
                                    <div class="mt-2 font-size-11 text-muted d-flex gap-2 flex-wrap">
                                        @foreach($bc->slabs as $slab)
                                            <span class="badge bg-info rounded-pill">
                                                {{ $slab->min_months }}{{ $slab->max_months ? '-'.$slab->max_months : '+' }}mo → {{ $slab->percent_of_bonus }}%
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-muted font-size-13 text-center py-3">No bonus configs yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let slabIndex = 1;
    function addSlab() {
        const container = document.getElementById('slabContainer');
        const row = document.createElement('div');
        row.className = 'slab-row d-flex gap-2 mb-2 align-items-center';
        row.innerHTML = `
            <input type="number" name="slabs[${slabIndex}][min_months]" class="form-control form-control-sm" style="width:70px;" placeholder="Min" value="0" required>
            <span class="text-muted font-size-12">to</span>
            <input type="number" name="slabs[${slabIndex}][max_months]" class="form-control form-control-sm" style="width:70px;" placeholder="Max">
            <span class="text-muted font-size-12">mo →</span>
            <input type="number" name="slabs[${slabIndex}][percent_of_bonus]" class="form-control form-control-sm" style="width:80px;" placeholder="%" value="0" required>
            <span class="text-muted font-size-12">%</span>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
        `;
        container.appendChild(row);
        slabIndex++;
    }

    function toggleCalcValue() {
        const type = document.querySelector('[name="calculation_type"]').value;
        const hint = document.getElementById('calcValueHint');
        if (type === 'basic_half' || type === 'gross_1_5x') {
            document.querySelector('[name="calculation_value"]').value = 0;
            document.querySelector('[name="calculation_value"]').disabled = true;
            hint.textContent = '(not used)';
        } else {
            document.querySelector('[name="calculation_value"]').disabled = false;
            hint.textContent = type === 'fixed_amount' ? '(amount)' : '(%)';
        }
    }

    function validateBonusForm() {
        const slabs = document.querySelectorAll('.slab-row');
        if (slabs.length === 0) {
            alert('Add at least one eligibility slab.');
            return false;
        }
        return true;
    }

    toggleCalcValue();
</script>
@endpush

{{-- Create Modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form method="POST" action="{{ route('subscriber.payroll.salary-role.store') }}">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold">Create Salary Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Standard, Executive" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold font-size-13">Basic (%)</label>
                            <input type="number" name="basic_percent" class="form-control percent-input" value="50" step="0.01" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold font-size-13">House Rent (%)</label>
                            <input type="number" name="house_rent_percent" class="form-control percent-input" value="25" step="0.01" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold font-size-13">Medical (%)</label>
                            <input type="number" name="medical_percent" class="form-control percent-input" value="10" step="0.01" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold font-size-13">TA/DA (%)</label>
                            <input type="number" name="tada_percent" class="form-control percent-input" value="15" step="0.01" required>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between font-size-13">
                            <span class="text-muted">Total must equal <strong>100%</strong></span>
                            <span id="createTotalPreview" class="fw-bold text-success">100%</span>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <h6 class="fw-bold font-size-13 mb-3">Salary Policies</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_ot_payable" value="1" id="createOtPayable" checked>
                                    <label class="form-check-label font-size-13" for="createOtPayable">OT Payable</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_late_deduction" value="1" id="createLateDeduct">
                                    <label class="form-check-label font-size-13" for="createLateDeduct">Late Deduction</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="single_punch_full_day" value="1" id="createSinglePunch">
                                    <label class="form-check-label font-size-13" for="createSinglePunch">Single Punch = Full Day</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modals --}}
@foreach($relations as $relation)
    <div class="modal fade" id="editRoleModal{{ $relation->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <form method="POST" action="{{ route('subscriber.payroll.salary-role.update', $relation->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="fw-bold">Edit Salary Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold font-size-13">Role Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $relation->name }}" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold font-size-13">Basic (%)</label>
                                <input type="number" name="basic_percent" class="form-control percent-input-edit" value="{{ $relation->basic_percent }}" step="0.01" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold font-size-13">House Rent (%)</label>
                                <input type="number" name="house_rent_percent" class="form-control percent-input-edit" value="{{ $relation->house_rent_percent }}" step="0.01" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold font-size-13">Medical (%)</label>
                                <input type="number" name="medical_percent" class="form-control percent-input-edit" value="{{ $relation->medical_percent }}" step="0.01" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold font-size-13">TA/DA (%)</label>
                                <input type="number" name="tada_percent" class="form-control percent-input-edit" value="{{ $relation->tada_percent }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;">
                            <div class="d-flex justify-content-between font-size-13">
                                <span class="text-muted">Total</span>
                                <span class="edit-total-preview fw-bold text-success">{{ $relation->basic_percent + $relation->house_rent_percent + $relation->medical_percent + $relation->tada_percent }}%</span>
                            </div>
                        </div>
                        <div class="border-top pt-3">
                            <h6 class="fw-bold font-size-13 mb-3">Salary Policies</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_ot_payable" value="1" id="editOtPayable{{ $relation->id }}" {{ $relation->is_ot_payable ? 'checked' : '' }}>
                                        <label class="form-check-label font-size-13" for="editOtPayable{{ $relation->id }}">OT Payable</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_late_deduction" value="1" id="editLateDeduct{{ $relation->id }}" {{ $relation->is_late_deduction ? 'checked' : '' }}>
                                        <label class="form-check-label font-size-13" for="editLateDeduct{{ $relation->id }}">Late Deduction</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="single_punch_full_day" value="1" id="editSinglePunch{{ $relation->id }}" {{ $relation->single_punch_full_day ? 'checked' : '' }}>
                                        <label class="form-check-label font-size-13" for="editSinglePunch{{ $relation->id }}">Single Punch = Full Day</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    document.querySelectorAll('.percent-input').forEach(el => {
        el.addEventListener('input', function() {
            const inputs = document.querySelectorAll('#createRoleModal .percent-input');
            let total = 0;
            inputs.forEach(i => total += parseFloat(i.value) || 0);
            const preview = document.getElementById('createTotalPreview');
            preview.textContent = total + '%';
            preview.style.color = total === 100 ? '#10b981' : '#ef4444';
        });
    });

    document.querySelectorAll('.percent-input-edit').forEach(el => {
        el.addEventListener('input', function() {
            const modal = this.closest('.modal');
            const inputs = modal.querySelectorAll('.percent-input-edit');
            let total = 0;
            inputs.forEach(i => total += parseFloat(i.value) || 0);
            const preview = modal.querySelector('.edit-total-preview');
            preview.textContent = total + '%';
            preview.style.color = total === 100 ? '#10b981' : '#ef4444';
        });
    });
</script>
@endpush
@endsection