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
    .cmd-btn { min-width: 130px; }
    .cmd-pending { opacity: 0.6; pointer-events: none; }
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
                <h6 class="fw-bold mb-0">Legacy Endpoint (No Token)</h6>
            </div>
            <p class="text-muted font-size-12 mb-3">Uses serial-number-based resolution. For devices that don't support URL tokens.</p>
            <div class="mb-2 font-size-12"><strong>Server Address:</strong></div>
            <code class="d-block p-2 rounded-2 mb-3" style="background:#f1f5f9; font-size:0.85rem;">https://{{ $serverHost }}/iclock/cdata</code>
            <div class="mb-2 font-size-12"><strong>Port:</strong> <code>443</code> (HTTPS required)</div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="testHandshake('legacy')">
                    <i class="bx bx-plug me-1"></i> Simulate Handshake
                </button>
                <button class="btn btn-sm btn-outline-success rounded-pill" onclick="sendDemoAttendance('legacy')">
                    <i class="bx bx-send me-1"></i> Send Demo ATTLOG
                </button>
            </div>

            <div class="p-2 rounded-2 mb-3 font-size-11" style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c;">
                <i class="bx bx-error-circle me-1"></i> Direct IP (<code>{{ $serverIp }}</code>:80) is <strong>blocked</strong> by the hosting server (cPanel suspended page). All devices must use the domain <code>{{ $serverHost }}</code> on port 443.
            </div>

            <div class="response-box p-3" id="legacyResponse">
                <span class="info">// Click a button above to test the endpoint</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- REAL DEVICE TEST SECTION --}}
{{-- ============================================================ --}}
<div class="card mt-4 p-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bx bx-chip text-primary font-size-20"></i>
        <h6 class="fw-bold mb-0">Real Device Test</h6>
    </div>
    <p class="text-muted font-size-12 mb-4">Select a registered ZKTeco device and send real commands. Commands are queued and delivered on the device's next heartbeat poll.</p>

    <div class="row g-4">
        {{-- Left: Device Selection & Command Buttons --}}
        <div class="col-lg-5">
            <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <label class="form-label fw-bold text-dark font-size-12 mb-2">Select Device</label>
                <select id="realDeviceSelect" class="form-select form-select-sm font-size-13">
                    <option value="">-- Choose a device --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" data-sn="{{ $device->serial_number }}" data-name="{{ $device->name }}">
                            {{ $device->name }} ({{ $device->serial_number }})
                        </option>
                    @endforeach
                </select>
                <div id="deviceInfo" class="mt-2 font-size-11 text-muted" style="display:none;"></div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-sm btn-outline-info cmd-btn" onclick="sendRealCommand('info')" id="btnInfo">
                    <i class="bx bx-info-circle me-1"></i> Get Device Info
                </button>
                <button class="btn btn-sm btn-outline-warning cmd-btn" onclick="sendRealCommand('reboot')" id="btnReboot">
                    <i class="bx bx-revision me-1"></i> Reboot
                </button>
                <button class="btn btn-sm btn-outline-danger cmd-btn" onclick="sendRealCommand('clear_log')" id="btnClearLog">
                    <i class="bx bx-trash me-1"></i> Clear Logs
                </button>
                <button class="btn btn-sm btn-outline-primary cmd-btn" onclick="sendRealCommand('get_users')" id="btnGetUsers">
                    <i class="bx bx-group me-1"></i> Get Users
                </button>
            </div>

            <div class="p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                <label class="form-label fw-bold text-danger font-size-12 mb-2">
                    <i class="bx bx-error me-1"></i> Delete User by PIN
                </label>
                <div class="input-group input-group-sm">
                    <input type="text" id="deletePin" class="form-control font-size-12" placeholder="Enter PIN (e.g. 1001)">
                    <button class="btn btn-outline-danger" onclick="deleteRealUser()" id="btnDeleteUser">
                        <i class="bx bx-user-x me-1"></i> Remove
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Response Log --}}
        <div class="col-lg-7">
            <div class="mb-2 font-size-12 fw-bold text-dark">
                <i class="bx bx-terminal me-1"></i> Command Log
            </div>
            <div class="response-box p-3" id="realDeviceResponse" style="min-height: 320px;">
                <span class="info">// Select a device and send a command to see the response here.</span>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bx bx-info-circle text-primary me-1"></i> How Real Device Commands Work</h6>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="fw-bold font-size-13 d-block mb-2">1. Command Queued</span>
                <p class="font-size-12 text-muted mb-0">When you click a command button, a pending command is created in the database with type INFO, REBOOT, CLEAR, etc.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="fw-bold font-size-13 d-block mb-2">2. Device Polls Server</span>
                <p class="font-size-12 text-muted mb-0">The ZKTeco device periodically calls <code>GET /iclock/getrequest?SN=xxx</code>. The server responds with any pending commands.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="fw-bold font-size-13 d-block mb-2">3. Device Executes</span>
                <p class="font-size-12 text-muted mb-0">The device executes the command and reports back via <code>POST /iclock/devicecmd</code> with a return code (0 = success).</p>
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
        .then(data => log('success', respId, data))
        .catch(err => log('error', respId, 'Error: ' + err.message));
}

