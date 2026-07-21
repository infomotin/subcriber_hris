@extends('layouts.app')

@section('title', 'Register New Device')

@section('content')
<div class="page-title-box">
    <h4>Register Biometric Device</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary btn-sm rounded-pill">Cancel</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.devices.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Serial Number (SN) <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" required placeholder="e.g. CKT12345678">
                    <small class="text-muted">Must match the Serial Number configured in ZKTeco device ADMS menu.</small>
                    @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Device Name / Location</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Main Entrance Gate">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Static IP Address (Optional)</label>
                    <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address') }}" placeholder="192.168.1.100">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Communication Port</label>
                    <input type="number" name="port" class="form-control" value="{{ old('port', 4370) }}">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Register Device</button>
            </div>
        </form>
    </div>
</div>
@endsection
