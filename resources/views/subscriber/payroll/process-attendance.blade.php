@extends('layouts.subscriber')

@section('title', 'Process Attendance - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
</style>

<div class="page-title-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Databases</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Process Attendance</h4>
    </div>
</div>

<div class="card mb-4 p-4" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(99,102,241,0.15) !important;">
    <div class="d-flex align-items-center gap-3">
        <i class="bx bx-info-circle text-primary font-size-24"></i>
        <div>
            <strong class="font-size-13">How it works:</strong>
            <p class="font-size-12 text-muted mb-0 mt-1">
                Processes raw punch data into timecard records. For each employee per day: first punch = <strong>In Time</strong>,
                last punch = <strong>Out Time</strong>. Checks shift schedule, weekends, and approved leave to determine
                day status (Present/Absent/Leave/Holiday/Weekend). Late and early minutes are calculated against shift timings.
            </p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Present</span>
                    <h3 class="mt-2 mb-0 fw-bold text-success">{{ $stats['present']->count ?? 0 }}</h3>
                </div>
                <div class="stat-icon bg-green-50 border border-green-100 text-green-600"><i class="bx bx-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Leave</span>
                    <h3 class="mt-2 mb-0 fw-bold text-warning">{{ $stats['leave']->count ?? 0 }}</h3>
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600"><i class="bx bx-calendar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Absent / Weekend / Holiday</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ ($stats['absent']->count ?? 0) + ($stats['weekend']->count ?? 0) + ($stats['holiday']->count ?? 0) }}</h3>
                </div>
                <div class="stat-icon bg-slate-50 border border-slate-200 text-slate-500"><i class="bx bx-x-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bx bx-cog text-primary me-1"></i> Process Raw Punch Data</h6>
            <form method="POST" action="{{ route('subscriber.payroll.process-attendance.run') }}" onsubmit="return confirm('Process raw punch data for the selected month? Existing processed records for this month will be replaced.')">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold font-size-13">Select Month</label>
                    <select name="month" class="form-select" required>
                        <option value="">— Select Month —</option>
                        @foreach($months as $m)
                            <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                {{ date('F Y', strtotime($m . '-01')) }}
                                @if(isset($processedGroups[$m]))
                                    ({{ $processedGroups[$m]->count }} processed)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bx bx-play-circle me-2"></i> Run Process
                </button>
            </form>

            <hr class="my-4">

            <h6 class="fw-bold mb-3 text-danger"><i class="bx bx-undo me-1"></i> Undo Processed Data</h6>
            <form method="POST" action="{{ route('subscriber.payroll.process-attendance.undo') }}" onsubmit="return confirmUndo(event)">
                @csrf
                @method('DELETE')
                <div class="mb-3">
                    <label class="form-label fw-semibold font-size-13">Select Month</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month', date('Y-m')) }}" required>
                </div>
                <div class="mb-3 p-3 rounded-3 font-size-12" id="undoPreview" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <span class="text-muted">Select a month to see record count...</span>
                </div>
                <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                    <i class="bx bx-trash me-2"></i> Undo & Remove
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-table text-primary me-1"></i> Processed Timecard</h6>
                <form method="GET" action="{{ route('subscriber.payroll.process-attendance') }}" class="d-flex gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" style="width:150px;" value="{{ request('month', date('Y-m')) }}" onchange="this.form.submit()">
                </form>
            </div>

            @if(isset($records) && $records->count())
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle font-size-12">
                        <thead class="text-muted text-uppercase font-size-10">
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>In</th>
                                <th>Out</th>
                                <th>Hours</th>
                                <th>Late</th>
                                <th>Early</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $r)
                                <tr>
                                    <td class="fw-semibold">{{ $r->employee_id }}</td>
                                    <td>{{ $r->date }}</td>
                                    <td>{{ substr($r->day_name, 0, 3) }}</td>
                                    <td>{{ $r->in_time ? date('h:i A', strtotime($r->in_time)) : '—' }}</td>
                                    <td>{{ $r->out_time ? date('h:i A', strtotime($r->out_time)) : '—' }}</td>
                                    <td>{{ $r->total_hours }}h</td>
                                    <td>
                                        @if($r->is_late)
                                            <span class="text-danger fw-bold">{{ $r->late_minutes }}m</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($r->is_early)
                                            <span class="text-warning fw-bold">{{ $r->early_minutes }}m</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($r->status)
                                            @case('present')
                                                <span class="badge bg-success font-size-10 rounded-pill">Present</span>
                                                @break
                                            @case('absent')
                                                <span class="badge bg-danger font-size-10 rounded-pill">Absent</span>
                                                @break
                                            @case('leave')
                                                <span class="badge bg-warning text-dark font-size-10 rounded-pill">Leave</span>
                                                @break
                                            @case('holiday')
                                                <span class="badge bg-info font-size-10 rounded-pill">Holiday</span>
                                                @break
                                            @case('weekend')
                                                <span class="badge bg-secondary font-size-10 rounded-pill">Weekend</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-muted font-size-10 rounded-pill">{{ $r->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $records->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-calendar text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">
                        @if(request('month'))
                            No processed records for {{ date('F Y', strtotime(request('month') . '-01')) }}.
                            Select a month and click <strong>Run Process</strong>.
                        @else
                            Select a month to view processed attendance records.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    let procMonthCount = {};

    document.querySelector('input[name="month"]')?.addEventListener('change', function() {
        const val = this.value;
        if (!val) return;
        fetch('{{ route("subscriber.payroll.process-attendance.month-count") }}?month=' + val)
            .then(r => r.json())
            .then(data => {
                procMonthCount[val] = data.count;
                document.getElementById('undoPreview').innerHTML =
                    '<span class="fw-bold">' + data.count + '</span> <span class="text-muted">processed records for <strong>' + val + '</strong></span>';
            })
            .catch(() => {
                document.getElementById('undoPreview').innerHTML = '<span class="text-muted">Could not load count.</span>';
            });
    });

    function confirmUndo(e) {
        e.preventDefault();
        const form = e.target;
        const month = form.querySelector('input[name="month"]').value;
        const count = procMonthCount[month];
        const msg = count !== undefined
            ? 'Delete ' + count + ' processed attendance record(s) for ' + month + '?\n\nThis cannot be undone.'
            : 'Delete processed records for ' + month + '?';
        if (confirm(msg)) form.submit();
    }
</script>
@endpush
@endsection