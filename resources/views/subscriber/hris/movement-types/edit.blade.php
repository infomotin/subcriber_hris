@extends('layouts.subscriber')

@section('title', 'Edit Movement Type')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-transfer-alt text-primary me-1.5 align-middle font-size-26"></i>Edit Movement Type
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.movement-types.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <form action="{{ route('subscriber.hris.movement-types.update', $type) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $type->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $type->code) }}" required maxlength="20">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Duration Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('duration_type') is-invalid @enderror" name="duration_type" id="durationType" required>
                            <option value="short_leave" {{ old('duration_type', $type->duration_type) === 'short_leave' ? 'selected' : '' }}>Short Leave (Out + Return)</option>
                            <option value="day_out" {{ old('duration_type', $type->duration_type) === 'day_out' ? 'selected' : '' }}>Day Out (Out only, no return)</option>
                        </select>
                        @error('duration_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Max Hours <span class="text-danger">*</span></label>
                        <input type="number" step="0.5" min="0.5" max="24" class="form-control @error('max_hours') is-invalid @enderror" name="max_hours" value="{{ old('max_hours', $type->max_hours) }}" required>
                        @error('max_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="requires_return" id="requiresReturn" value="1" {{ old('requires_return', $type->requires_return) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-slate-700" for="requiresReturn">Requires Return Time</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-slate-700" for="isActive">Active</label>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <a href="{{ route('subscriber.hris.movement-types.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
                            <i class="bx bx-save me-1.5 align-middle font-size-18"></i> Update Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('durationType').addEventListener('change', function() {
    document.getElementById('requiresReturn').checked = this.value === 'short_leave';
});
</script>
@endpush
