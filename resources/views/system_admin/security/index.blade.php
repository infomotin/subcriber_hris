@extends('layouts.system_admin')

@section('title', 'System Security Audit & IP Lockout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-shield-x text-danger me-2 font-size-22"></i> System Security Audit & IP Lockout</h4>
        <p class="text-muted font-size-13 mb-0">Monitor security events, failed login attempts, active sessions, and IP address blocklists.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Block IP Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-block text-danger me-2"></i> Manual IP Security Blocklist</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.system.security.block_ip') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">IP Address to Block</label>
                        <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.1.250" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark font-size-13">Lockout Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="Repeated failed login attempts">
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-bold">
                        <i class="bx bx-block me-1"></i> Block IP Address
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Blocked IPs Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-list-minus text-primary me-2"></i> Currently Blocked IP Addresses</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>IP Address</th>
                                <th>Reason</th>
                                <th>Blocked At</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedIps as $item)
                                <tr>
                                    <td><code class="fw-bold text-danger">{{ $item['ip'] }}</code></td>
                                    <td><small class="text-muted">{{ $item['reason'] }}</small></td>
                                    <td><small class="text-muted">{{ $item['blocked_at'] }}</small></td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.system.security.unblock_ip') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="ip_address" value="{{ $item['ip'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bx bx-check me-1"></i> Unblock
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No IP addresses currently blocked in security blacklist.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security Audit Log -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-error text-warning me-2"></i> Security Event & Warning Logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Severity</th>
                        <th>Security Event Message</th>
                        <th>Origin IP</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($securityLogs as $sLog)
                        <tr>
                            <td>#{{ $sLog->id }}</td>
                            <td>
                                <span class="badge {{ $sLog->level === 'error' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ strtoupper($sLog->level) }}
                                </span>
                            </td>
                            <td><span class="font-monospace text-dark">{{ $sLog->message }}</span></td>
                            <td><code class="text-dark">{{ $sLog->ip_address ?? '127.0.0.1' }}</code></td>
                            <td><small class="text-muted">{{ $sLog->created_at->format('M d, Y H:i:s') }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No security warnings or failure events logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $securityLogs->links() }}
    </div>
</div>
@endsection
