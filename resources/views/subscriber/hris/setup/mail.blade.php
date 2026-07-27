@extends('layouts.subscriber')

@section('title', 'Mail Configuration')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-envelope text-primary me-1.5 align-middle font-size-26"></i>Mail Configuration
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
                <form method="POST" action="{{ route('subscriber.hris.setup.mail.update') }}">
                    @csrf @method('PUT')
                    <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                        <i class="bx bx-cog text-primary me-1.5"></i> SMTP Settings
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">Mail Driver</label>
                            <select class="form-select" name="mail_mailer">
                                @foreach(['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'resend', 'log', 'array'] as $driver)
                                    <option value="{{ $driver }}" {{ ($config['mail_mailer'] ?? 'smtp') === $driver ? 'selected' : '' }}>{{ ucfirst($driver) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">Encryption</label>
                            <select class="form-select" name="mail_encryption">
                                <option value="tls" {{ ($config['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($config['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-slate-700">SMTP Host</label>
                            <input type="text" class="form-control" name="mail_host" value="{{ $config['mail_host'] ?? 'sandbox.smtp.mailtrap.io' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-slate-700">Port</label>
                            <input type="number" class="form-control" name="mail_port" value="{{ $config['mail_port'] ?? '2525' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">Username</label>
                            <input type="text" class="form-control" name="mail_username" value="{{ $config['mail_username'] ?? '5222b220dcdef4' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">Password</label>
                            <input type="password" class="form-control" name="mail_password" value="{{ $config['mail_password'] ?? '0f62b8b368e1f9' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">From Address</label>
                            <input type="email" class="form-control" name="mail_from_address" value="{{ $config['mail_from_address'] ?? 'noreply@example.com' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-slate-700">From Name</label>
                            <input type="text" class="form-control" name="mail_from_name" value="{{ $config['mail_from_name'] ?? config('app.name') }}">
                        </div>
                    </div>
                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Save Mail Config</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Test Email Panel --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-send text-success me-1.5"></i> Test Email
                </h6>
                <p class="text-muted font-size-12 mb-3">Send a test email to verify your configuration works. Uses this tenant's saved SMTP settings.</p>
                <form method="POST" action="{{ route('subscriber.hris.setup.mail.test') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-700">Send Test To</label>
                        <input type="email" class="form-control" name="test_email" value="{{ auth()->user()->email }}" required placeholder="your@email.com">
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill" style="height:42px;">
                        <i class="bx bx-send me-1"></i> Send Test Email
                    </button>
                </form>

                <div class="mt-3 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="font-size-11 text-muted">
                        <i class="bx bx-info-circle text-success me-1"></i>
                        Current: <strong>{{ $config['mail_host'] ?? 'N/A' }}:{{ $config['mail_port'] ?? 'N/A' }}</strong><br>
                        From: <strong>{{ $config['mail_from_address'] ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
