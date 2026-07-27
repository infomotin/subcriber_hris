@extends('layouts.subscriber')

@section('title', 'Dedicated ZKTeco Machine ADMS Endpoint')

@section('content')
<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Dedicated ZKTeco Machine ADMS Endpoint</h4>
    </div>
</div>

<div class="card border-0" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(95, 90, 246, 0.1) !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="fw-bold text-slate-800 mb-2" style="font-family: 'Poppins', sans-serif; font-size: 0.95rem;">
                    <i class="bx bx-broadcast me-2 text-primary font-size-20 align-middle"></i> Dedicated ZKTeco Machine ADMS Endpoint
                </h6>
                <p class="mb-3 text-slate-600 font-size-13">Configure this unique URL on your ZKTeco biometric machine's <strong>COMM. &gt; ADMS Cloud Server</strong> settings:</p>

                <div class="input-group" style="max-width: 580px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                    <span class="input-group-text bg-white border-end-0 text-slate-400 font-size-13"><i class="bx bx-link"></i></span>
                    <input type="text" class="form-control font-size-13 bg-white border-start-0 border-end-0 fw-semibold text-slate-700 py-2" id="adms-url" value="http://amds.test/iclock/{{ $tenant->tenant_token }}/cdata" readonly>
                    <button class="btn btn-primary px-4 fw-bold font-size-13" type="button" id="copy-btn" onclick="copyAdmsUrl()">
                        <i class="bx bx-copy me-1" id="copy-icon"></i> <span id="copy-text">Copy URL</span>
                    </button>
                </div>
            </div>
            <div class="d-flex flex-column align-items-start align-items-md-end gap-1">
                <span class="badge bg-soft-primary text-primary font-size-11 px-3 py-2 rounded-pill">Active Tenant Token</span>
                <code class="font-size-13 fw-bold text-primary bg-white border px-3 py-1.5 rounded-pill mt-1" style="border-color: rgba(95, 90, 246, 0.15) !important;">{{ $tenant->tenant_token }}</code>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-slate-800 mb-3"><i class="bx bx-info-circle text-primary me-1"></i> How to Configure</h6>
        <ol class="font-size-13 text-slate-600 mb-0" style="line-height: 2;">
            <li>On your ZKTeco biometric device, press <strong>MENU</strong> and log in as admin.</li>
            <li>Navigate to <strong>COMM. &gt; ADMS Cloud Server</strong> or <strong>Network &gt; ADMS</strong>.</li>
            <li>Set the <strong>Server IP / URL</strong> to the endpoint shown above.</li>
            <li>Set the <strong>Port</strong> to <code>80</code> (or <code>443</code> for HTTPS).</li>
            <li>Ensure the device has internet connectivity and save the settings.</li>
            <li>The device will automatically start pushing attendance logs to this endpoint.</li>
        </ol>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyAdmsUrl() {
        const copyText = document.getElementById("adms-url");
        navigator.clipboard.writeText(copyText.value);

        const copyBtn = document.getElementById("copy-btn");
        const copyIcon = document.getElementById("copy-icon");
        const copyTextSpan = document.getElementById("copy-text");

        copyBtn.classList.remove("btn-primary");
        copyBtn.classList.add("btn-success");
        copyIcon.className = "bx bx-check me-1";
        copyTextSpan.textContent = "Copied URL";

        setTimeout(() => {
            copyBtn.classList.remove("btn-success");
            copyBtn.classList.add("btn-primary");
            copyIcon.className = "bx bx-copy me-1";
            copyTextSpan.textContent = "Copy URL";
        }, 2000);
    }
</script>
@endpush