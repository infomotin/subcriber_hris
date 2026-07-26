@extends('layouts.subscriber')

@section('title', 'Leaves Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Leaves Management</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.leaves.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Add Leave Application
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
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Total Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td><strong>{{ $leave->employee->user->name ?? 'N/A' }}</strong> <br><small class="text-muted">ID: {{ $leave->employee->employee_id ?? 'N/A' }}</small></td>
                                    <td><span class="badge bg-soft-secondary text-dark">{{ $leave->leaveType->name ?? 'N/A' }}</span></td>
                                    <td>
                                        <div class="font-size-13">
                                            <span><strong>From:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</span> <br>
                                            <span><strong>To:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $leave->total_days }} days</code></td>
                                    <td><span class="text-wrap d-inline-block" style="max-width: 200px;">{{ $leave->reason }}</span></td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-soft-success text-success px-2 py-1">Approved</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-soft-danger text-danger px-2 py-1">Rejected</span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning px-2 py-1">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('subscriber.hris.leaves.edit', $leave) }}" class="btn btn-sm btn-light border-0">
                                                <i class="bx bx-edit text-muted"></i>
                                            </a>
                                            <form action="{{ route('subscriber.hris.leaves.destroy', $leave) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave application?');">
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
                                        No leave applications found. Click "Add Leave Application" to apply.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($leaves->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
