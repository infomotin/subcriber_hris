@extends('layouts.subscriber')

@section('title', 'KPI Goals')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">KPI Goals Setup</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.kpis.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Add KPI Goal
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Goal Title</th>
                                <th>Target Date</th>
                                <th>Weightage</th>
                                <th>Status</th>
                                <th>Score Rating</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kpis as $kpi)
                                <tr>
                                    <td><strong>{{ $kpi->employee->user->name ?? 'N/A' }}</strong> <br><small class="text-muted">ID: {{ $kpi->employee->employee_id ?? 'N/A' }}</small></td>
                                    <td>{{ $kpi->goal_title }}</td>
                                    <td><span class="text-muted"><i class="bx bx-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($kpi->target_date)->format('M d, Y') }}</span></td>
                                    <td><code>{{ $kpi->weightage }}%</code></td>
                                    <td>
                                        @if($kpi->status === 'achieved')
                                            <span class="badge bg-soft-success text-success px-2 py-1">Achieved</span>
                                        @elseif($kpi->status === 'ongoing')
                                            <span class="badge bg-soft-info text-info px-2 py-1">Ongoing</span>
                                        @elseif($kpi->status === 'defined')
                                            <span class="badge bg-soft-primary text-primary px-2 py-1">Defined</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger px-2 py-1">{{ ucfirst($kpi->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kpi->score_rating)
                                            <span class="fw-bold text-success">{{ $kpi->score_rating }} / 10</span>
                                        @else
                                            <span class="text-muted">Not Reviewed</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('subscriber.hris.kpis.edit', $kpi) }}" class="btn btn-sm btn-light border-0">
                                                <i class="bx bx-edit text-muted"></i>
                                            </a>
                                            <form action="{{ route('subscriber.hris.kpis.destroy', $kpi) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this KPI goal?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border-0 text-danger">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No KPI goals found. Click "Add KPI Goal" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($kpis->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $kpis->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
