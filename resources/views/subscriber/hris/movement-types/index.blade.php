@extends('layouts.subscriber')

@section('title', 'Movement Types')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-transfer-alt text-primary me-1.5 align-middle font-size-26"></i>Movement Types
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.movement-types.limits') }}" class="btn btn-outline-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-slider me-1"></i> Monthly Limits
        </a>
        <a href="{{ route('subscriber.hris.movement-types.create') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-plus me-1"></i> Add Type
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Code</th>
                    <th>Duration Type</th>
                    <th>Max Hours</th>
                    <th>Requires Return</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $type->name }}</td>
                        <td><code>{{ $type->code }}</code></td>
                        <td>
                            @if($type->duration_type === 'short_leave')
                                <span class="badge bg-soft-info text-info px-3 py-1.5 font-size-11">
                                    <i class="bx bx-time align-middle me-0.5"></i> Short Leave
                                </span>
                            @else
                                <span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11">
                                    <i class="bx bx-sun align-middle me-0.5"></i> Day Out
                                </span>
                            @endif
                        </td>
                        <td>{{ $type->max_hours }}h</td>
                        <td>
                            @if($type->requires_return)
                                <span class="badge bg-soft-success text-success">Yes</span>
                            @else
                                <span class="badge bg-soft-secondary text-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($type->is_active)
                                <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">Active</span>
                            @else
                                <span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('subscriber.hris.movement-types.edit', $type) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11">
                                    <i class="bx bx-edit me-0.5"></i> Edit
                                </a>
                                <form action="{{ route('subscriber.hris.movement-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bx bx-transfer font-size-40 d-block mb-2"></i>
                            No movement types found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($types->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $types->firstItem() }}-{{ $types->lastItem() }} of {{ $types->total() }}</small>
        <div>{{ $types->links() }}</div>
    </div>
@endif
@endsection
