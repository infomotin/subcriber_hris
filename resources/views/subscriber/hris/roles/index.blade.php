@extends('layouts.subscriber')

@section('title', 'Roles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-shield text-primary me-2"></i> Roles</h4>
        <p class="text-muted font-size-13 mb-0">Manage tenant roles and permission assignments.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-list-check me-1"></i> Permissions
        </a>
        <a href="{{ route('subscriber.hris.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> New Role
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fff1f2 !important; border-left: 4px solid #f43f5e !important; color: #9f1239 !important; border-radius: 8px !important;">
        <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #ecfdf5 !important; border-left: 4px solid #10b981 !important; color: #065f46 !important; border-radius: 8px !important;">
        <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2"><i class="bx bx-shield"></i></div>
                    <div><span class="text-muted font-size-11">Total Roles</span><br><span class="fw-bold font-size-14">{{ number_format($roles->total()) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2"><i class="bx bx-lock"></i></div>
                    <div><span class="text-muted font-size-11">System Roles</span><br><span class="fw-bold font-size-14">{{ $roles->getCollection()->filter(fn($r) => $r->isSystemRole())->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2"><i class="bx bx-user"></i></div>
                    <div><span class="text-muted font-size-11">Tenant Roles</span><br><span class="fw-bold font-size-14">{{ $roles->getCollection()->filter(fn($r) => $r->isTenantRole())->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2"><i class="bx bx-check-shield"></i></div>
                    <div><span class="text-muted font-size-11">Permissions</span><br><span class="fw-bold font-size-14">{{ \Spatie\Permission\Models\Permission::count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <span class="text-muted font-size-11">Showing {{ $roles->firstItem() ?? 0 }}-{{ $roles->lastItem() ?? 0 }} of {{ number_format($roles->total()) }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:35px">#</th>
                        <th>Role Name</th>
                        <th>Type</th>
                        <th>Permissions</th>
                        <th style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="text-muted">{{ $roles->firstItem() + $loop->index }}</td>
                            <td><span class="fw-semibold text-dark">{{ $role->name }}</span></td>
                            <td>
                                @if($role->isSystemRole())
                                    <span class="badge bg-secondary"><i class="bx bx-lock me-1"></i>System</span>
                                @else
                                    <span class="badge bg-success"><i class="bx bx-building me-1"></i>Tenant</span>
                                @endif
                            </td>
                            <td>
                                @forelse($role->permissions->take(3) as $perm)
                                    <span class="badge bg-light text-dark px-2 py-1 font-size-10">{{ $perm->name }}</span>
                                @empty
                                    <span class="text-muted font-size-11">No permissions</span>
                                @endforelse
                                @if($role->permissions->count() > 3)
                                    <span class="badge bg-light text-muted px-2 py-1 font-size-10">+{{ $role->permissions->count() - 3 }}</span>
                                @endif
                            </td>
                            <td>
                                @if($role->isSystemRole())
                                    <span class="text-muted font-size-11"><i class="bx bx-lock me-1"></i>Protected</span>
                                @else
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('subscriber.hris.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('subscriber.hris.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="bx bx-shield me-1"></i> No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($roles->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