function sendDemoAttendance(protocol) {
    const url = protocol === 'new' ? newEndpoint() + '?SN=' + demoSerial + '&table=ATTLOG' : legacyEndpoint() + '?SN=' + demoSerial + '&table=ATTLOG';
    const respId = protocol === 'new' ? 'newResponse' : 'legacyResponse';
    const body = '1\t2026-07-27 08:15:00\t0\t1\t0\t\t\n2\t2026-07-27 08:20:30\t0\t1\t0\t\t\n3\t2026-07-27 17:30:00\t1\t1\t0\t\t';
    log('info', respId, 'Sending ' + body.split('\n').length + ' attendance records to: ' + url);
    fetch(url, { method: 'POST', body: body })
        .then(r => r.text())
        .then(data => log('success', respId, data))
        .catch(err => log('error', respId, 'Error: ' + err.message));
}

{{-- ============================================================ --}}
{{-- REAL DEVICE COMMANDS --}}
{{-- ============================================================ --}}
const commandNames = {
    'info': 'Get Device Info',
    'reboot': 'Reboot Device',
    'clear_log': 'Clear Attendance Logs',
    'get_users': 'Get User List',
    'delete_user': 'Delete User'
};

function getSelectedDevice() {
    const sel = document.getElementById('realDeviceSelect');
    const opt = sel.options[sel.selectedIndex];
    if (!sel.value) return null;
    return { id: sel.value, name: opt.dataset.name, sn: opt.dataset.sn };
}

function sendRealCommand(type) {
    const device = getSelectedDevice();
    if (!device) {
        log('error', 'realDeviceResponse', 'Please select a device first.');
        return;
    }

    const btnId = 'btn' + type.charAt(0).toUpperCase() + type.slice(1).replace('_', '');
    const btn = document.getElementById(btnId);
    if (btn) btn.classList.add('cmd-pending');

    log('info', 'realDeviceResponse', 'Sending [' + commandNames[type] + '] to ' + device.name + ' (' + device.sn + ')...');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    fetch('{{ route("subscriber.adms.device-command") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            device_id: device.id,
            command: type
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let msg = 'Command Queued!\n' +
                '  Command ID : #' + data.command_id + '\n' +
                '  Command    : ' + data.command + '\n' +
                '  Formatted  : ' + data.formatted + '\n' +
                '  Type       : ' + data.type + '\n' +
                '  Status     : ' + data.status + '\n' +
                '  Device     : ' + data.device + '\n' +
                '  Online     : ' + (data.device_online ? 'YES' : 'NO') + '\n\n' +
                data.message;
            if (!data.device_online) {
                msg += '\n\n⚠ Device is OFFLINE. Command will be delivered when device reconnects.';
            }
            log(data.device_online ? 'success' : 'info', 'realDeviceResponse', msg);
            pollCommandStatus(data.command_id, 0);
        } else {
            log('error', 'realDeviceResponse', 'Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => log('error', 'realDeviceResponse', 'Request failed: ' + err.message))
    .finally(() => { if (btn) btn.classList.remove('cmd-pending'); });
}

function deleteRealUser() {
    const device = getSelectedDevice();
    const pin = document.getElementById('deletePin').value.trim();

    if (!device) {
        log('error', 'realDeviceResponse', 'Please select a device first.');
        return;
    }
    if (!pin) {
        log('error', 'realDeviceResponse', 'Please enter a User PIN to delete.');
        return;
    }

    if (!confirm('Delete user PIN ' + pin + ' from ' + device.name + '? This will remove the user from the physical device.')) return;

    const btn = document.getElementById('btnDeleteUser');
    btn.classList.add('cmd-pending');

    log('info', 'realDeviceResponse', 'Sending [Delete User PIN=' + pin + '] to ' + device.name + '...');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    fetch('{{ route("subscriber.adms.device-command") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            device_id: device.id,
            command: 'delete_user',
            pin: pin
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let msg = 'Delete User Command Queued!\n' +
                '  Command ID : #' + data.command_id + '\n' +
                '  Command    : ' + data.command + '\n' +
                '  Device     : ' + data.device + '\n' +
                '  Online     : ' + (data.device_online ? 'YES' : 'NO') + '\n\n' +
                data.message;
            if (!data.device_online) {
                msg += '\n\n⚠ Device is OFFLINE. Command will be delivered when device reconnects.';
            }
            log(data.device_online ? 'success' : 'info', 'realDeviceResponse', msg);
            document.getElementById('deletePin').value = '';
            pollCommandStatus(data.command_id, 0);
        } else {
            log('error', 'realDeviceResponse', 'Error: ' + (data.error || 'Unknown'));
        }
    })
    .catch(err => log('error', 'realDeviceResponse', 'Failed: ' + err.message))
    .finally(() => btn.classList.remove('cmd-pending'));
}

