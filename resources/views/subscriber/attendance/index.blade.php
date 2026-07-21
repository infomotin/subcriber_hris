@extends('layouts.subscriber')

@section('title', 'Attendance Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Tenant Attendance Logs</h4>
    <a href="{{ route('subscriber.attendance.export') }}" class="btn btn-success btn-sm">
        <i class="bx bx-download me-1"></i> Export CSV Report
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>PIN</th>
                        <th>User Name</th>
                        <th>Machine Serial</th>
                        <th>Punched Time</th>
                        <th>Punch Status</th>
                        <th>Verify Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceLogs as $log)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td><code>{{ $log->device->serial_number ?? 'N/A' }}</code></td>
                            <td>{{ $log->punched_at ? $log->punched_at->format('M d, Y h:i:s A') : 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $log->status_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ $log->verify_type_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No attendance punches recorded for your tenant.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
