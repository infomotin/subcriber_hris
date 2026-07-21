@extends('layouts.system_admin')

@section('title', 'Role & Permissions Matrix')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-key text-warning me-2 font-size-22"></i> Role & Permissions Matrix</h4>
        <p class="text-muted font-size-13 mb-0">Create application roles and assign fine-grained menu and CRUD permissions per section.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Create Role Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-plus-circle text-primary me-2"></i> Create New Role</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.system.roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Audit Manager" required>
                    </div>

                    <h6 class="fw-bold text-dark font-size-13 mb-2">Initial Permissions:</h6>
                    <div class="bg-light p-3 rounded border mb-4" style="max-height: 220px; overflow-y: auto;">
                        @foreach($allPermissions as $perm)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_create_{{ $perm->id }}">
                                <label class="form-check-label font-size-12 text-dark" for="perm_create_{{ $perm->id }}">
                                    {{ $perm->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">
                        <i class="bx bx-check-circle me-1"></i> Create Role
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Roles & Permissions Accordion Matrix -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-list-check text-primary me-2"></i> Assigned Roles & Permission Matrix</h5>
            </div>
            <div class="card-body p-4">
                <div class="accordion" id="rolesAccordion">
                    @foreach($roles as $role)
                        <div class="accordion-item border mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header" id="headingRole{{ $role->id }}">
                                <button class="accordion-button collapsed bg-light font-size-15 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRole{{ $role->id }}">
                                    <i class="bx bx-shield-quarter me-2 text-warning font-size-20"></i>
                                    {{ $role->name }}
                                    <span class="badge bg-dark ms-auto me-3 font-size-11">{{ $role->permissions->count() }} Permissions Assigned</span>
                                </button>
                            </h2>
                            <div id="collapseRole{{ $role->id }}" class="accordion-collapse collapse" data-bs-parent="#rolesAccordion">
                                <div class="accordion-body bg-white p-4">
                                    <form action="{{ route('admin.system.roles.update', $role) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            @foreach($defaultPermissions as $category => $perms)
                                                <div class="col-md-6">
                                                    <div class="border p-3 rounded bg-light">
                                                        <h6 class="fw-bold text-uppercase text-primary font-size-11 mb-2">
                                                            <i class="bx bx-folder me-1"></i> {{ str_replace('_', ' ', $category) }}
                                                        </h6>
                                                        @foreach($perms as $pName)
                                                            <div class="form-check font-size-12">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $pName }}" id="role_{{ $role->id }}_{{ $pName }}" {{ $role->hasPermissionTo($pName) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-dark" for="role_{{ $role->id }}_{{ $pName }}">
                                                                    {{ $pName }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 mt-3">
                                            <i class="bx bx-save me-1"></i> Update {{ $role->name }} Permissions
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
