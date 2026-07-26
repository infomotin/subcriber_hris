@extends('layouts.subscriber')

@section('title', 'Employee Profiles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Human Resources</span>
                <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Employee Directory</h4>
            </div>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.employees.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Add Employee
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $emp)
                                <tr>
                                    <td><code>{{ $emp->employee_id }}</code></td>
                                    <td><strong>{{ $emp->user->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $emp->user->email ?? 'N/A' }}</td>
                                    <td>{{ $emp->department->name ?? 'N/A' }}</td>
                                    <td>{{ $emp->designation->title ?? 'N/A' }}</td>
                                    <td>
                                        @if($emp->status === 'active')
                                            <span class="badge bg-soft-success text-success px-2.5 py-1.5 rounded-pill">Active</span>
                                        @elseif($emp->status === 'probation')
                                            <span class="badge bg-soft-warning text-warning px-2.5 py-1.5 rounded-pill">Probation</span>
                                        @elseif($emp->status === 'resigned')
                                            <span class="badge bg-soft-secondary text-secondary px-2.5 py-1.5 rounded-pill">Resigned</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 rounded-pill">{{ ucfirst($emp->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Profile Quick View -->
                                            <button class="btn btn-sm btn-light border-0" data-bs-toggle="modal" data-bs-target="#modalQuickView_{{ $emp->id }}" title="Quick Profile View" style="height: 34px; width: 34px; border-radius: 50% !important; min-height: auto;">
                                                <i class="bx bx-show-alt text-primary font-size-16"></i>
                                            </button>

                                            <!-- Show ID Card Quick Link -->
                                            <button class="btn btn-sm btn-light border-0" data-bs-toggle="modal" data-bs-target="#modalIdCard_{{ $emp->id }}" title="Employee ID Card" style="height: 34px; width: 34px; border-radius: 50% !important; min-height: auto;">
                                                <i class="bx bx-id-card text-indigo-500 font-size-16"></i>
                                            </button>

                                            <!-- Edit Action -->
                                            <button class="btn btn-sm btn-light border-0" data-bs-toggle="modal" data-bs-target="#modalEdit_{{ $emp->id }}" title="Quick Edit Profile" style="height: 34px; width: 34px; border-radius: 50% !important; min-height: auto;">
                                                <i class="bx bx-pencil text-warning font-size-16"></i>
                                            </button>

                                            <!-- Full View Page -->
                                            <a href="{{ route('subscriber.hris.employees.show', $emp) }}" class="btn btn-sm btn-light border-0" title="Full Profile" style="height: 34px; width: 34px; border-radius: 50% !important; min-height: auto; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bx bx-user text-muted font-size-16"></i>
                                            </a>

                                            <!-- Delete profile -->
                                            <form action="{{ route('subscriber.hris.employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee profile? This will also delete their login account.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border-0 text-danger" title="Delete Profile" style="height: 34px; width: 34px; border-radius: 50% !important; min-height: auto;">
                                                    <i class="bx bx-trash font-size-16"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Modal: Quick View -->
                                        <div class="modal fade text-start" id="modalQuickView_{{ $emp->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                                    <div class="modal-header bg-white border-bottom py-3">
                                                        <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-user-pin text-primary me-2 align-middle"></i>Profile At A Glance</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="d-flex align-items-center gap-3 mb-4">
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'N/A') }}&background=5f5af6&color=fff" class="rounded-circle border" width="60" height="60">
                                                            <div>
                                                                <h5 class="fw-bold mb-1 text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $emp->user->name ?? 'N/A' }}</h5>
                                                                <span class="badge bg-soft-primary text-primary rounded-pill font-size-10">Card ID: {{ $emp->employee_id }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Department</span>
                                                                <strong class="text-slate-800 font-size-13">{{ $emp->department->name ?? 'N/A' }}</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Designation</span>
                                                                <strong class="text-slate-800 font-size-13">{{ $emp->designation->title ?? 'N/A' }}</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Email Address</span>
                                                                <span class="text-slate-800 font-size-13">{{ $emp->user->email ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Phone Number</span>
                                                                <span class="text-slate-800 font-size-13">{{ $emp->phone_number ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Joining Date</span>
                                                                <span class="text-slate-800 font-size-13">{{ $emp->joining_date }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Gender / Blood Group</span>
                                                                <span class="text-slate-800 font-size-13">{{ $emp->gender }} ({{ $emp->blood_group ?? 'N/A' }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                                                        <a href="{{ route('subscriber.hris.employees.show', $emp) }}" class="btn btn-primary px-4 font-size-13 fw-bold rounded-pill"><i class="bx bx-link-external me-1"></i> Full Profile</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal: ID Card -->
                                        <div class="modal fade text-start" id="modalIdCard_{{ $emp->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                                    <div class="modal-header bg-white border-bottom py-3">
                                                        <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-id-card text-indigo-500 me-2 align-middle font-size-20"></i>Employee ID Card</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-center bg-light">
                                                        <!-- Printable ID Card Holder -->
                                                        <div id="printIdCard_{{ $emp->id }}" class="mx-auto shadow-sm border border-slate-200 bg-white p-4" style="width: 280px; height: 420px; border-radius: 12px; position: relative;">
                                                            <!-- Card Header -->
                                                            <div class="d-flex align-items-center justify-content-center gap-1.5 mb-3 border-bottom pb-2">
                                                                <i class="bx bx-shield-quarter text-emerald-500 font-size-20"></i>
                                                                <span style="font-family: 'Poppins', sans-serif; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; color: #0f172a;">NEXOZAINT ADMS</span>
                                                            </div>
                                                            
                                                            <!-- Photo -->
                                                            <div class="mb-2">
                                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'N/A') }}&background=5f5af6&color=fff&size=100" class="rounded-circle border border-2 p-1 border-indigo-100" width="80" height="80">
                                                            </div>
                                                            
                                                            <!-- Name & Desig -->
                                                            <h6 class="fw-bold mb-1 text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $emp->user->name ?? 'N/A' }}</h6>
                                                            <span class="text-primary font-size-11 fw-bold d-block uppercase tracking-wider mb-2.5">{{ $emp->designation->title ?? 'Employee' }}</span>
                                                            
                                                            <!-- Info Grid -->
                                                            <div class="bg-light p-2.5 rounded-3 border text-start font-size-11 mb-3">
                                                                <div class="d-flex justify-content-between mb-1.5">
                                                                    <span class="text-muted">Card ID:</span>
                                                                    <strong class="text-slate-800">{{ $emp->employee_id }}</strong>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1.5">
                                                                    <span class="text-muted">Department:</span>
                                                                    <strong class="text-slate-800">{{ $emp->department->name ?? 'N/A' }}</strong>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted">Phone:</span>
                                                                    <strong class="text-slate-800">{{ $emp->phone_number }}</strong>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Barcode Simulation -->
                                                            <div class="mt-4 pt-1">
                                                                <div class="d-inline-block text-center bg-light p-1 px-3 border border-slate-100" style="border-radius: 4px;">
                                                                    <div style="letter-spacing: 2px; font-family: monospace; font-size: 0.95rem; font-weight: bold; color: #334155;">||| | |||| || |||</div>
                                                                    <div class="font-size-8 text-muted uppercase mt-0.5" style="letter-spacing: 0.5px;">ADMS-{{ $emp->employee_id }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-3 bg-white" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                                                        <button type="button" class="btn btn-outline-primary px-4 w-100 font-size-13 fw-bold rounded-pill" onclick="printCard('printIdCard_{{ $emp->id }}')"><i class="bx bx-printer me-1"></i> Print ID Card</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal: Quick Edit -->
                                        <div class="modal fade text-start" id="modalEdit_{{ $emp->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('subscriber.hris.employees.update', $emp) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-white border-bottom py-3">
                                                        <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-pencil text-warning me-2 align-middle"></i>Quick Edit Employee</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Full Name</label>
                                                            <input type="text" class="form-control bg-light" value="{{ $emp->user->name ?? 'N/A' }}" readonly disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="phone_number_{{ $emp->id }}" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="phone_number_{{ $emp->id }}" name="phone_number" value="{{ $emp->phone_number }}" required>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="department_id_{{ $emp->id }}" class="form-label">Department <span class="text-danger">*</span></label>
                                                                    <select class="form-select" id="department_id_{{ $emp->id }}" name="department_id" required style="border: 2px solid #e2e8f0 !important; border-radius: 10px;">
                                                                        @foreach($departments as $dept)
                                                                            <option value="{{ $dept->id }}" {{ $emp->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="designation_id_{{ $emp->id }}" class="form-label">Designation <span class="text-danger">*</span></label>
                                                                    <select class="form-select" id="designation_id_{{ $emp->id }}" name="designation_id" required style="border: 2px solid #e2e8f0 !important; border-radius: 10px;">
                                                                        @foreach($designations as $desig)
                                                                            <option value="{{ $desig->id }}" {{ $emp->designation_id == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="status_{{ $emp->id }}" class="form-label">Employment Status <span class="text-danger">*</span></label>
                                                            <select class="form-select" id="status_{{ $emp->id }}" name="status" required style="border: 2px solid #e2e8f0 !important; border-radius: 10px;">
                                                                <option value="active" {{ $emp->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="probation" {{ $emp->status === 'probation' ? 'selected' : '' }}>Probation</option>
                                                                <option value="terminated" {{ $emp->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                                                <option value="resigned" {{ $emp->status === 'resigned' ? 'selected' : '' }}>Resigned</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check-circle me-1"></i> Update Profile</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No employee profiles found. Click "Add Employee" to register one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($employees->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printCard(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #ffffff;">
                ${printContents}
            </div>
        `;

        window.print();

        document.body.innerHTML = originalContents;
        window.location.reload(); // Reload to restore modal states and event listeners
    }
</script>
@endpush
