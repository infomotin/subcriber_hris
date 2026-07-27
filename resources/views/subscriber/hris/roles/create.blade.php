@extends('layouts.subscriber')

@section('title', 'Create Role')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-plus-circle text-primary me-1.5 align-middle font-size-26"></i>Create New Role
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('subscriber.hris.roles.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-info-circle text-primary me-1.5"></i> Role Info
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="e.g. HR Manager" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="text-end pt-3 border-top">
                        <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Create Role</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-lock text-primary me-1.5"></i> Assign Permissions
                    </h6>
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11" onclick="toggleAll(true)">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-size-11" onclick="toggleAll(false)">Deselect All</button>
                    </div>
                    @foreach($permissions as $group => $perms)
                        <div class="mb-3">
                            <div class="fw-bold text-slate-700 font-size-12 text-uppercase mb-2" style="letter-spacing:0.04em;">
                                <i class="bx bx-folder me-1"></i> {{ $group ?? 'General' }}
                            </div>
                            <div class="row g-2">
                                @foreach($perms as $perm)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}" {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label font-size-12" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
}
</script>
@endpush
@endsection
