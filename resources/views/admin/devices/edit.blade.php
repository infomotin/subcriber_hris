@extends('layouts.app')

@section('title', 'Edit Device Settings')

@section('content')
<div class="page-title-box">
    <h4>Edit Device: {{ $device->serial_number }}</h4>
    <div class="page-title-right">
        <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-secondary btn-sm rounded-pill">Cancel</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.devices.update', $device) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Device Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $device->name) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Serial Number (SN)</label>
                    <input type="text" class="form-control bg-light" value="{{ $device->serial_number }}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Heartbeat Delay (Seconds)</label>
                    <input type="number" name="delay" class="form-control" value="{{ old('delay', $device->delay) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Error Delay (Seconds)</label>
                    <input type="number" name="error_delay" class="form-control" value="{{ old('error_delay', $device->error_delay) }}">
                </div>
            </div>

            <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="realtime" value="1" id="realtime" {{ $device->realtime ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="realtime">Enable Realtime Push Mode</label>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
