@extends('layouts.subscriber')

@section('title', 'Roles')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-shield-quarter text-primary me-1.5 align-middle font-size-26"></i>Roles
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.permissions.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-list-check me-1"></i> View Permissions
        </a>
        <a href="{{ route('subscriber.hris.roles.create') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-plus me-1"></i> New Role
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="ps-4">{{ $role->id }}</td>
                        <td class="fw-semibold text-slate-800">{{ $role->name }}</td>
                        <td>
                            @forelse($role->permissions->take(5) as $perm)
                                <span class="badge bg-soft-secondary text-secondary px-2 py-1 font-size-10">{{ $perm->name }}</span>
                            @empty
                                <span class="text-muted font-size-11">No permissions</span>
                            @endforelse
                            @if($role->permissions->count() > 5)
                                <span class="badge bg-light text-muted px-2 py-1 font-size-10">+{{ $role->permissions->count() - 5 }} more</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('subscriber.hris.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11">
                                    <i class="bx bx-edit me-0.5"></i> Edit
                                </a>
                                <form action="{{ route('subscriber.hris.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11"><i class="bx bx-trash me-0.5"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bx bx-shield-quarter font-size-40 d-block mb-2"></i> No roles created yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($roles->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $roles->firstItem() }}-{{ $roles->lastItem() }} of {{ $roles->total() }}</small>
        <div>{{ $roles->links() }}</div>
    </div>
@endif
@endsection
