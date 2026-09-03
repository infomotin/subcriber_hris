@extends('layouts.subscriber')

@section('title', 'Attendance Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-calendar-check text-primary me-2"></i> Attendance Logs</h4>
        <p class="text-muted font-size-13 mb-0">Real-time attendance punch records from biometric machines.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success font-size-12" id="liveBadge"><i class="bx bx-pulse me-1"></i> Live (5s)</span>
        <a href="{{ route('subscriber.attendance.export') }}" class="btn btn-success btn-sm">
            <i class="bx bx-download me-1"></i> Export CSV
        </a>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2"><i class="bx bx-data"></i></div>
                    <div><span class="text-muted font-size-11">Total Records</span><br><span class="fw-bold font-size-14">{{ number_format($attendanceLogs->total()) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2"><i class="bx bx-log-in"></i></div>
                    <div><span class="text-muted font-size-11">Check Ins</span><br><span class="fw-bold font-size-14">{{ number_format($attendanceLogs->getCollection()->where('raw_status', 0)->count()) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger text-white me-2"><i class="bx bx-log-out"></i></div>
                    <div><span class="text-muted font-size-11">Check Outs</span><br><span class="fw-bold font-size-14">{{ number_format($attendanceLogs->getCollection()->where('raw_status', 1)->count()) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2"><i class="bx bx-show"></i></div>
                    <div><span class="text-muted font-size-11">On This Page</span><br><span class="fw-bold font-size-14">{{ $attendanceLogs->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="max-width: 160px;">
                <span class="input-group-text bg-light border-end-0 py-1"><i class="bx bx-search text-muted"></i></span>
                <input type="text" name="pin" class="form-control border-start-0 ps-0 bg-light" placeholder="Filter by PIN..." value="{{ request('pin') }}">
            </div>
            <input type="date" name="date" class="form-control form-control-sm bg-light border-0 py-1" style="max-width: 150px;" value="{{ request('date') }}">
            <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bx bx-search me-1"></i> Filter</button>
            @if(request()->has('pin') || request()->has('date'))
                <a href="{{ route('subscriber.attendance.index') }}" class="btn btn-sm btn-outline-secondary px-2"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="text-muted font-size-11 ms-auto">Showing {{ $attendanceLogs->firstItem() ?? 0 }}-{{ $attendanceLogs->lastItem() ?? 0 }} of {{ number_format($attendanceLogs->total()) }}</span>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:35px">#</th>
                        <th>PIN</th>
                        <th>User Name</th>
                        <th>Machine</th>
                        <th>Punched Time</th>
                        <th>Status</th>
                        <th>Verify</th>
                    </tr>
                </thead>
                <tbody id="liveAttendanceFeed">
                    @forelse($attendanceLogs as $log)
                        <tr>
                            <td class="text-muted">{{ $attendanceLogs->firstItem() + $loop->index }}</td>
                            <td><span class="fw-semibold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td><code>{{ $log->device->serial_number ?? 'N/A' }}</code></td>
                            <td>{{ $log->punched_at ? $log->punched_at->format('M d, Y h:i:s A') : 'N/A' }}</td>
                            <td><span class="badge {{ $log->raw_status == 0 ? 'bg-success' : 'bg-danger' }}">{{ $log->status_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ $log->verify_type_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bx bx-info-circle me-1"></i> No attendance punches recorded for your tenant.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendanceLogs->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $attendanceLogs->withQueryString()->links() }}
    </div>
    @endif
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
                        <td><span class="fw-semibold text-primary">${log.pin}</span></td>
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

                const emptyRow = feed.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();
            })
            .catch(() => {});
        }

        setInterval(pollNewLogs, 5000);
    });
</script>
@endpush
