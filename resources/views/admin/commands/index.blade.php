@extends('layouts.app')

@section('title', 'Device Command Queue Audit')

@section('content')
<div class="page-title-box">
    <h4>Device Command Queue Audit Log</h4>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.commands.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <select name="device_id" class="form-select">
                        <option value="">All Devices</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}" {{ request('device_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name ?? $d->serial_number }} ({{ $d->serial_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Command Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Queue</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent to Device</option>
                        <option value="executed" {{ request('status') == 'executed' ? 'selected' : '' }}>Executed (Success)</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter Queue</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Command ID</th>
                        <th>Target Device</th>
                        <th>Command Payload</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Return Code</th>
                        <th>Queued / Executed</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commands as $cmd)
                        <tr>
                            <td><code>#{{ $cmd->id }}</code></td>
                            <td>
                                <span class="fw-bold">{{ $cmd->device->name ?? $cmd->device->serial_number ?? 'N/A' }}</span>
                            </td>
                            <td><code>{{ $cmd->formatted_command }}</code></td>
                            <td><span class="badge bg-soft-info text-info">{{ $cmd->type }}</span></td>
                            <td>
                                <span class="badge {{ $cmd->status === 'executed' ? 'bg-soft-success text-success' : ($cmd->status === 'pending' ? 'bg-soft-warning text-warning' : ($cmd->status === 'sent' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger')) }}">
                                    {{ strtoupper($cmd->status) }}
                                </span>
                            </td>
                            <td>
                                @if(!is_null($cmd->return_code))
                                    <span class="badge {{ $cmd->return_code == 0 ? 'bg-success' : 'bg-danger' }}">
                                        Code {{ $cmd->return_code }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $cmd->created_at->format('M d, H:i') }}</div>
                                @if($cmd->executed_at)
                                    <small class="text-muted">Executed: {{ $cmd->executed_at->diffForHumans() }}</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.commands.destroy', $cmd) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete command log?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light"><i class="bx bx-trash text-danger"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bx bx-terminal font-size-36 text-secondary d-block mb-2"></i>
                                No device commands found in the audit queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($commands->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $commands->links() }}
        </div>
    @endif
</div>
@endsection
