@extends('layouts.system_admin')

@section('title', 'Gateway Configurations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-cog text-warning me-2 font-size-22"></i> Gateway Configuration (SMS, Mail & SSLCommerz)</h4>
        <p class="text-muted font-size-13 mb-0">Configure SMS Gateway, SMTP Mail Server credentials, and SSLCommerz Payment Gateway settings.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- SMS Gateway Configuration Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-message-square-dots text-primary me-2"></i> SMS Gateway Configuration</h5>
                <span class="badge bg-success">Active</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.system.gateways.update_sms') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">SMS Gateway Provider</label>
                        <select name="sms_provider" class="form-select border-secondary">
                            <option value="greenweb" {{ $config->sms_provider === 'greenweb' ? 'selected' : '' }}>Greenweb SMS Bangladesh</option>
                            <option value="twilio" {{ $config->sms_provider === 'twilio' ? 'selected' : '' }}>Twilio International</option>
                            <option value="bulksms" {{ $config->sms_provider === 'bulksms' ? 'selected' : '' }}>BulkSMS BD</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">API Secret Key</label>
                        <input type="text" name="sms_api_key" class="form-control" value="{{ old('sms_api_key', $config->sms_api_key) }}" placeholder="GW_SECRET_KEY_123">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Sender ID / Masking</label>
                        <input type="text" name="sms_sender_id" class="form-control" value="{{ old('sms_sender_id', $config->sms_sender_id) }}" placeholder="ZKTecoSaaS">
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold px-3 btn-sm">
                        <i class="bx bx-save me-1"></i> Save SMS Gateway Settings
                    </button>
                </form>

                <hr>
                <!-- Test SMS Form -->
                <form action="{{ route('admin.system.gateways.test_sms') }}" method="POST">
                    @csrf
                    <label class="form-label fw-bold text-dark font-size-13">Dispatch Test SMS</label>
                    <div class="input-group">
                        <input type="text" name="phone_number" class="form-control" placeholder="+8801700000000" required>
                        <button type="submit" class="btn btn-outline-primary fw-bold"><i class="bx bx-paper-plane me-1"></i> Send Test SMS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SMTP Mail Server Configuration Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-envelope text-primary me-2"></i> SMTP Mail Server Configuration</h5>
                <span class="badge bg-success">Active</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.system.gateways.update_mail') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-bold text-dark font-size-13">SMTP Host</label>
                            <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $config->mail_host) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold text-dark font-size-13">Port</label>
                            <input type="number" name="mail_port" class="form-control" value="{{ old('mail_port', $config->mail_port) }}" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark font-size-13">SMTP Username</label>
                            <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $config->mail_username) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark font-size-13">SMTP Password</label>
                            <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $config->mail_password) }}">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark font-size-13">Encryption</label>
                            <select name="mail_encryption" class="form-select border-secondary">
                                <option value="tls" {{ $config->mail_encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $config->mail_encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark font-size-13">From Address</label>
                            <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $config->mail_from_address) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">From Sender Name</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $config->mail_from_name) }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold px-3 btn-sm">
                        <i class="bx bx-save me-1"></i> Save SMTP Settings
                    </button>
                </form>

                <hr>
                <!-- Test Mail Form -->
                <form action="{{ route('admin.system.gateways.test_mail') }}" method="POST">
                    @csrf
                    <label class="form-label fw-bold text-dark font-size-13">Dispatch Test Email</label>
                    <div class="input-group">
                        <input type="email" name="test_email" class="form-control" placeholder="user@domain.com" required>
                        <button type="submit" class="btn btn-outline-primary fw-bold"><i class="bx bx-paper-plane me-1"></i> Send Test Mail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SSLCommerz Gateway Configuration Card -->
<div class="card border-0 shadow-sm max-w-700">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-credit-card text-primary me-2"></i> SSLCommerz Merchant Payment Gateway</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.system.gateways.update_sslcommerz') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold text-dark font-size-13">SSLCommerz Store ID</label>
                <input type="text" name="sslcommerz_store_id" class="form-control" value="{{ old('sslcommerz_store_id', $config->sslcommerz_store_id) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark font-size-13">SSLCommerz Store Password</label>
                <input type="password" name="sslcommerz_store_passwd" class="form-control" value="{{ old('sslcommerz_store_passwd', $config->sslcommerz_store_passwd) }}" required>
            </div>

            <div class="mb-4 form-check form-switch bg-light p-3 rounded border">
                <input class="form-check-input ms-0 me-3" type="checkbox" name="sslcommerz_is_sandbox" id="switchSandbox" value="1" {{ $config->sslcommerz_is_sandbox ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark cursor-pointer" for="switchSandbox">
                    Enable SSLCommerz Sandbox / Testing Mode
                </label>
            </div>

            <button type="submit" class="btn btn-success fw-bold px-4">
                <i class="bx bx-save me-1"></i> Save SSLCommerz Configuration
            </button>
        </form>
    </div>
</div>
@endsection
