@extends('layouts.subscriber')

@section('title', 'Dedicated ZKTeco Machine ADMS Endpoint')

@section('content')
<style>
    .protocol-tab { cursor: pointer; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.5rem; transition: all 0.2s; }
    .protocol-tab:hover { border-color: #a5b4fc; }
    .protocol-tab.active { border-color: #6366f1; background: #eef2ff; }
    .protocol-tab .icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
</style>

<div class="page-title-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">ZKTeco ADMS Endpoint Configuration</h4>
    </div>
    <span class="badge bg-primary font-size-12 px-3 py-2 rounded-pill" style="box-shadow: 0 2px 8px rgba(99,102,241,0.3);">
        <i class="bx bx-key me-1"></i> Token: <strong>{{ $tenant->tenant_token }}</strong>
    </span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="protocol-tab active" id="newProtocolTab" onclick="switchProtocol('new')">
            <div class="d-flex align-items-start gap-3">
                <div class="icon" style="background: #eef2ff; color: #6366f1;"><i class="bx bx-chip"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">New Protocol</h6>
                    <p class="text-muted font-size-12 mb-1">For modern ZKTeco firmware that supports domain-based ADMS URLs.</p>
                    <span class="badge bg-success font-size-10 rounded-pill px-2 py-1">Recommended</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="protocol-tab" id="oldProtocolTab" onclick="switchProtocol('old')">
            <div class="d-flex align-items-start gap-3">
                <div class="icon" style="background: #fffbeb; color: #f59e0b;"><i class="bx bx-chip"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Old / Legacy Protocol</h6>
                    <p class="text-muted font-size-12 mb-1">For older ZKTeco devices that only support IP address ADMS connections.</p>
                    <span class="badge bg-warning text-dark font-size-10 rounded-pill px-2 py-1">Legacy</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="newProtocolContent">
    <div class="card border-0" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(95, 90, 246, 0.1) !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="fw-bold text-slate-800 mb-2" style="font-family: 'Poppins', sans-serif; font-size: 0.95rem;">
                        <i class="bx bx-broadcast me-2 text-primary font-size-20 align-middle"></i> New Protocol &mdash; Domain-Based Endpoint
                    </h6>
                    <p class="mb-3 text-slate-600 font-size-13">Configure this unique URL on your ZKTeco biometric machine's <strong>COMM. &gt; ADMS Cloud Server</strong> settings:</p>

                    <div class="mb-2 p-2 rounded-3 d-inline-block" style="background:rgba(99,102,241,0.08);">
                        <span class="font-size-11 text-muted">Your Token: </span>
                        <strong class="font-size-13 text-primary">{{ $tenant->tenant_token }}</strong>
                    </div>

                    <div class="input-group mb-2" style="max-width: 620px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                        <span class="input-group-text bg-white border-end-0 text-slate-400 font-size-13"><i class="bx bx-link"></i></span>
                        <input type="text" class="form-control font-size-13 bg-white border-start-0 border-end-0 fw-semibold text-slate-700 py-2" id="adms-url" value="https://hr.nexogiant.com/iclock/{{ $tenant->tenant_token }}/cdata" readonly>
                        <button class="btn btn-primary px-4 fw-bold font-size-13" type="button" id="copy-btn" onclick="copyAdmsUrl()">
                            <i class="bx bx-copy me-1" id="copy-icon"></i> <span id="copy-text">Copy URL</span>
                        </button>
                    </div>

                    <div class="mt-2 d-flex align-items-center gap-3 font-size-12 text-slate-500">
                        <span class="badge bg-success font-size-10 rounded-pill px-2 py-1"><i class="bx bx-lock me-1"></i> HTTPS (Port 443) &mdash; Required</span>
                        <span class="text-muted">HTTP (Port 80) is not available — Cloudflare enforces HTTPS.</span>
                    </div>

                    <div class="mt-3 p-3 rounded-3" style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15);">
                        <span class="fw-bold font-size-12 text-primary"><i class="bx bx-server me-1"></i> Device Configuration Summary:</span>
                        <div class="mt-2 font-size-12 text-slate-600" style="line-height: 2;">
                            <div><strong>Server Address:</strong> <code>hr.nexogiant.com</code></div>
                            <div><strong>Port:</strong> <code>443</code></div>
                            <div><strong>Protocol:</strong> <code>HTTPS</code> (enable SSL/TLS on device)</div>
                            <div><strong>Token:</strong> <code>{{ $tenant->tenant_token }}</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="oldProtocolContent" style="display:none;">
    <div class="card border-0" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid rgba(245, 158, 11, 0.15) !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="fw-bold text-slate-800 mb-2" style="font-family: 'Poppins', sans-serif; font-size: 0.95rem;">
                        <i class="bx bx-server me-2 text-warning font-size-20 align-middle"></i> Old / Legacy Protocol &mdash; Serial-Number-Based Endpoint
                    </h6>
                    <p class="mb-2 text-slate-600 font-size-13">
                        For devices that <strong>don't support URL tokens</strong> in ADMS settings.
                        Uses standard tab-delimited push with <code>table=ATTLOG</code> / <code>table=OPERLOG</code>.
                        Device is identified automatically by its serial number.
                    </p>

                    <div class="mb-3 p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                        <span class="fw-bold font-size-12 text-amber-700"><i class="bx bx-key me-1"></i> Your Tenant Token:</span>
                        <div class="input-group mt-1" style="max-width: 580px;">
                            <input type="text" class="form-control font-size-13 bg-white fw-bold text-slate-800 py-2" value="{{ $tenant->tenant_token }}" readonly>
                            <button class="btn btn-success px-3 fw-bold font-size-13" type="button" onclick="navigator.clipboard.writeText('{{ $tenant->tenant_token }}')">
                                <i class="bx bx-copy me-1"></i> Copy Token
                            </button>
                        </div>
                        <div class="mt-1 font-size-11 text-muted">
                            Legacy protocol does <strong>not</strong> require a token — it uses serial-number-based tenant resolution.
                            Token is only needed for the <strong>New Protocol</strong> (domain-based) URL above.
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3" style="background: rgba(255,255,255,0.7); border: 1px solid rgba(245, 158, 11, 0.15);">
                        <span class="fw-bold font-size-12 text-slate-700">Device Configuration:</span>
                        <div class="mt-2 font-size-12 text-slate-600" style="line-height: 2;">
                            <div><strong>Server Address:</strong> <code>hr.nexogiant.com</code></div>
                            <div><strong>Port:</strong> <code>443</code></div>
                            <div><strong>Protocol:</strong> <code>HTTPS</code> (enable SSL/TLS on device)</div>
                        </div>
                        <div class="mt-2 font-size-11 text-amber-700">
                            <i class="bx bx-error-circle me-1"></i> Direct IP connections (<code>15.235.229.40</code>) are not supported — the server requires domain-based HTTPS connections.
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3" style="background: rgba(255,255,255,0.7); border: 1px solid rgba(245, 158, 11, 0.15);">
                        <span class="fw-bold font-size-12 text-slate-700">ADMS URL (full reference):</span>
                        <div class="input-group mt-1" style="max-width: 620px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                            <span class="input-group-text bg-white border-end-0 text-slate-400 font-size-13"><i class="bx bx-link"></i></span>
                            <input type="text" class="form-control font-size-13 bg-white border-start-0 border-end-0 fw-semibold text-slate-700 py-2" value="https://hr.nexogiant.com/iclock/cdata" readonly>
                            <button class="btn btn-warning px-3 fw-bold font-size-13" type="button" onclick="navigator.clipboard.writeText('https://hr.nexogiant.com/iclock/cdata')">
                                <i class="bx bx-copy me-1"></i> Copy URL
                            </button>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="fw-bold font-size-12 text-green-700"><i class="bx bx-check-circle me-1 text-green-500"></i> Endpoint Status</span>
                                <p class="font-size-12 text-muted mb-0 mt-1">
                                    <code>https://hr.nexogiant.com/iclock/cdata</code> responds to device handshake and attendance pushes.
                                    Serial-number-based tenant resolution is active.
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('subscriber.devices.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bx bx-chip me-1"></i> My Machines
                                </a>
                                <a href="{{ route('subscriber.adms.handshake-test') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bx bx-test-tube me-1"></i> Test Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-slate-800 mb-3"><i class="bx bx-plug text-primary me-1"></i> Device Connection Options — Which One to Use</h6>
        <p class="font-size-13 text-slate-600 mb-3">All three endpoints below work with the ADMS protocol (heartbeat, commands, attendance push). The difference is only how the device reaches this server:</p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle font-size-13 mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:26%;">Option</th>
                        <th style="width:34%;">Device Settings</th>
                        <th style="width:20%;">Status</th>
                        <th style="width:20%;">Required Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold"><i class="bx bx-globe text-primary me-1"></i> Domain + HTTPS</td>
                        <td>
                            Server Address: <code>hr.nexogiant.com</code><br>
                            Port: <code>443</code> · HTTPS/SSL: <strong>ON</strong>
                        </td>
                        <td><span class="badge bg-success font-size-11">WORKS NOW</span></td>
                        <td><span class="text-success">None — ready to use</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bx bx-globe text-warning me-1"></i> Domain + HTTP</td>
                        <td>
                            Server Address: <code>hr.nexogiant.com</code><br>
                            Port: <code>80</code> · HTTPS/SSL: <strong>OFF</strong>
                        </td>
                        <td><span class="badge bg-warning text-dark font-size-11">NEEDS CLOUDFLARE CHANGE</span></td>
                        <td>Turn OFF <em>Always Use HTTPS</em> and set SSL mode to <em>Flexible</em> in Cloudflare</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bx bx-server text-danger me-1"></i> Direct IP + HTTP</td>
                        <td>
                            Server IP: <code>{{ $serverIp }}</code><br>
                            Port: <code>80</code>
                        </td>
                        <td><span class="badge bg-danger font-size-11">NEEDS HOSTING CHANGE</span></td>
                        <td>cPanel server blocks IP-based requests (suspended page). Ask your host to point the server IP's default vhost at this account</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border: 1px solid #fecaca !important;">
        <h6 class="fw-bold text-red-700 mb-3"><i class="bx bx-error-circle me-1"></i> Why Direct IP &amp; HTTP Port 80 Don't Work Right Now</h6>
        <ul class="font-size-13 text-red-600 mb-3" style="line-height: 2;">
            <li><strong>Direct IP (<code>http://{{ $serverIp }}</code>)</strong> — the cPanel web server intercepts IP-based requests and serves a suspended page. Only domain-based requests reach this app.</li>
            <li><strong>HTTP Domain (<code>http://hr.nexogiant.com</code>)</strong> — Cloudflare is configured with <em>Always Use HTTPS</em>, so port 80 requests are redirected to HTTPS.</li>
            <li><strong>HTTPS Domain (<code>https://hr.nexogiant.com</code>)</strong> — works correctly right now.</li>
        </ul>

        <div class="p-3 rounded-3 mb-3" style="background:#fff; border:1px solid #fecaca;">
            <span class="fw-bold font-size-13 text-red-700"><i class="bx bx-wrench me-1"></i> To enable HTTP (port 80) — 2-minute Cloudflare fix:</span>
            <ol class="font-size-13 text-slate-700 mt-2 mb-0" style="line-height: 2;">
                <li>Log in to <strong>dash.cloudflare.com</strong> and select the <code>nexogiant.com</code> zone.</li>
                <li>Go to <strong>SSL/TLS → Edge Certificates</strong>.</li>
                <li>Turn <strong>OFF</strong> "<em>Always Use HTTPS</em>".</li>
                <li>Go to <strong>SSL/TLS → Overview</strong> and set encryption mode to <strong>Flexible</strong>.</li>
                <li>Done. Devices can now connect with <strong>Server Address: <code>hr.nexogiant.com</code>, Port: <code>80</code></strong> (no SSL on device).</li>
            </ol>
        </div>

        <div class="p-3 rounded-3" style="background:#fff; border:1px solid #fecaca;">
            <span class="fw-bold font-size-13 text-red-700"><i class="bx bx-server me-1"></i> To enable Direct IP — ask your hosting provider (WHM access required):</span>
            <p class="font-size-13 text-slate-700 mt-2 mb-0" style="line-height: 2;">
                In <strong>WHM → "Main IP Address" / "Apache Configuration → Configure PHP and suEXEC"</strong> area, set the
                <strong>Default Virtual Host</strong> for IP <code>{{ $serverIp }}</code> to serve the document root
                <code>/home/nexogiant/hr.nexogiant.com</code> (instead of the suspended page). Then devices using
                <strong>Server IP: <code>{{ $serverIp }}</code>, Port: <code>80</code></strong> will work.
            </p>
        </div>

        <p class="font-size-13 text-red-600 mt-3 mb-0">
            <strong>On your ZKTeco device now:</strong> Set Server Address = <code>hr.nexogiant.com</code>, Port = <code>443</code>, enable <strong>HTTPS/SSL</strong>.
        </p>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-slate-800 mb-3"><i class="bx bx-info-circle text-primary me-1"></i> Configuration Guide</h6>

        <div id="newProtocolGuide">
            <p class="font-size-13 text-slate-600 mb-3">For devices running <strong>newer firmware</strong> that supports domain-based ADMS servers:</p>
            <ol class="font-size-13 text-slate-600 mb-0" style="line-height: 2;">
                <li>On your ZKTeco biometric device, press <strong>MENU</strong> and log in as admin.</li>
                <li>Navigate to <strong>COMM. &gt; ADMS Cloud Server</strong> or <strong>Network &gt; ADMS</strong>.</li>
                <li>Set the <strong>Server URL</strong> or <strong>Server Address</strong> to <code>hr.nexogiant.com</code>.</li>
                <li>Set the <strong>Port</strong> to <code>443</code>.</li>
                <li><strong>Enable HTTPS/SSL</strong> if the option is available.</li>
                <li>Ensure the device has internet connectivity (DNS resolution required) and save.</li>
                <li>The device will handshake and start pushing attendance logs automatically.</li>
            </ol>
        </div>

        <div id="oldProtocolGuide" style="display:none;">
            <p class="font-size-13 text-slate-600 mb-3">For <strong>older devices</strong> with the legacy protocol:</p>
            <ol class="font-size-13 text-slate-600 mb-0" style="line-height: 2;">
                <li>On your ZKTeco biometric device, press <strong>MENU</strong> and log in as admin.</li>
                <li>Navigate to <strong>COMM. &gt; ADMS Cloud Server</strong> or <strong>Network &gt; ADMS</strong>.</li>
                <li>Set the <strong>Server Address</strong> to <code>hr.nexogiant.com</code> (NOT an IP address).</li>
                <li>Set the <strong>Port</strong> to <code>443</code> and <strong>enable HTTPS/SSL</strong>.</li>
                <li>The device will push data using tab-delimited format with <code>table=ATTLOG</code> (attendance) and <code>table=OPERLOG</code> (operation logs).</li>
                <li>The system automatically identifies your device by its <strong>serial number</strong> during handshake.</li>
            </ol>
            <div class="mt-3 p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                <p class="font-size-12 text-amber-700 mb-0"><strong>Note:</strong> IP-based connections are not supported on this server. All devices must use the domain name <code>hr.nexogiant.com</code> with HTTPS (port 443).</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-slate-800 mb-3"><i class="bx bx-chip text-primary me-1"></i> Choosing a Protocol Version</h6>
        <p class="font-size-13 text-slate-600 mb-3">When adding a device under <strong>Devices</strong>, set its <strong>Protocol Version</strong> to match the firmware:</p>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <span class="badge bg-success font-size-10 rounded-pill px-2 py-1 mb-2">New Protocol</span>
                    <p class="font-size-12 text-slate-600 mb-0">
                        Tolerant parsing for newer firmware that sends space/kv-delimited data, 
                        omits the <code>table</code> param, or uses <code>c=registry</code> for handshake.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <span class="badge bg-warning text-dark font-size-10 rounded-pill px-2 py-1 mb-2">Old / Legacy Protocol</span>
                    <p class="font-size-12 text-slate-600 mb-0">
                        Standard tab-delimited ADMS push with <code>table=ATTLOG</code> / <code>table=OPERLOG</code> required. 
                        No URL token needed (serial-number-based). Use <code>{{ $serverHost }}</code> on port 443 with HTTPS.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
        const baseHost = '{{ $serverHost }}';
        const serverIp = '{{ $serverIp }}';
        const token = '{{ $tenant->tenant_token }}';

    function switchProtocol(type) {
        document.getElementById('newProtocolTab').classList.toggle('active', type === 'new');
        document.getElementById('oldProtocolTab').classList.toggle('active', type === 'old');
        document.getElementById('newProtocolContent').style.display = type === 'new' ? '' : 'none';
        document.getElementById('oldProtocolContent').style.display = type === 'old' ? '' : 'none';
        document.getElementById('newProtocolGuide').style.display = type === 'new' ? '' : 'none';
        document.getElementById('oldProtocolGuide').style.display = type === 'old' ? '' : 'none';
    }

    function copyAdmsUrl() {
        const copyText = document.getElementById('adms-url');
        navigator.clipboard.writeText(copyText.value);
        showCopied('copy-btn', 'copy-icon', 'copy-text');
    }

    function showCopied(btnId, iconId, textId) {
        const btn = document.getElementById(btnId);
        const icon = document.getElementById(iconId);
        const text = document.getElementById(textId);
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        icon.className = 'bx bx-check me-1';
        text.textContent = 'Copied URL';
        setTimeout(() => {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            icon.className = 'bx bx-copy me-1';
            text.textContent = 'Copy URL';
        }, 2000);
    }
</script>
@endpush