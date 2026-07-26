@extends('layouts.subscriber')

@section('title', 'Leaves Management')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-calendar-check text-primary me-1.5 align-middle font-size-26"></i>Leaves Management
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.leaves.apply') }}" class="btn btn-primary rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-plus me-1"></i> New Application
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Employee</th>
                    <th>Leave Type</th>
                    <th>Duration</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($leave->employee?->user?->name ?? 'U') }}&background=5f5af6&color=fff&size=28" class="rounded-circle" width="30" height="30">
                                <div>
                                    <span class="fw-semibold text-slate-800 font-size-13">{{ $leave->employee?->user?->name ?? 'N/A' }}</span>
                                    <code class="font-size-11 text-muted d-block">{{ $leave->employee?->employee_id }}</code>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11">{{ $leave->leaveType->name ?? 'N/A' }}</span></td>
                        <td class="font-size-12">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($leave->end_date)->format('d M, Y') }}
                        </td>
                        <td><code>{{ $leave->total_days }}d</code></td>
                        <td style="max-width: 180px;">
                            <span class="text-truncate d-inline-block font-size-12" style="max-width: 180px;">{{ $leave->reason }}</span>
                        </td>
                        <td>
                            @if($leave->status === 'approved')
                                <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">
                                    <i class="bx bx-check-circle align-middle me-0.5"></i> Approved
                                </span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge bg-soft-danger text-danger px-3 py-1.5 font-size-11">
                                    <i class="bx bx-x-circle align-middle me-0.5"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-soft-warning text-warning px-3 py-1.5 font-size-11">
                                    <i class="bx bx-time align-middle me-0.5"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($leave->status === 'pending')
                                <div class="d-flex justify-content-end gap-1">
                                    <form method="POST" action="{{ route('subscriber.hris.leaves.approve', $leave) }}" class="d-inline" onsubmit="return confirm('Approve this leave?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success rounded-pill px-3 font-size-11">
                                            <i class="bx bx-check me-0.5"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11" onclick="showRejectModal({{ $leave->id }})">
                                        <i class="bx bx-x me-0.5"></i> Reject
                                    </button>
                                    <form action="{{ route('subscriber.hris.leaves.destroy', $leave) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border-0 text-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </div>
                            @else
                                <span class="font-size-11 text-muted">
                                    by {{ $leave->actionedBy?->name ?? 'System' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bx bx-calendar-x font-size-40 d-block mb-2"></i>
                            No leave applications found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($leaves->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $leaves->firstItem() }}–{{ $leaves->lastItem() }} of {{ $leaves->total() }}</small>
        <div>{{ $leaves->links() }}</div>
    </div>
@endif

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px;">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-2 px-4 pt-4">
                    <h5 class="fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">
                        <i class="bx bx-x-circle text-danger me-1.5 align-middle"></i> Reject Leave
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label class="form-label fw-semibold text-slate-700">Remarks</label>
                    <textarea class="form-control" name="action_remarks" rows="3" placeholder="Reason for rejection..."></textarea>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRejectModal(leaveId) {
    document.getElementById('rejectForm').action = '{{ route("subscriber.hris.leaves.reject", "PLACEHOLDER") }}'.replace('PLACEHOLDER', leaveId);
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
