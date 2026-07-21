@extends('layouts.system_admin')

@section('title', 'Website Content Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-globe text-warning me-2 font-size-22"></i> Website Landing Page Content Manager</h4>
        <p class="text-muted font-size-13 mb-0">Manage every section and dynamic text content displayed on the public landing page (http://amds.test/).</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
        <i class="bx bx-show me-1"></i> Preview Live Website
    </a>
</div>

<div class="card border-0 shadow-sm max-w-900">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-slider-alt text-primary me-2"></i> Dynamic Landing Page Settings</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.system.website.update') }}" method="POST">
            @csrf

            <h6 class="fw-bold text-primary mb-3"><i class="bx bx-text me-1"></i> Hero Banner Header Section</h6>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark font-size-13">Hero Main Title</label>
                <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $setting->hero_title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark font-size-13">Hero Subtitle Paragraph</label>
                <textarea name="hero_subtitle" class="form-control" rows="3" required>{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark font-size-13">Hero Call-To-Action Button Text</label>
                <input type="text" name="hero_cta_text" class="form-control" value="{{ old('hero_cta_text', $setting->hero_cta_text) }}" required>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold text-primary mb-3"><i class="bx bx-phone me-1"></i> Contact & Support Details</h6>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark font-size-13">Support Email</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $setting->contact_email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark font-size-13">Support Phone Number</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $setting->contact_phone) }}">
                </div>
            </div>

            <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                <i class="bx bx-save me-1"></i> Update Landing Page Content
            </button>
        </form>
    </div>
</div>
@endsection
