@extends('layouts.subscriber')

@section('title', 'ADMS Handshake Test')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .response-box {
        background: #1e293b; color: #a5f3fc; border-radius: 10px;
        font-family: 'Courier New', monospace; font-size: 0.8rem;
        max-height: 300px; overflow-y: auto; white-space: pre-wrap; word-break: break-all;
    }
    .response-box .success { color: #4ade80; }
    .response-box .error { color: #f87171; }
    .response-box .info { color: #fbbf24; }
</style>

<div class="page-title-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">ADMS Handshake & Protocol Test</h4>
    </div>
    <span class="badge bg-soft-primary text-primary font-size-11 px-3 py-2 rounded-pill">
        <i class="bx bx-key me-1"></i> Token: <strong>{{ $tenant->tenant_token }}</strong>
    </span>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-success font-size-10 rounded-pill px-2 py-1">New Protocol</span>
                <h6 class="fw-bold mb-0">Domain-Based Endpoint</h6>
            </div>
            <p class="text-muted font-size-12 mb-3">For modern ZKTeco firmware with domain support.</p>
            <div class="mb-2 font-size-12"><strong>Endpoint:</strong></div>
            <code class="d-block p-2 rounded-2 mb-3" style="background:#f1f5f9; font-size:0.85rem;">{{ request()->getSchemeAndHttpHost() }}/iclock/{{ $tenant->tenant_token }}/cdata</code>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="testHandshake('new')">
                    <i class="bx bx-plug me-1"></i> Simulate Handshake
                </button>
                <button class="btn btn-sm btn-outline-success rounded-pill" onclick="sendDemoAttendance('new')">
                    <i class="bx bx-send me-1"></i> Send Demo ATTLOG
                </button>
                <button class="btn btn-sm btn-outline-warning rounded-pill" onclick="sendDemoOperlog('new')">
                    <i class="bx bx-send me-1"></i> Send Demo OPERLOG
                </button>
            </div>

            <div class="mb-2 font-size-12"><strong>Demo ATTLOG Data:</strong></div>
            <div class="p-2 rounded-2 mb-2 font-size-11" style="background:#f8fafc; font-family:monospace;">
1	2026-07-27 08:15:00	0	1	0		
2	2026-07-27 08:20:30	0	1	0		
3	2026-07-27 09:05:00	0	15	0		
4	2026-07-27 09:10:15	0	1	0		
5	2026-07-27 17:30:00	1	1	0		
            </div>

            <div class="response-box p-3" id="newResponse">
                <span class="info">// Click a button above to test the endpoint</span>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-warning text-dark font-size-10 rounded-pill px-2 py-1">Old / Legacy Protocol</span>
                <h6 class="fw-bold mb-0">IP-Based Endpoint</h6>
            </div>
            <p class="text-muted font-size-12 mb-3">For older ZKTeco devices that only support IP address.</p>
            <div class="mb-2 font-size-12"><strong>Server IP Address:</strong></div>
            <code class="d-block p-2 rounded-2 mb-3" style="background:#f1f5f9; font-size:0.85rem;">{{ $serverIp }}</code>
            <div class="mb-2 font-size-12"><strong>Port:</strong> <code>80</code></div>
            <div class="mb-3 p-2 rounded-2 font-size-11" style="background:#fffbeb; border:1px solid #fde68a;">
                <strong>Note:</strong> Old devices only need <strong>Server IP</strong> and <strong>Port</strong> in COMM &gt; ADMS settings — not a URL.
                The system automatically resolves the endpoint as <code>http://{{ $serverIp }}/iclock/cdata</code>.
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="testHandshake('legacy')">
                    <i class="bx bx-plug me-1"></i> Simulate Handshake
                </button>
                <button class="btn btn-sm btn-outline-success rounded-pill" onclick="sendDemoAttendance('legacy')">
                    <i class="bx bx-send me-1"></i> Send Demo ATTLOG
                </button>
                <button class="btn btn-sm btn-outline-warning rounded-pill" onclick="sendDemoOperlog('legacy')">
                    <i class="bx bx-send me-1"></i> Send Demo OPERLOG
                </button>
            </div>

            <div class="mb-2 font-size-12"><strong>Demo ATTLOG Data (tab-delimited):</strong></div>
            <div class="p-2 rounded-2 mb-2 font-size-11" style="background:#f8fafc; font-family:monospace;">
table=ATTLOG
SN=DEMO{{ str_pad(rand(1000,9999), 4, '0', STR_PAD_LEFT) }}
PIN	DateTime	Status	VerifyType	WorkCode
1	2026-07-27 08:15:00	0	1	0
2	2026-07-27 08:20:30	0	1	0
3	2026-07-27 17:30:00	1	1	0
            </div>

            <div class="response-box p-3" id="legacyResponse">
                <span class="info">// Click a button above to test the endpoint</span>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bx bx-info-circle text-primary me-1"></i> How the Handshake Works</h6>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="fw-bold font-size-13 d-block mb-2">New Protocol Handshake</span>
                <ol class="font-size-12 text-muted mb-0 ps-3" style="line-height: 2;">
                    <li>Device sends <code>GET /iclock/{token}/cdata?SN=SERIAL&options=all</code></li>
                    <li>Server identifies tenant from URL token, responds with device config options</li>
                    <li>Device parses config, adjusts its polling/push settings</li>
                    <li>Device pushes attendance via <code>POST /iclock/{token}/cdata?SN=SERIAL&table=ATTLOG</code></li>
                </ol>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                <span class="fw-bold font-size-13 d-block mb-2">Legacy Protocol Handshake</span>
                <ol class="font-size-12 text-muted mb-0 ps-3" style="line-height: 2;">
                    <li>Device sends <code>GET /iclock/cdata?SN=SERIAL&options=all</code></li>
                    <li>Server identifies device by serial number, assigns to tenant</li>
                    <li>Device pushes data with <code>POST /iclock/cdata</code> including <code>table=ATTLOG</code> in body</li>
                    <li>Server parses tab-delimited lines: <code>PIN \t DateTime \t Status \t VerifyType</code></li>
                </ol>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const baseUrl = '{{ request()->getSchemeAndHttpHost() }}';
const token = '{{ $tenant->tenant_token }}';
const demoSerial = 'DEMO' + String(Math.floor(1000 + Math.random() * 9000));

function newEndpoint() { return baseUrl + '/iclock/' + token + '/cdata'; }
function legacyEndpoint() { return baseUrl + '/iclock/cdata'; }

function log(type, elId, msg) {
    const el = document.getElementById(elId);
    const cls = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
    el.innerHTML = '<span class="' + cls + '">// ' + new Date().toLocaleTimeString() + ' [' + type.toUpperCase() + ']</span>\n' + msg + '\n\n' + el.innerHTML;
}

function testHandshake(protocol) {
    const url = protocol === 'new' ? newEndpoint() + '?SN=' + demoSerial + '&options=all' : legacyEndpoint() + '?SN=' + demoSerial + '&options=all';
    const respId = protocol === 'new' ? 'newResponse' : 'legacyResponse';
    log('info', respId, 'Connecting to: ' + url);

    fetch(url)
        .then(r => r.text())
        .then(data => {
            log('success', respId, data);
        })
        .catch(err => {
            log('error', respId, 'Error: ' + err.message);
        });
}

function sendDemoAttendance(protocol) {
    const url = protocol === 'new' ? newEndpoint() + '?SN=' + demoSerial + '&table=ATTLOG' : legacyEndpoint() + '?SN=' + demoSerial + '&table=ATTLOG';
    const respId = protocol === 'new' ? 'newResponse' : 'legacyResponse';

    const body = protocol === 'new'
        ? '1\t2026-07-27 08:15:00\t0\t1\t0\t\t\n2\t2026-07-27 08:20:30\t0\t1\t0\t\t\n3\t2026-07-27 09:05:00\t0\t15\t0\t\t\n4\t2026-07-27 17:30:00\t1\t1\t0\t\t'
        : '1\t2026-07-27 08:15:00\t0\t1\t0\t\t\n2\t2026-07-27 08:20:30\t0\t1\t0\t\t\n3\t2026-07-27 17:30:00\t1\t1\t0\t\t';

    log('info', respId, 'Sending ' + body.split('\\n').length + ' attendance records to: ' + url);

    fetch(url, { method: 'POST', body: body })
        .then(r => r.text())
        .then(data => {
            log('success', respId, data);
        })
        .catch(err => {
            log('error', respId, 'Error: ' + err.message);
        });
}

function sendDemoOperlog(protocol) {
    const url = protocol === 'new' ? newEndpoint() + '?SN=' + demoSerial + '&table=OPERLOG' : legacyEndpoint() + '?SN=' + demoSerial + '&table=OPERLOG';
    const respId = protocol === 'new' ? 'newResponse' : 'legacyResponse';
    const body = '1\t2026-07-27 08:00:00\tAdmin logged in\n2\t2026-07-27 17:35:00\tAdmin logged out';

    log('info', respId, 'Sending OPERLOG data to: ' + url);

    fetch(url, { method: 'POST', body: body })
        .then(r => r.text())
        .then(data => {
            log('success', respId, data);
        })
        .catch(err => {
            log('error', respId, 'Error: ' + err.message);
        });
}
</script>
@endpush