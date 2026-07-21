@extends('layouts.app')

@section('title', 'Biometric Users Management')

@section('content')
<div class="page-title-box">
    <h4>Biometric User Roster</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm">
            <i class="bx bx-plus me-1"></i> Add New User
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-2">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by User PIN, Name or Card Number...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search-alt me-1"></i> Search Users</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>PIN / ID</th>
                        <th>User Name</th>
                        <th>Card Number</th>
                        <th>Privilege</th>
                        <th>Associated Device</th>
                        <th>Sync Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $user->pin }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? $user->pin) }}&background=random" class="rounded-circle me-2" width="28" height="28" alt="">
                                    <span>{{ $user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td><code>{{ $user->card_number ?? '-' }}</code></td>
                            <td>
                                <span class="badge {{ $user->privilege == 14 ? 'bg-soft-danger text-danger' : 'bg-soft-info text-info' }}">
                                    {{ $user->privilege_label }}
                                </span>
                            </td>
                            <td>{{ $user->device->name ?? $user->device->serial_number ?? 'Global' }}</td>
                            <td>
                                <span class="badge {{ $user->is_synced ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                    {{ $user->is_synced ? 'Synced' : 'Pending' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light"><i class="bx bx-edit text-info"></i></a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user profile?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light"><i class="bx bx-trash text-danger"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bx bx-user-x font-size-36 text-secondary d-block mb-2"></i>
                                No biometric users registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
