@extends('layouts.subscriber')

@section('title', 'Subscriber Information')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-building text-primary me-1.5 align-middle font-size-26"></i>Subscriber Information
        </h4>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4" role="alert">
        <i class="bx bx-error-circle me-1 align-middle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('subscriber.hris.setup.subscriber.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- Company Details --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-info-circle text-primary me-1.5"></i> Company Details
            </h6>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Company Name</label>
                    <input type="text" class="form-control" name="company_name" value="{{ $config['company_name'] ?? $tenant->name ?? '' }}" placeholder="Your Company Name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Company Email</label>
                    <input type="email" class="form-control" name="company_email" value="{{ $config['company_email'] ?? '' }}" placeholder="info@company.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Company Phone</label>
                    <input type="text" class="form-control" name="company_phone" value="{{ $config['company_phone'] ?? '' }}" placeholder="+880 1XXXXXXXXX">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Address</label>
                    <textarea class="form-control" name="company_address" rows="2" placeholder="Full company address">{{ $config['company_address'] ?? '' }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Website</label>
                    <input type="url" class="form-control" name="company_website" value="{{ $config['company_website'] ?? '' }}" placeholder="https://company.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Registration No.</label>
                    <input type="text" class="form-control" name="registration_no" value="{{ $config['registration_no'] ?? '' }}" placeholder="Company registration number">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Tax ID / TIN</label>
                    <input type="text" class="form-control" name="tax_id" value="{{ $config['tax_id'] ?? '' }}" placeholder="Tax identification number">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Company Logo</label>
                    <input type="file" class="form-control" name="company_logo" accept=".jpg,.jpeg,.png">
                    <div class="form-text">JPG or PNG, max 2MB</div>
                    @if(!empty($config['company_logo']))
                        <div class="mt-2">
                            <img src="{{ Storage::disk('public')->url($config['company_logo']) }}" alt="Current Logo" style="max-height:50px;border-radius:6px;border:1px solid #e2e8f0;">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Short Description</label>
                    <textarea class="form-control" name="short_description" rows="3" placeholder="Brief description about your organization (appears on reports)">{{ $config['short_description'] ?? '' }}</textarea>
                    <div class="form-text">Max 500 characters</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Header & Footer --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-slate-800 mb-1" style="font-family:'Poppins',sans-serif;">
                <i class="bx bx-file text-primary me-1.5"></i> Report Header & Footer Design
            </h6>
            <p class="text-muted font-size-12 mb-3">These fields appear on all generated reports, invoices, and PDF documents.</p>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Report Header Text</label>
                    <textarea class="form-control" name="report_header_text" rows="3" placeholder="e.g. ABC Corporation Ltd. | Authorized Dealer of XYZ">{{ $config['report_header_text'] ?? '' }}</textarea>
                    <div class="form-text">Appears at the top of every report/document</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Report Footer Text</label>
                    <textarea class="form-control" name="report_footer_text" rows="3" placeholder="e.g. This is a system-generated document. For queries contact hr@company.com">{{ $config['report_footer_text'] ?? '' }}</textarea>
                    <div class="form-text">Appears at the bottom of every report/document</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-slate-700">Footer Notes / Legal Text</label>
                    <textarea class="form-control" name="report_footer_notes" rows="3" placeholder="e.g. This document is digitally generated and valid without signature.">{{ $config['report_footer_notes'] ?? '' }}</textarea>
                    <div class="form-text">Optional legal or compliance notes for reports</div>
                </div>
            </div>

            {{-- Live Preview --}}
            <div class="mt-4 p-3 rounded" style="background:#f8fafc;border:1px dashed #cbd5e1;">
                <label class="form-label fw-semibold text-slate-700 font-size-12 mb-2"><i class="bx bx-eye me-1"></i> Live Preview</label>
                <div class="bg-white p-3 rounded" style="border:1px solid #e2e8f0;" id="reportPreview">
                    <div class="text-center mb-2" style="border-bottom:2px solid #5f5af6;padding-bottom:10px;">
                        @if(!empty($config['company_logo']))
                            <img src="{{ Storage::disk('public')->url($config['company_logo']) }}" style="max-height:40px;margin-bottom:5px;">
                        @endif
                        <div class="fw-bold text-dark font-size-14" id="previewHeader">{{ $config['report_header_text'] ?? 'Report header will appear here...' }}</div>
                        <div class="font-size-11 text-muted">{{ $config['company_name'] ?? $tenant->name }}</div>
                    </div>
                    <div class="text-center text-muted font-size-11 my-2">[ Report Content Area ]</div>
                    <div class="text-center mt-2" style="border-top:1px solid #e2e8f0;padding-top:10px;">
                        <div class="font-size-11 text-muted" id="previewFooter">{{ $config['report_footer_text'] ?? 'Report footer will appear here...' }}</div>
                        <div class="font-size-10 text-muted fst-italic" id="previewNotes">{{ $config['report_footer_notes'] ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary rounded-pill px-5" style="height:44px;">
            <i class="bx bx-save me-1.5 align-middle font-size-18"></i> Save All Information
        </button>
    </div>
</form>

@push('scripts')
<script>
document.querySelectorAll('input, textarea').forEach(el => {
    el.addEventListener('input', function() {
        const name = this.getAttribute('name');
        if (name === 'report_header_text') document.getElementById('previewHeader').textContent = this.value || 'Report header will appear here...';
        if (name === 'report_footer_text') document.getElementById('previewFooter').textContent = this.value || 'Report footer will appear here...';
        if (name === 'report_footer_notes') document.getElementById('previewNotes').textContent = this.value;
    });
});
</script>
@endpush
@endsection
