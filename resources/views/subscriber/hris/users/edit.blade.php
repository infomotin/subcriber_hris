@extends('layouts.subscriber')

@section('title', 'Edit User')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-edit text-primary me-1.5 align-middle font-size-26"></i>Edit User: {{ $user->name }}
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;max-width:650px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">New Password <small class="text-muted">(leave blank to keep)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Assign Role</label>
                    <select class="form-select" name="role">
                        <option value="">-- No Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('subscriber.hris.users.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
