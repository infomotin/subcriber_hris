@extends('layouts.subscriber')

@section('title', 'Work Shifts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Shift Setup</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.shifts.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Add Shift
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
                                <th>Shift Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Late Buffer (Minutes)</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                                <tr>
                                    <td><strong>{{ $shift->name }}</strong></td>
                                    <td><span class="badge bg-soft-primary text-primary">{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</span></td>
                                    <td><span class="badge bg-soft-info text-info">{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</span></td>
                                    <td><code>{{ $shift->late_buffer_time ? \Carbon\Carbon::parse($shift->late_buffer_time)->format('i') . ' mins' : 'None' }}</code></td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('subscriber.hris.shifts.edit', $shift) }}" class="btn btn-sm btn-light border-0">
                                                <i class="bx bx-edit text-muted"></i>
                                            </a>
                                            <form action="{{ route('subscriber.hris.shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift?');">
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
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No shifts found. Click "Add Shift" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($shifts->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
