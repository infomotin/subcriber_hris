@extends('layouts.subscriber')

@section('title', 'System Theme')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-palette text-primary me-1.5 align-middle font-size-26"></i>System Theme
        </h4>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:14px;max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.setup.theme.update') }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Primary Color</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color" name="primary_color" value="{{ $config['primary_color'] ?? '#5f5af6' }}">
                        <span class="font-size-12 text-muted">{{ $config['primary_color'] ?? '#5f5af6' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Sidebar Style</label>
                    <select class="form-select" name="sidebar_style">
                        <option value="dark" {{ ($config['sidebar_style'] ?? 'dark') === 'dark' ? 'selected' : '' }}>Dark Sidebar</option>
                        <option value="light" {{ ($config['sidebar_style'] ?? '') === 'light' ? 'selected' : '' }}>Light Sidebar</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Font Family</label>
                    <select class="form-select" name="font_family">
                        <option value="Poppins" {{ ($config['font_family'] ?? 'Poppins') === 'Poppins' ? 'selected' : '' }}>Poppins</option>
                        <option value="Inter" {{ ($config['font_family'] ?? '') === 'Inter' ? 'selected' : '' }}>Inter</option>
                        <option value="Roboto" {{ ($config['font_family'] ?? '') === 'Roboto' ? 'selected' : '' }}>Roboto</option>
                        <option value="Open Sans" {{ ($config['font_family'] ?? '') === 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 p-3 rounded" style="background:#f8fafc;">
                <label class="form-label fw-semibold text-slate-700 font-size-12 mb-2">Preview</label>
                <div class="d-flex gap-3 align-items-center">
                    <div id="previewBox" style="width:50px;height:50px;border-radius:12px;background:{{ $config['primary_color'] ?? '#5f5af6' }};"></div>
                    <div>
                        <div class="fw-bold" id="previewText" style="font-family:'{{ $config['font_family'] ?? 'Poppins' }}',sans-serif;">Sample Heading</div>
                        <div class="font-size-12 text-muted">This is how text will look</div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Theme</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('input[name="primary_color"]').addEventListener('input', function() {
    document.getElementById('previewBox').style.background = this.value;
});
</script>
@endpush
@endsection
