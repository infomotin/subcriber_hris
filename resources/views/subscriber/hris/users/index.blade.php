@extends('layouts.subscriber')

@section('title', 'Users')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-group text-primary me-1.5 align-middle font-size-26"></i>User Management
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.users.create') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-plus me-1"></i> New User
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." style="max-width:350px;">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4"><i class="bx bx-search me-0.5"></i> Search</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=5f5af6&color=fff&size=32" class="rounded-circle" width="32" height="32">
                                <span class="fw-semibold text-slate-800 font-size-13">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="font-size-12">{{ $u->email }}</td>
                        <td>
                            @forelse($u->roles as $role)
                                <span class="badge bg-soft-primary text-primary px-2 py-1 font-size-10">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted font-size-11">No role</span>
                            @endforelse
                        </td>
                        <td class="font-size-12">{{ $u->created_at->format('d M, Y') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('subscriber.hris.users.edit', $u) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11">
                                    <i class="bx bx-edit me-0.5"></i> Edit
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('subscriber.hris.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11"><i class="bx bx-trash me-0.5"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bx bx-group font-size-40 d-block mb-2"></i> No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}</small>
        <div>{{ $users->links() }}</div>
    </div>
@endif
@endsection
