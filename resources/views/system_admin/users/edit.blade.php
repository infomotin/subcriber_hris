@extends('layouts.system_admin')

@section('title', 'Edit SaaS Application User')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.system.users.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bx bx-arrow-back me-1"></i> Back to User List</a>
    <h4 class="fw-bold mb-1"><i class="bx bx-edit text-warning me-2"></i> Edit SaaS User: {{ $user->name }}</h4>
</div>

<div class="card border-0 shadow-sm max-w-700">
    <div class="card-body p-4">
        <form action="{{ route('admin.system.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">User Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Reset Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Assigned Role</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Subscriber Organization</label>
                    <select name="tenant_id" class="form-select">
                        <option value="">-- Global / System Admin --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ $user->tenant_id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                <i class="bx bx-save me-1"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
