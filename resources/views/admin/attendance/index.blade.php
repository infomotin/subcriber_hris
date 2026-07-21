@extends('layouts.app')

@section('title', 'Attendance Logs & Reports')

@section('content')
<div class="page-title-box">
    <h4>Attendance Logs & Reports</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.attendance.export', request()->all()) }}" class="btn btn-success btn-sm rounded-pill shadow-sm">
            <i class="bx bx-download me-1"></i> Export Filtered CSV
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.attendance.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label font-size-12 fw-bold text-uppercase">Filter Device</label>
                    <select name="device_id" class="form-select">
                        <option value="">All Connected Devices</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}" {{ request('device_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name ?? $d->serial_number }} ({{ $d->serial_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label font-size-12 fw-bold text-uppercase">User PIN</label>
                    <input type="text" name="pin" class="form-control" value="{{ request('pin') }}" placeholder="Search PIN...">
                </div>

                <div class="col-md-2">
                    <label class="form-label font-size-12 fw-bold text-uppercase">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label font-size-12 fw-bold text-uppercase">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Apply Filter</button>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Logs Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User PIN</th>
                        <th>User Name</th>
                        <th>Device</th>
                        <th>Punched Time</th>
                        <th>Attendance Status</th>
                        <th>Verification</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><code>#{{ $log->id }}</code></td>
                            <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bx bx-chip me-1"></i> {{ $log->device->serial_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $log->punched_at->format('M d, Y h:i:s A') }}</td>
                            <td>
                                <span class="badge bg-soft-info text-info">
                                    {{ $log->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-soft-secondary text-secondary">
                                    <i class="bx bx-shield-check me-1"></i> {{ $log->verify_type_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bx bx-calendar-x font-size-36 text-secondary d-block mb-2"></i>
                                No attendance log records match the search filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
