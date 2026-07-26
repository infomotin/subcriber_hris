@extends('layouts.subscriber')

@section('title', 'Employee Profiles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Employee Entry & Directory</h4>
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
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                            <span class="badge bg-soft-success text-success px-2 py-1">Active</span>
                                        @elseif($emp->status === 'probation')
                                            <span class="badge bg-soft-warning text-warning px-2 py-1">Probation</span>
                                        @elseif($emp->status === 'resigned')
                                            <span class="badge bg-soft-secondary text-secondary px-2 py-1">Resigned</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger px-2 py-1">{{ ucfirst($emp->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('subscriber.hris.employees.show', $emp) }}" class="btn btn-sm btn-light border-0" title="View Details">
                                                <i class="bx bx-show text-muted"></i>
                                            </a>
                                            <form action="{{ route('subscriber.hris.employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee profile? This will also delete their login account.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border-0 text-danger">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
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
