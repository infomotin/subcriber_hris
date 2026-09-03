@extends('layouts.subscriber')

@section('title', 'Employee Profiles')

@section('content')
<style>
    .filter-card {
        background: linear-gradient(135deg, rgba(95, 90, 246, 0.03), rgba(139, 92, 246, 0.03));
        border: 1px solid rgba(95, 90, 246, 0.08) !important;
        border-radius: 16px;
    }
    .search-input-group {
        position: relative;
    }
    .search-input-group .bx-search {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.1rem;
        color: #94a3b8;
        z-index: 5;
    }
    .search-input-group .form-control {
        padding-left: 38px;
        border-radius: 40px !important;
        height: 42px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
    }
    .search-input-group .form-control:focus {
        background: #fff;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(95, 90, 246, 0.08);
    }
    .filter-select {
        border-radius: 40px !important;
        height: 40px;
        font-size: 0.8rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding-left: 14px;
    }
    .filter-select:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(95, 90, 246, 0.08);
    }
    .badge-soft {
        font-weight: 600;
        font-size: 0.7rem;
        padding: 0.25rem 0.7rem;
        border-radius: 40px;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 50% !important;
        min-height: auto !important;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .action-btn:hover {
        border-color: #e2e8f0;
        background: #f1f5f9 !important;
    }
    .table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        font-weight: 700;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #f1f5f9;
        background: #fafbfc;
    }
    .table td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .table tbody tr {
        transition: background 0.15s ease;
    }
    .table tbody tr:hover {
        background: rgba(95, 90, 246, 0.02);
    }
    .employee-avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .pagination-container nav .pagination {
        margin-bottom: 0;
    }
    .more-dropdown-toggle {
        width: 30px;
        height: 30px;
        border-radius: 50% !important;
        min-height: auto !important;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        transition: background 0.15s ease;
    }
    .more-dropdown-toggle:hover {
        background: #f1f5f9;
    }
    .more-dropdown-toggle::after {
        display: none !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Human Resources</span>
                <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
                    <i class="bx bx-group text-primary me-1.5 align-middle font-size-26"></i>Employee Directory
                </h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted font-size-12">{{ $employees->total() }} total</span>
                <a href="{{ route('subscriber.hris.employees.import') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm" style="height: 40px; font-size: 0.78rem;">
                    <i class="bx bx-import me-1 font-size-14 align-middle"></i> Import
                </a>
                <a href="{{ route('subscriber.hris.employees.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm" style="height: 40px;">
                    <i class="bx bx-plus me-1 font-size-16 align-middle"></i> Add Employee
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 filter-card mb-4">
    <div class="card-body px-4 py-3">
        <form method="GET" action="{{ route('subscriber.hris.employees.index') }}" id="filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4">
                    <div class="search-input-group">
                        <i class="bx bx-search"></i>
                        <input type="text" class="form-control" name="search" placeholder="Search by name, email, ID or phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select class="form-select filter-select" name="department_id" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select class="form-select filter-select" name="designation_id" onchange="this.form.submit()">
                        <option value="">All Designations</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select class="form-select filter-select" name="shift_id" onchange="this.form.submit()">
                        <option value="">All Shifts</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <select class="form-select filter-select" name="status" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill font-size-13" style="height: 40px;">
                            <i class="bx bx-filter-alt me-1 align-middle"></i> Filter
                        </button>
                        @if(request('search') || request('department_id') || request('designation_id') || request('status') || request('shift_id'))
                            <a href="{{ route('subscriber.hris.employees.index') }}" class="btn btn-outline-secondary rounded-pill font-size-13 px-3" style="height: 40px;">
                                <i class="bx bx-x me-1 align-middle font-size-16"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Name / ID</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Shift</th>
                                <th>Contact</th>
                                <th>Verified</th>
                                <th>Status</th>
                                <th class="text-end pe-4" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $emp)
                                <tr>
                                    <td class="ps-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'U') }}&background=5f5af6&color=fff&size=40" class="employee-avatar-sm border border-slate-100" alt="">
                                    </td>
                                    <td>
                                        <strong class="text-slate-800 d-block" style="font-size: 0.9rem;">{{ $emp->user->name ?? 'N/A' }}</strong>
                                        <code class="font-size-11 text-muted">{{ $emp->employee_id }}</code>
                                    </td>
                                    <td>
                                        @if($emp->department)
                                            <span class="badge bg-soft-primary text-primary badge-soft">{{ $emp->department->name }}</span>
                                        @else
                                            <span class="text-muted font-size-12">—</span>
                                        @endif
                                    </td>
                                    <td class="text-slate-700 font-size-13">{{ $emp->designation->title ?? '—' }}</td>
                                    <td>
                                        @if($emp->shift)
                                            <span class="font-size-12 text-slate-600 fw-semibold">
                                                <i class="bx bx-time-five text-muted me-1 align-middle font-size-14"></i>
                                                {{ $emp->shift->name }}
                                                <small class="text-muted d-block font-size-11">{{ $emp->shift->start_time }} - {{ $emp->shift->end_time }}</small>
                                            </span>
                                        @else
                                            <span class="text-muted font-size-12">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-size-12 text-slate-700">{{ $emp->phone_number ?? '—' }}</span>
                                        <small class="text-muted d-block font-size-11">{{ $emp->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @php $vpct = $emp->verificationProgress(); @endphp
                                        <div class="d-flex align-items-center gap-1.5">
                                            <div class="progress-bar-soft" style="width: 50px;">
                                                <div class="bar {{ $vpct === 100 ? 'complete' : '' }}" style="width: {{ $vpct }}%;"></div>
                                            </div>
                                            <span class="font-size-11 fw-semibold {{ $vpct === 100 ? 'text-success' : ($vpct >= 50 ? 'text-warning' : 'text-danger') }}">{{ $vpct }}%</span>
                                        </div>
                                        @if($vpct === 100)
                                            <small class="text-success font-size-10 d-block"><i class="bx bx-badge-check align-middle"></i> Complete</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($emp->status === 'active')
                                            <span class="badge bg-soft-success text-success badge-soft px-3"><i class="bx bx-check-circle align-middle me-0.5 font-size-14"></i> Active</span>
                                        @elseif($emp->status === 'probation')
                                            <span class="badge bg-soft-warning text-warning badge-soft px-3"><i class="bx bx-time align-middle me-0.5 font-size-14"></i> Probation</span>
                                        @elseif($emp->status === 'resigned')
                                            <span class="badge bg-soft-secondary text-secondary badge-soft px-3">Resigned</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger badge-soft px-3">{{ ucfirst($emp->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#modalQuickView_{{ $emp->id }}" title="Quick View">
                                                <i class="bx bx-show-alt text-primary font-size-16"></i>
                                            </button>
                                            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#modalIdCard_{{ $emp->id }}" title="ID Card">
                                                <i class="bx bx-id-card text-indigo-500 font-size-16"></i>
                                            </button>
                                            <a href="{{ route('subscriber.hris.employees.edit', $emp) }}" class="action-btn" title="Edit">
                                                <i class="bx bx-pencil text-warning font-size-16"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="more-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="More">
                                                    <i class="bx bx-dots-vertical-rounded text-slate-500 font-size-18"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border border-slate-100 py-1" style="border-radius: 12px; min-width: 180px;">
                                                    <li><a class="dropdown-item py-2 font-size-13" href="{{ route('subscriber.hris.employees.show', $emp) }}"><i class="bx bx-user-circle text-primary me-2 font-size-16 align-middle"></i> Full Profile</a></li>
                                                    <li><a class="dropdown-item py-2 font-size-13" href="{{ route('subscriber.hris.employees.edit', $emp) }}"><i class="bx bx-edit-alt text-warning me-2 font-size-16 align-middle"></i> Edit Profile</a></li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form action="{{ route('subscriber.hris.employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Delete this employee profile and their login account?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-2 font-size-13 text-danger"><i class="bx bx-trash me-2 font-size-16 align-middle"></i> Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bx bx-user-x text-muted font-size-40 d-block mb-3"></i>
                                        <p class="text-muted mb-2">No employee profiles found.</p>
                                        <a href="{{ route('subscriber.hris.employees.create') }}" class="btn btn-primary rounded-pill px-4 font-size-13">
                                            <i class="bx bx-plus me-1"></i> Add Employee
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($employees->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing {{ $employees->firstItem() }}–{{ $employees->lastItem() }} of {{ $employees->total() }}</small>
                        <div class="pagination-container">{{ $employees->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('modals')
@foreach($employees as $emp)
    <div class="modal fade text-start" id="modalQuickView_{{ $emp->id }}" tabindex="-1" aria-hidden="true">
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
                            <span class="text-muted font-size-10 d-block uppercase tracking-wide fw-bold mb-0.5">Gender / Blood</span>
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

    <div class="modal fade text-start" id="modalIdCard_{{ $emp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-id-card text-indigo-500 me-2 align-middle font-size-20"></i>ID Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center bg-light">
                    <div id="printIdCard_{{ $emp->id }}" class="mx-auto shadow-sm border border-slate-200 bg-white p-4" style="width: 280px; height: 420px; border-radius: 12px; position: relative;">
                        <div class="d-flex align-items-center justify-content-center gap-1.5 mb-3 border-bottom pb-2">
                            <i class="bx bx-shield-quarter text-emerald-500 font-size-20"></i>
                            <span style="font-family: 'Poppins', sans-serif; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; color: #0f172a;">NEXOZAINT ADMS</span>
                        </div>
                        <div class="mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->user->name ?? 'N/A') }}&background=5f5af6&color=fff&size=100" class="rounded-circle border border-2 p-1 border-indigo-100" width="80" height="80">
                        </div>
                        <h6 class="fw-bold mb-1 text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $emp->user->name ?? 'N/A' }}</h6>
                        <span class="text-primary font-size-11 fw-bold d-block uppercase tracking-wider mb-2.5">{{ $emp->designation->title ?? 'Employee' }}</span>
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
@endforeach
@endsection

@push('scripts')
<script>
function printCard(divId) {
    var el = document.getElementById(divId);
    if (!el) return;
    var win = window.open('', '_blank', 'width=400,height=600');
    win.document.write('<html><head><title>ID Card</title>');
    win.document.write('<style>body{margin:0;display:flex;justify-content:center;align-items:center;min-height:100vh;font-family:sans-serif;}</style>');
    win.document.write('</head><body>');
    win.document.write(el.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); win.close(); }, 250);
}

document.querySelector('#filter-form input[name="search"]').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') this.form.submit();
});
</script>
@endpush