@extends('layouts.subscriber')

@section('title', 'Departments')

@section('content')
<div class="page-title-box mb-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Directory</span>
        <h4 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-git-branch text-primary me-2 align-middle"></i>Department Setup
        </h4>
    </div>
    <div class="page-title-right">
        <a href="{{ route('subscriber.hris.departments.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bx bx-plus me-1"></i> Add Department
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-size-12">
                    <i class="bx bx-layer me-1"></i> {{ $totalDepartments }} Total
                </span>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill font-size-12">
                    <i class="bx bx-group me-1"></i> {{ \App\Models\EmployeeProfile::count() }} Employees
                </span>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 font-size-13" placeholder="Search departments..." value="{{ request('search') }}">
                </div>
                @if(request('search'))
                    <a href="{{ route('subscriber.hris.departments.index') }}" class="btn btn-sm btn-light border text-muted px-2">
                        <i class="bx bx-x"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="font-size-12 text-uppercase text-muted fw-semibold letter-spacing-05">
                    <th class="ps-3 py-2" style="width: 40px;">#</th>
                    <th class="py-2">Department Name</th>
                    <th class="py-2">Code</th>
                    <th class="py-2">Parent</th>
                    <th class="py-2 text-center">Employees</th>
                    <th class="py-2 text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                    <tr class="border-start-0">
                        <td class="ps-3 text-muted font-size-12">{{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center font-size-13 fw-bold" style="width: 32px; height: 32px; min-width: 32px;">
                                    {{ strtoupper(substr($dept->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ $dept->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($dept->code)
                                <code class="bg-light px-2 py-1 rounded font-size-12 text-dark">{{ $dept->code }}</code>
                            @else
                                <span class="text-muted font-size-12">—</span>
                            @endif
                        </td>
                        <td>
                            @if($dept->parent)
                                <span class="badge bg-light text-dark border font-size-11 px-2 py-1">
                                    <i class="bx bx-up-arrow-alt text-primary me-1 font-size-10"></i>{{ $dept->parent->name }}
                                </span>
                            @else
                                <span class="text-muted font-size-12">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php $count = $dept->employees_count ?? 0; @endphp
                            @if($count > 0)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 font-size-11">
                                    <i class="bx bx-user me-1 font-size-10"></i>{{ $count }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted rounded-pill px-2 py-1 font-size-11">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('subscriber.hris.departments.edit', $dept) }}" class="btn btn-sm btn-light border-0 px-2 py-1" title="Edit">
                                    <i class="bx bx-edit text-primary font-size-14"></i>
                                </a>
                                <form action="{{ route('subscriber.hris.departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Delete this department?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0 px-2 py-1" title="Delete">
                                        <i class="bx bx-trash text-danger font-size-14"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bx bx-git-branch text-muted font-size-24"></i>
                                </div>
                                <h6 class="text-muted mb-1 font-size-14">No Departments Found</h6>
                                <p class="text-muted font-size-12 mb-3">Create your first department to get started.</p>
                                <a href="{{ route('subscriber.hris.departments.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bx bx-plus me-1"></i> Add Department
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($departments->hasPages())
        <div class="card-footer bg-white border-top py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <span class="font-size-12 text-muted">
                    Showing {{ $departments->firstItem() }}–{{ $departments->lastItem() }} of {{ $departments->total() }}
                </span>
                {{ $departments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
