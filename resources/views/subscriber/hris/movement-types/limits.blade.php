@extends('layouts.subscriber')

@section('title', 'Movement Monthly Limits')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-slider text-primary me-1.5 align-middle font-size-26"></i>Monthly Limits
        </h4>
        <small class="text-muted">Set monthly usage limits per movement type ({{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }})</small>
    </div>
    <a href="{{ route('subscriber.hris.movement-types.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<div class="row g-4">
    @foreach($types as $type)
        @php $limit = $limits->get($type->id); @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="fw-bold text-slate-800 mb-0">{{ $type->name }}</h6>
                            <code class="font-size-11 text-muted">{{ $type->code }} | {{ $type->duration_type === 'short_leave' ? 'Short Leave' : 'Day Out' }} | Max {{ $type->max_hours }}h</code>
                        </div>
                        @if($type->is_active)
                            <span class="badge bg-soft-success text-success font-size-10">Active</span>
                        @else
                            <span class="badge bg-soft-danger text-danger font-size-10">Inactive</span>
                        @endif
                    </div>

                    <form action="{{ route('subscriber.hris.movement-types.limits.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="movement_type_id" value="{{ $type->id }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 font-size-12">Max Allowed</span>
                            <input type="number" min="1" max="100" class="form-control border-start-0" name="max_allowed" value="{{ $limit?->max_allowed ?? 3 }}" style="font-weight:700;">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="bx bx-check font-size-16"></i>
                            </button>
                        </div>
                    </form>

                    @if($limit)
                        <div class="mt-2 text-end">
                            <small class="text-muted">Last updated: {{ $limit->updated_at->format('d M, H:i') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if($types->isEmpty())
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bx bx-transfer font-size-40 d-block mb-2"></i>
                    No movement types found. <a href="{{ route('subscriber.hris.movement-types.create') }}">Create one</a>.
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
