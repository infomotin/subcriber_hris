@extends('layouts.subscriber')

@section('title', 'Increment Rules')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-rule text-primary me-1.5 align-middle font-size-26"></i>Increment Rules
        </h4>
    </div>
    <div>
        <a href="{{ route('subscriber.hris.increment-rules.create') }}" class="btn btn-primary rounded-pill px-4" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-plus me-1"></i> New Rule
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Based On</th>
                    <th>Joining Date Range</th>
                    <th>Year Start</th>
                    <th>Special Max %</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td class="ps-4 fw-semibold text-slate-800">{{ $rule->name }}</td>
                        <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11 text-uppercase">{{ $rule->increment_based_on }}</span></td>
                        <td class="font-size-12">{{ $rule->joining_date_from?->format('d M Y') ?? 'Any' }} – {{ $rule->joining_date_to?->format('d M Y') ?? 'Any' }}</td>
                        <td class="font-size-12">{{ $rule->year_start_date?->format('d M Y') ?? '—' }}</td>
                        <td class="font-size-12">{{ $rule->special_max_percentage ?? '—' }}%</td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">Active</span>
                            @else
                                <span class="badge bg-soft-secondary text-secondary px-3 py-1.5 font-size-11">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('subscriber.hris.increment-rules.edit', $rule) }}" class="btn btn-sm btn-light border-0" title="Edit">
                                <i class="bx bx-edit text-muted"></i>
                            </a>
                            <form method="POST" action="{{ route('subscriber.hris.increment-rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm('Delete this rule?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light border-0 text-danger" title="Delete"><i class="bx bx-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No increment rules defined yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
