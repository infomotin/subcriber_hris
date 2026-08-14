@extends('layouts.system_admin')

@section('title', 'System & Server Monitoring')

@section('content')
<style>
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .status-dot.online { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.5); }
    .status-dot.offline { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.5); }
</style>

<meta http-equiv="refresh" content="30">
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-line-chart text-warning me-2 font-size-22"></i> System & Server Monitoring Hub</h4>
        <p class="text-muted font-size-13 mb-0">Realtime server port listening, ZK device data flow visualization, punch activity, and system health monitor.</p>
    </div>
</div>

<!-- Health & Hardware Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">CPU Load</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['cpu_load'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-primary rounded p-2">
                        <i class="bx bx-chip text-primary font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Memory Usage</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['memory_usage'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-success rounded p-2">
                        <i class="bx bx-hdd text-success font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">Disk Space Free</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $metrics['disk_free'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-warning rounded p-2">
                        <i class="bx bx-pie-chart-alt-2 text-warning font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted font-size-12 fw-bold text-uppercase mb-1">PHP Engine</p>
                        <h4 class="fw-bold text-dark mb-0">v{{ $metrics['php_version'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-soft-info rounded p-2">
                        <i class="bx bxl-php text-info font-size-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Health Check Audit Banner -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-pulse text-danger me-2"></i> System Health Audit Checks</h5>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            @foreach($healthCheck as $checkName => $info)
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-capitalize text-dark mb-1">{{ str_replace('_', ' ', $checkName) }}</h6>
                            <small class="text-muted d-block">{{ $info['message'] }}</small>
                        </div>
                        <span class="badge {{ $info['status'] === 'ok' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($info['status']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Port Listening & ZK Data Flow -->
<div class="row g-4 mb-4">
    <!-- Listening Ports -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-cog text-primary me-2"></i> Active Listening Ports</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Port</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listeningPorts as $port)
                                <tr>
                                    <td><span class="badge bg-dark font-monospace">{{ $port['port'] }}</span></td>
                                    <td class="font-size-11">{{ $port['service'] }}</td>
                                    <td><span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Active</span></td>
                                    <td>
                                        @if($portActivity[$port['port']] ?? null)
                                            <small class="text-muted">{{ $portActivity[$port['port']]['punch_count'] }} punches/hr</small>
                                        @else
                                            <small class="text-muted">—</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No listening ports detected</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ZK Device Data Flow Wave -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-waveform text-success me-2"></i> ZK Data Flow Wave</h5>
                <div>
                    <span class="badge bg-success font-size-11"><i class="bx bx-radio-circle me-1"></i>Live</span>
                    <span class="badge bg-info font-size-11 ms-1">{{ $zkFlow['total_last_5min'] }} punches (5min)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <canvas id="zkDataFlowWave" height="120" class="w-100"></canvas>
            </div>
            <div class="card-body pt-0">
                <div class="row g-2">
                    @foreach($zkFlow['device_summaries'] as $device)
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <span class="status-dot online me-2"></span>
                                <div class="flex-grow-1">
                                    <small class="fw-bold text-dark d-block">{{ $device['serial'] }}</small>
                                    <small class="text-muted font-size-10">{{ $device['ip'] }} · {{ $device['punch_count'] }} punches/hr</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ZK Real-Time Punch Feed -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-data text-primary me-2"></i> Real-Time Punch Feed</h5>
        <span class="text-muted font-size-11">Last {{ count($zkFlow['recent_flow']) }} punches</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="zkPunchFeed">
                <thead class="bg-light">
                    <tr>
                        <th>Time</th>
                        <th>Employee</th>
                        <th>Device</th>
                        <th>Direction</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zkFlow['recent_flow'] as $punch)
                        <tr>
                            <td><small class="font-monospace">{{ $punch['timestamp'] }}</small></td>
                            <td><span class="fw-bold">{{ $punch['employee_id'] }}</span></td>
                            <td><small>{{ $punch['serial'] }}</small></td>
                            <td>
                                <span class="badge {{ $punch['direction'] === 'IN' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $punch['direction'] }}
                                </span>
                            </td>
                            <td><span class="badge bg-secondary">Matched</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No punch data in the last 5 minutes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category-Based Logs Filter & List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-receipt text-primary me-2"></i> System Activity Logs by Category</h5>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'all']) }}" class="btn {{ $category === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All Logs</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'info']) }}" class="btn {{ $category === 'info' ? 'btn-info text-white' : 'btn-outline-secondary' }}">System / Info</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'warning']) }}" class="btn {{ $category === 'warning' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Warnings</a>
            <a href="{{ route('admin.system.monitoring.index', ['category' => 'error']) }}" class="btn {{ $category === 'error' ? 'btn-danger' : 'btn-outline-secondary' }}">Errors</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Category Level</th>
                        <th>Log Message</th>
                        <th>Ip / User</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>#{{ $log->id }}</td>
                            <td>
                                <span class="badge {{ $log->level === 'error' ? 'bg-danger' : ($log->level === 'warning' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ strtoupper($log->level) }}
                                </span>
                            </td>
                            <td><span class="font-monospace text-dark">{{ $log->message }}</span></td>
                            <td><small class="text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</small></td>
                            <td><small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No system activity logs recorded for this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $logs->links() }}
    </div>
