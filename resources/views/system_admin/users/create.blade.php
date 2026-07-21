@extends('layouts.system_admin')

@section('title', 'Create SaaS Application User')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.system.users.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bx bx-arrow-back me-1"></i> Back to User List</a>
    <h4 class="fw-bold mb-1"><i class="bx bx-user-plus text-warning me-2"></i> Create SaaS User Account</h4>
</div>

<div class="card border-0 shadow-sm max-w-700">
    <div class="card-body p-4">
        <form action="{{ route('admin.system.users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">User Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Tanvir Ahmed" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="tanvir@company.com" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Assign SaaS Role</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Assign Subscriber Organization</label>
                    <select name="tenant_id" class="form-select">
                        <option value="">-- Global / System Admin --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                <i class="bx bx-check-circle me-1"></i> Create SaaS User
            </button>
        </form>
    </div>
</div>
@endsection
