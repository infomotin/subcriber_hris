@extends('layouts.app')

@section('title', 'Edit User - ' . $user->pin)

@section('content')
<div class="page-title-box">
    <h4>Edit User PIN: {{ $user->pin }}</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm rounded-pill">Cancel</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">User PIN / ID</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->pin }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Password / Keypad PIN</label>
                    <input type="password" name="password" class="form-control" value="{{ old('password', $user->password) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Card Number</label>
                    <input type="text" name="card_number" class="form-control" value="{{ old('card_number', $user->card_number) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Role Privilege</label>
                    <select name="privilege" class="form-select">
                        <option value="0" {{ $user->privilege == 0 ? 'selected' : '' }}>Normal User</option>
                        <option value="14" {{ $user->privilege == 14 ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign Device</label>
                    <select name="device_id" class="form-select">
                        <option value="">All Devices (Global)</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}" {{ $user->device_id == $d->id ? 'selected' : '' }}>{{ $d->name ?? $d->serial_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="push_to_device" value="1" id="push_to_device" checked>
                        <label class="form-check-label fw-bold" for="push_to_device">Queue Command to Sync to Physical Terminal</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update User Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
