@extends('layouts.subscriber')

@section('title', 'Realtime Punch Logs Feed')

@section('content')
<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Realtime Punch Logs Feed</h4>
    </div>
</div>

<div class="card border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-time me-1 text-primary align-middle font-size-18"></i> Punch Logs Feed</span>
        <div class="d-flex align-items-center gap-2.5">
            <span class="badge bg-success font-size-11 px-2.5 py-1.5" id="liveBadge"><i class="bx bx-pulse me-1"></i> Live feed (5s)</span>
            <a href="{{ route('subscriber.attendance.index') }}" class="btn btn-sm btn-outline-primary font-size-12 px-3 py-1.5 rounded-pill">View All Logs</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="border: 0 !important; border-radius: 0 !important;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>User PIN</th>
                        <th>User Name</th>
                        <th>Machine</th>
                        <th>Punched Time</th>
                        <th>Status</th>
                        <th>Verification</th>
                    </tr>
                </thead>
                <tbody id="livePunchFeed">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td><code>{{ $log->device->serial_number ?? 'N/A' }}</code></td>
                            <td>{{ $log->punched_at->format('M d, Y h:i:s A') }}</td>
                            <td><span class="badge bg-soft-info text-info">{{ $log->status_label }}</span></td>
                            <td><span class="badge bg-soft-secondary text-secondary">{{ $log->verify_type_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No attendance punches recorded for your organization yet.</td></tr>
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
        const punchFeed = document.getElementById('livePunchFeed');
        let lastLogId = {{ $recentLogs->first()?->id ?? 0 }};

        function fetchLiveStats() {
            fetch('{{ route("subscriber.dashboard.stats") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.recent_logs && data.recent_logs.length > 0) {
                    punchFeed.innerHTML = '';
                    data.recent_logs.forEach(log => {
                        let statusBadge = 'bg-soft-info text-info';
                        if (log.status_label === 'Check In') statusBadge = 'bg-soft-success text-success';
                        else if (log.status_label === 'Check Out') statusBadge = 'bg-soft-danger text-danger';

                        punchFeed.innerHTML += `
                            <tr>
                                <td><span class="fw-bold text-primary">${log.pin}</span></td>
                                <td>${log.user_name}</td>
                                <td><code>${log.device_serial}</code></td>
                                <td>${log.punched_at}</td>
                                <td><span class="badge ${statusBadge}">${log.status_label}</span></td>
                                <td><span class="badge bg-soft-secondary text-secondary">${log.verify_type_label}</span></td>
                            </tr>`;
                    });
                }
            })
            .catch(() => {});
        }

        setInterval(fetchLiveStats, 5000);
    });
</script>
@endpush