function pollCommandStatus(commandId, attempt) {
    if (attempt > 60) {
        log('info', 'realDeviceResponse', 'Stopped polling after 10 minutes. Command may still be pending if the device is offline or not responding.');
        return;
    }

    setTimeout(() => {
        fetch('{{ route("subscriber.adms.command-status") }}?command_id=' + commandId, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'executed') {
                log('success', 'realDeviceResponse',
                    'Command #' + data.id + ' EXECUTED by ' + data.device + '\n' +
                    '  Return Code : ' + data.return_code + '\n' +
                    '  Response    : ' + (data.response || 'None') + '\n' +
                    '  Executed At : ' + data.executed_at
                );
            } else if (data.status === 'failed') {
                log('error', 'realDeviceResponse',
                    'Command #' + data.id + ' FAILED on ' + data.device + '\n' +
                    '  Return Code : ' + data.return_code + '\n' +
                    '  Response    : ' + (data.response || 'None')
                );
            } else {
                const devStatus = data.device_online ? 'Device ONLINE' : 'Device OFFLINE';
                log('info', 'realDeviceResponse',
                    'Command #' + data.id + ' status: ' + data.status.toUpperCase() +
                    ' (' + devStatus + ', attempt ' + (attempt + 1) + '/60)'
                );
                pollCommandStatus(commandId, attempt + 1);
            }
        })
        .catch(() => pollCommandStatus(commandId, attempt + 1));
    }, 10000);
}

document.getElementById('realDeviceSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('deviceInfo');
    if (this.value) {
        info.style.display = 'block';
        info.innerHTML = '<strong>' + opt.dataset.name + '</strong> &mdash; SN: <code>' + opt.dataset.sn + '</code>';
        fetch('{{ route("subscriber.adms.device-status") }}?device_id=' + this.value, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            const status = d.is_online ? '<span style="color:green">ONLINE</span>' : '<span style="color:red">OFFLINE</span>';
            const hb = d.last_heartbeat !== 'Never' ? d.last_heartbeat : 'never';
            let traffic = '';
            if (!d.has_real_traffic) {
                traffic = '<br><span style="color:#b91c1c"><i class="bx bx-error-circle"></i> No real device traffic yet — heartbeats come from server-side tests. Configure the device with <code>' + location.origin + '/iclock/cdata</code> (port 443).</span>';
            } else {
                traffic = '<br><span style="color:green"><i class="bx bx-check-circle"></i> Real device detected (last source IP: ' + d.ip_address + ')</span>';
            }
            info.innerHTML += ' — Status: ' + status + ' (last heartbeat: ' + hb + ')' + traffic;
        })
        .catch(() => {});
    } else {
        info.style.display = 'none';
    }
});
</script>
@endpush
