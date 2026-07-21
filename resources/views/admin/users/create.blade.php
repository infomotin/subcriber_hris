@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="page-title-box">
    <h4>Add Biometric User</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm rounded-pill">Cancel</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">User PIN / ID <span class="text-danger">*</span></label>
                    <input type="text" name="pin" class="form-control @error('pin') is-invalid @enderror" value="{{ old('pin') }}" required placeholder="e.g. 1001">
                    @error('pin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Password / Keypad PIN</label>
                    <input type="password" name="password" class="form-control" placeholder="1234">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Card Number</label>
                    <input type="text" name="card_number" class="form-control" placeholder="e.g. 00084321">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Role Privilege</label>
                    <select name="privilege" class="form-select">
                        <option value="0">Normal User</option>
                        <option value="14">Administrator</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign to Specific Device</label>
                    <select name="device_id" class="form-select">
                        <option value="">All Devices (Global)</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}">{{ $d->name ?? $d->serial_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="push_to_device" value="1" id="push_to_device" checked>
                        <label class="form-check-label fw-bold" for="push_to_device">Queue Command to Sync to Physical Terminal Immediately</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save User Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
