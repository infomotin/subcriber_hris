@extends('layouts.subscriber')

@section('title', 'Departments')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Department Setup</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.departments.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Add Department
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Parent Department</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                                <tr>
                                    <td><strong>{{ $dept->name }}</strong></td>
                                    <td><code>{{ $dept->code ?? 'N/A' }}</code></td>
                                    <td>{{ $dept->parent->name ?? 'None (Top Level)' }}</td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('subscriber.hris.departments.edit', $dept) }}" class="btn btn-sm btn-light border-0">
                                                <i class="bx bx-edit text-muted"></i>
                                            </a>
                                            <form action="{{ route('subscriber.hris.departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?');">
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
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No departments found. Click "Add Department" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($departments->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