</div>

<script>
(function() {
    var canvas = document.getElementById('zkDataFlowWave');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var waveData = @json($zkFlow['punches_by_minute']);
    var waveLabels = Object.keys(waveData).map(Number);
    var waveValues = Object.values(waveData);

    function draw() {
        var w = canvas.parentElement.offsetWidth;
        var h = 120;
        canvas.width = w;
        canvas.height = h;
        ctx.clearRect(0, 0, w, h);
        if (waveValues.length === 0) {
            ctx.fillStyle = '#999';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No punch data in last 5 minutes', w / 2, h / 2);
            return;
        }
        var maxVal = Math.max.apply(null, waveValues.concat([1]));
        var padding = 20;
        var drawW = w - padding * 2;
        var drawH = h - padding * 2;
        ctx.beginPath();
        ctx.moveTo(padding, h - padding);
        for (var i = 0; i < waveValues.length; i++) {
            var x = padding + (i / Math.max(waveValues.length - 1, 1)) * drawW;
            var y = h - padding - (waveValues[i] / maxVal) * drawH;
            ctx.lineTo(x, y);
        }
        ctx.lineTo(padding + drawW, h - padding);
        ctx.closePath();
        var grad = ctx.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, 'rgba(79,70,229,0.3)');
        grad.addColorStop(1, 'rgba(79,70,229,0.02)');
        ctx.fillStyle = grad;
        ctx.fill();
        ctx.beginPath();
        for (var i = 0; i < waveValues.length; i++) {
            var x = padding + (i / Math.max(waveValues.length - 1, 1)) * drawW;
            var y = h - padding - (waveValues[i] / maxVal) * drawH;
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        }
        ctx.strokeStyle = '#4f46e5';
        ctx.lineWidth = 2.5;
        ctx.stroke();
        for (var i = 0; i < waveValues.length; i++) {
            var x = padding + (i / Math.max(waveValues.length - 1, 1)) * drawW;
            var y = h - padding - (waveValues[i] / maxVal) * drawH;
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fillStyle = '#4f46e5';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 1.5;
            ctx.stroke();
        }
        ctx.fillStyle = '#666';
        ctx.font = '10px sans-serif';
        ctx.textAlign = 'center';
        for (var i = 0; i < waveLabels.length; i++) {
            var x = padding + (i / Math.max(waveLabels.length - 1, 1)) * drawW;
            ctx.fillText(waveLabels[i] + 'm', x, h - 4);
        }
    }
    draw();
    window.addEventListener('resize', draw);
})();
</script>
@endsection
