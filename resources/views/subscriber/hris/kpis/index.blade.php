@extends('layouts.subscriber')

@section('title', 'KPI Goals')

@section('content')
<div class="page-title-box mb-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Performance</span>
        <h4 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-bar-chart text-primary me-2 align-middle"></i>KPI Goals
        </h4>
    </div>
    <div class="page-title-right">
        <a href="{{ route('subscriber.hris.kpis.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bx bx-plus me-1"></i> Add KPI Goal
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-size-12">
                    <i class="bx bx-layer me-1"></i> {{ $kpis->total() }} Total
                </span>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 font-size-13" placeholder="Search KPIs or employees..." value="{{ request('search') }}">
                </div>
                @if(request('search'))
                    <a href="{{ route('subscriber.hris.kpis.index') }}" class="btn btn-sm btn-light border text-muted px-2"><i class="bx bx-x"></i></a>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="font-size-12 text-uppercase text-muted fw-semibold letter-spacing-05">
                    <th class="ps-3 py-2" style="width: 40px;">#</th>
                    <th class="py-2">Employee</th>
                    <th class="py-2">Goal Title</th>
                    <th class="py-2">Target Date</th>
                    <th class="py-2">Weightage</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Score</th>
                    <th class="py-2 text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kpis as $kpi)
                    <tr>
                        <td class="ps-3 text-muted font-size-12">{{ ($kpis->currentPage() - 1) * $kpis->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($kpi->employee?->user?->name ?? 'U') }}&background=5f5af6&color=fff&size=32" class="rounded-circle" width="32" height="32">
                                <div>
                                    <span class="fw-semibold text-dark font-size-13">{{ $kpi->employee?->user?->name ?? 'N/A' }}</span>
                                    <code class="font-size-11 text-muted d-block">{{ $kpi->employee?->employee_id }}</code>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-semibold text-slate-800 font-size-13">{{ $kpi->goal_title }}</span></td>
                        <td>
                            <span class="font-size-12 text-muted">
                                <i class="bx bx-calendar-event me-1 font-size-11"></i>{{ \Carbon\Carbon::parse($kpi->target_date)->format('d M, Y') }}
                            </span>
                        </td>
                        <td><code class="bg-light px-2 py-1 rounded font-size-12 text-dark">{{ $kpi->weightage }}%</code></td>
                        <td>
                            @if($kpi->status === 'achieved')
                                <span class="badge bg-soft-success text-success px-2 py-1 font-size-11"><i class="bx bx-check-circle align-middle me-0.5"></i> Achieved</span>
                            @elseif($kpi->status === 'ongoing')
                                <span class="badge bg-soft-info text-info px-2 py-1 font-size-11"><i class="bx bx-time align-middle me-0.5"></i> Ongoing</span>
                            @elseif($kpi->status === 'defined')
                                <span class="badge bg-soft-primary text-primary px-2 py-1 font-size-11"><i class="bx bx-note align-middle me-0.5"></i> Defined</span>
                            @else
                                <span class="badge bg-soft-danger text-danger px-2 py-1 font-size-11">{{ ucfirst($kpi->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($kpi->score_rating)
                                <span class="fw-bold text-success font-size-13">{{ $kpi->score_rating }}<small class="text-muted">/10</small></span>
                            @else
                                <span class="text-muted font-size-12">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('subscriber.hris.kpis.edit', $kpi) }}" class="btn btn-sm btn-light border-0 px-2 py-1" title="Edit">
                                    <i class="bx bx-edit text-primary font-size-14"></i>
                                </a>
                                <form action="{{ route('subscriber.hris.kpis.destroy', $kpi) }}" method="POST" onsubmit="return confirm('Delete this KPI goal?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0 px-2 py-1" title="Delete">
                                        <i class="bx bx-trash text-danger font-size-14"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bx bx-bar-chart text-muted font-size-24"></i>
                                </div>
                                <h6 class="text-muted mb-1 font-size-14">No KPI Goals Found</h6>
                                <p class="text-muted font-size-12 mb-3">Create your first KPI goal to get started.</p>
                                <a href="{{ route('subscriber.hris.kpis.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bx bx-plus me-1"></i> Add KPI Goal
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kpis->hasPages())
        <div class="card-footer bg-white border-top py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <span class="font-size-12 text-muted">Showing {{ $kpis->firstItem() }}–{{ $kpis->lastItem() }} of {{ $kpis->total() }}</span>
                {{ $kpis->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
