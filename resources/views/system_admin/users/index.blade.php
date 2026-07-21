@extends('layouts.system_admin')

@section('title', 'SaaS User Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-user-voice text-warning me-2 font-size-22"></i> SaaS User Manager</h4>
        <p class="text-muted font-size-13 mb-0">Manage all SaaS application administrative, subscriber, and demo accounts (Excludes physical device biometric users).</p>
    </div>
    <a href="{{ route('admin.system.users.create') }}" class="btn btn-warning btn-sm fw-bold px-3 shadow-sm text-dark">
        <i class="bx bx-plus-circle me-1"></i> Create SaaS User
    </a>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.system.users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by user name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Filter by Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 fw-bold"><i class="bx bx-filter-alt me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>User Name & Email</th>
                        <th>Assigned SaaS Role</th>
                        <th>Assigned Tenant</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>#{{ $u->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=f1b44c&color=000" class="rounded-circle" width="36" height="36" alt="Avatar">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $u->name }}</h6>
                                        <small class="text-muted">{{ $u->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($u->roles as $role)
                                    <span class="badge {{ $role->name === 'System Admin' ? 'bg-danger' : ($role->name === 'Business Admin' ? 'bg-warning text-dark' : 'bg-primary') }} font-size-12">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($u->tenant)
                                    <span class="badge bg-secondary"><i class="bx bx-building me-1"></i> {{ $u->tenant->name }}</span>
                                @else
                                    <span class="text-muted font-size-12">Global / None</span>
                                @endif
                            </td>
                            <td>{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.system.users.edit', $u) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bx bx-edit"></i> Edit
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.system.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this SaaS User?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No SaaS Users found matching filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
