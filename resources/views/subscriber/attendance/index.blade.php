@extends('layouts.subscriber')

@section('title', 'Attendance Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Tenant Attendance Logs</h4>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success font-size-12" id="liveBadge"><i class="bx bx-pulse me-1"></i> Live (5s)</span>
        <a href="{{ route('subscriber.attendance.export') }}" class="btn btn-success btn-sm">
            <i class="bx bx-download me-1"></i> Export CSV Report
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>PIN</th>
                        <th>User Name</th>
                        <th>Machine Serial</th>
                        <th>Punched Time</th>
                        <th>Punch Status</th>
                        <th>Verify Type</th>
                    </tr>
                </thead>
                <tbody id="liveAttendanceFeed">
                    @forelse($attendanceLogs as $log)
                        <tr>
                            <td class="text-muted">{{ $log->id }}</td>
                            <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td><code>{{ $log->device->serial_number ?? 'N/A' }}</code></td>
                            <td>{{ $log->punched_at ? $log->punched_at->format('M d, Y h:i:s A') : 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $log->status_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ $log->verify_type_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No attendance punches recorded for your tenant.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const feed = document.getElementById('liveAttendanceFeed');
        let lastLogId = {{ $attendanceLogs->first()?->id ?? 0 }};

        function pollNewLogs() {
            fetch('{{ route("subscriber.attendance.live") }}?after=' + lastLogId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.logs || data.logs.length === 0) return;

                data.logs.forEach(log => {
                    const statusBadge = log.status_label === 'Check In'
                        ? 'bg-success text-white'
                        : log.status_label === 'Check Out'
                            ? 'bg-danger text-white'
                            : 'bg-info text-white';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-muted">${log.id}</td>
                        <td><span class="fw-bold text-primary">${log.pin}</span></td>
                        <td>${log.user_name}</td>
                        <td><code>${log.device_serial}</code></td>
                        <td>${log.punched_at}</td>
                        <td><span class="badge ${statusBadge}">${log.status_label}</span></td>
                        <td><span class="badge bg-secondary text-white">${log.verify_type_label}</span></td>
                    `;

                    row.style.backgroundColor = '#d4edda';
                    feed.insertBefore(row, feed.firstChild);

                    setTimeout(() => { row.style.backgroundColor = ''; }, 2000);

                    if (log.id > lastLogId) lastLogId = log.id;
                });

                // Remove empty message if present
                const emptyRow = feed.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();
            })
            .catch(() => {});
        }

        setInterval(pollNewLogs, 5000);
    });
</script>
@endpush
