@extends('layouts.subscriber')

@section('title', 'Edit Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-edit text-primary me-2"></i> Edit Role: {{ $role->name }}</h4>
        <p class="text-muted font-size-13 mb-0">Update role name and permission assignments.</p>
    </div>
    <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fff1f2 !important; border-left: 4px solid #f43f5e !important; color: #9f1239 !important; border-radius: 8px !important;">
        <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('subscriber.hris.roles.update', $role) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-2 px-3">
                    <h6 class="fw-bold mb-0"><i class="bx bx-info-circle text-primary me-2"></i> Role Info</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold font-size-13">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="text-end pt-2 border-top">
                        <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-light btn-sm me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bx bx-check-shield text-primary me-2"></i> Assign Permissions</h6>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">Deselect All</button>
                    </div>
                </div>
                <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                    @foreach($permissions as $group => $perms)
                        <div class="mb-3">
                            <div class="fw-bold text-muted font-size-11 text-uppercase mb-2" style="letter-spacing:0.04em;">
                                <i class="bx bx-folder me-1"></i> {{ $group ?? 'General' }}
                                <span class="badge bg-light text-muted ms-1 font-size-10">{{ $perms->count() }}</span>
                            </div>
                            <div class="row g-1">
                                @foreach($perms as $perm)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check form-check-sm">
                                            <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}" {{ in_array($perm->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                            <label class="form-check-label font-size-11" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
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
