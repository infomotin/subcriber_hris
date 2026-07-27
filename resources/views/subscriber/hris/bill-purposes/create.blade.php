@extends('layouts.subscriber')

@section('title', 'Create Bill Purpose')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-plus-circle text-primary me-1.5 align-middle font-size-26"></i>New Bill Purpose
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.bill-purposes.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;max-width:600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.bill-purposes.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-slate-700">Purpose Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="e.g. Office Supply, Travel, Utility Bill" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-slate-700">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="Brief description of this bill purpose...">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-slate-700">Active</label>
                </div>
            </div>
            <div class="text-end pt-3 border-top">
                <a href="{{ route('subscriber.hris.bill-purposes.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5">Create Purpose</button>
            </div>
        </form>
    </div>
</div>
@endsection
