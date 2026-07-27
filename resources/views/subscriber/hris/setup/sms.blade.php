@extends('layouts.subscriber')

@section('title', 'SMS Gateway')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-message text-primary me-1.5 align-middle font-size-26"></i>SMS Gateway Configuration
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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('subscriber.hris.setup.sms.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">SMS Provider</label>
                            <select class="form-select" name="sms_provider">
                                <option value="">-- Select Provider --</option>
                                @foreach(['Twilio', 'Nexmo', 'MSG91', 'Banglalink', 'Grameenphone', 'Robi', 'Other'] as $provider)
                                    <option value="{{ $provider }}" {{ ($config['sms_provider'] ?? '') === $provider ? 'selected' : '' }}>{{ $provider }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">Sender ID</label>
                            <input type="text" class="form-control" name="sms_sender_id" value="{{ $config['sms_sender_id'] ?? '' }}" placeholder="e.g. ADMS">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">API Key</label>
                            <input type="text" class="form-control" name="sms_api_key" value="{{ $config['sms_api_key'] ?? '' }}" placeholder="Your API key">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">API Secret</label>
                            <input type="password" class="form-control" name="sms_api_secret" value="{{ $config['sms_api_secret'] ?? '' }}" placeholder="Your API secret">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">From Number</label>
                            <input type="text" class="form-control" name="sms_from_number" value="{{ $config['sms_from_number'] ?? '' }}" placeholder="+8801XXXXXXXXX">
                        </div>
                    </div>
                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Save SMS Config</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Test SMS Panel --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-message text-success me-1.5"></i> Test SMS
                </h6>
                <p class="text-muted font-size-12 mb-3">Send a test SMS to verify your gateway configuration.</p>
                <form method="POST" action="{{ route('subscriber.hris.setup.sms.test') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Test Phone Number</label>
                        <input type="text" class="form-control" name="test_number" value="{{ $config['sms_from_number'] ?? '' }}" required placeholder="+8801XXXXXXXXX">
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill" style="height:42px;">
                        <i class="bx bx-message me-1"></i> Send Test SMS
                    </button>
                </form>

                <div class="mt-3 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="font-size-11 text-muted">
                        <i class="bx bx-info-circle text-success me-1"></i>
                        Provider: <strong>{{ $config['sms_provider'] ?? 'Not configured' }}</strong><br>
                        Sender: <strong>{{ $config['sms_sender_id'] ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
