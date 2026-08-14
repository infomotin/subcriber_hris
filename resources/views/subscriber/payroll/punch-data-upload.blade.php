@extends('layouts.subscriber')

@section('title', 'Punch Data Upload - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .drop-zone {
        border: 2px dashed #cbd5e1; border-radius: 16px; padding: 3rem 2rem;
        text-align: center; cursor: pointer; transition: all 0.3s;
        background: #f8fafc;
    }
    .drop-zone:hover, .drop-zone.dragover { border-color: #6366f1; background: #eef2ff; }
    .drop-zone.dragover { border-color: #6366f1; }
    .drop-zone i { font-size: 3rem; color: #94a3b8; }
    .drop-zone.dragover i { color: #6366f1; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
</style>

<div class="page-title-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Databases</span>
        <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Upload Raw Punch Data</h4>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Live ADMS Punches</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $liveCount }}</h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600"><i class="bx bx-chip"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Synced to Raw</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $syncedCount }}</h3>
                </div>
                <div class="stat-icon bg-green-50 border border-green-100 text-green-600"><i class="bx bx-sync"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 p-3 h-100 d-flex justify-content-center">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="liveSyncToggle" style="width:3rem; height:1.5rem; cursor:pointer;"
                            onchange="toggleLiveSync(this.checked)"
                            {{ $liveSyncEnabled === '1' ? 'checked' : '' }}>
                    </div>
                    <div>
                        <span class="fw-bold font-size-13 d-block">Live Auto-Sync</span>
                        <span class="text-muted font-size-12">When enabled, new ADMS punches are automatically stored in raw_punch_data</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('subscriber.payroll.punch-data-upload.sync-live') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bx bx-sync me-1"></i> Sync Now
                    </button>
                </form>
            </div>
        </div>
        <form id="liveSyncForm" method="POST" action="{{ route('subscriber.payroll.punch-data-upload.toggle-live-sync') }}" class="d-none">
            @csrf
            <input type="hidden" name="enabled" id="liveSyncValue">
        </form>
    </div>
</div>

<div class="card mb-4 p-4" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(99,102,241,0.15) !important;">
    <div class="d-flex align-items-center gap-3">
        <i class="bx bx-info-circle text-primary font-size-24"></i>
        <div>
            <strong class="font-size-13">How it works:</strong>
            <p class="font-size-12 text-muted mb-0 mt-1">
                Upload a text file with raw punch data exported from your ZKTeco machine. 
                Each line should contain: <code>EmployeeID</code> tab <code>DateTime</code> tab <code>Status</code> tab <code>VerifyType</code>.
                The system matches employees by their Employee ID within your tenant and inserts records into <code>raw_punch_data</code>.
                Or enable <strong>Live Auto-Sync</strong> to automatically capture punches from connected ADMS devices.
            </p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bx bx-upload text-primary me-1"></i> Upload File</h6>
            <form method="POST" action="{{ route('subscriber.payroll.punch-data-upload.process') }}" enctype="multipart/form-data">
                @csrf
                <div class="drop-zone" id="dropZone">
                    <i class="bx bx-cloud-upload"></i>
                    <h6 class="fw-bold mt-3 mb-1">Drag & drop or click to upload</h6>
                    <p class="text-muted font-size-12 mb-3">Supported: .txt, .csv, .log, .dat (max 10MB)</p>
                    <input type="file" name="punch_file" id="fileInput" class="d-none" accept=".txt,.csv,.log,.dat" required>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4" onclick="document.getElementById('fileInput').click()">
                        <i class="bx bx-folder-open me-1"></i> Browse Files
                    </button>
                </div>
                <div id="fileInfo" class="mt-3 d-none">
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background:#f1f5f9;">
                        <div>
                            <i class="bx bx-file text-primary me-2"></i>
                            <span id="fileName" class="fw-semibold font-size-13"></span>
                            <span id="fileSize" class="text-muted font-size-12 ms-2"></span>
                        </div>
                        <i class="bx bx-x text-muted" style="cursor:pointer;" onclick="resetFile()"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill" id="uploadBtn" disabled>
                    <i class="bx bx-upload me-2"></i> Upload & Process
                </button>
            </form>

            <hr class="my-4">

            <h6 class="fw-bold mb-3 text-danger"><i class="bx bx-undo me-1"></i> Undo Punch Data</h6>
            <p class="font-size-12 text-muted mb-3">Select a month to remove all raw punch data for that period. This only affects <code>raw_punch_data</code> — original ADMS logs are unaffected.</p>
            <form method="POST" action="{{ route('subscriber.payroll.punch-data-upload.undo') }}" onsubmit="return confirmUndo(event)">
                @csrf
                @method('DELETE')
                <div class="mb-3">
                    <label class="form-label fw-semibold font-size-13">Select Month</label>
                    <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                </div>
                <div class="mb-3 p-3 rounded-3 font-size-12" id="monthPreview" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <span class="text-muted">Select a month to see record count...</span>
                </div>
                <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                    <i class="bx bx-trash me-2"></i> Undo & Remove for Selected Month
                </button>
            </form>

            <hr class="my-4">
            <h6 class="fw-bold mb-2"><i class="bx bx-info-circle text-muted me-1"></i> Expected Format</h6>
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <a href="{{ route('subscriber.payroll.download-template', 'txt') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bx bx-download me-1"></i> Tab Template (.txt)
                </a>
                <a href="{{ route('subscriber.payroll.download-template', 'csv') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="bx bx-download me-1"></i> CSV Template (.csv)
                </a>
            </div>
            <div class="font-size-12" style="font-family:monospace; background:#1e293b; color:#a5f3fc; padding:1rem; border-radius:8px;">
# Tab-delimited (from ZKTeco machine)<br>
1&nbsp;&nbsp;&nbsp;&nbsp;2026-07-27 08:15:00&nbsp;&nbsp;&nbsp;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;1<br>
2&nbsp;&nbsp;&nbsp;&nbsp;2026-07-27 08:20:30&nbsp;&nbsp;&nbsp;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;1<br>
3&nbsp;&nbsp;&nbsp;&nbsp;2026-07-27 17:30:00&nbsp;&nbsp;&nbsp;&nbsp;1&nbsp;&nbsp;&nbsp;&nbsp;1<br>
<br>
# Or space/comma delimited<br>
1 2026-07-27 08:15:00<br>
2,2026-07-27 08:20:30,0,1
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bx bx-data text-primary me-1"></i> Recent Punch Data</h6>
                <span class="badge bg-soft-primary text-primary font-size-11 rounded-pill px-3">{{ $recentPunches->total() }} records</span>
            </div>

            @if($recentPunches->count())
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle font-size-12">
                        <thead class="text-muted text-uppercase font-size-10">
                            <tr>
                                <th>Employee ID</th>
                                <th>Date Time</th>
                                <th>Machine Serial</th>
                                <th>Status</th>
                                <th>Matched</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPunches as $punch)
                                <tr>
                                    <td class="fw-semibold">{{ $punch->employee_id }}</td>
                                    <td>{{ $punch->punch_date_time }}</td>
                                    <td><code>{{ $punch->punch_machine_serial ?? '—' }}</code></td>
                                    <td>{{ $punch->status ?? '—' }}</td>
                                    <td>
                                        @if($punch->is_matched)
                                            <span class="badge bg-success font-size-10 rounded-pill">Yes</span>
                                        @else
                                            <span class="badge bg-warning text-dark font-size-10 rounded-pill">No</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ Str::limit($punch->source_file, 20) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $recentPunches->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No punch data uploaded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleLiveSync(enabled) {
        document.getElementById('liveSyncValue').value = enabled ? '1' : '0';
        document.getElementById('liveSyncForm').submit();
    }

    let monthPunchCount = {};

    document.querySelector('input[name="month"]')?.addEventListener('change', function() {
        const val = this.value;
        if (!val) return;
        fetch('{{ route("subscriber.payroll.punch-data-upload.month-count") }}?month=' + val)
            .then(r => r.json())
            .then(data => {
                monthPunchCount[val] = data.count;
                document.getElementById('monthPreview').innerHTML =
                    '<span class="fw-bold">' + data.count + '</span> <span class="text-muted">records found for <strong>' + val + '</strong></span>';
            })
            .catch(() => {
                document.getElementById('monthPreview').innerHTML = '<span class="text-muted">Could not load count.</span>';
            });
    });

    function confirmUndo(e) {
        e.preventDefault();
        const form = e.target;
        const month = form.querySelector('input[name="month"]').value;
        const count = monthPunchCount[month];
        const msg = count !== undefined
            ? 'Delete ' + count + ' punch record(s) for ' + month + '?\n\nThis action cannot be undone. Raw punch data will be permanently removed.'
            : 'Delete all punch records for ' + month + '?\n\nThis action cannot be undone.';
        if (confirm(msg)) {
            form.submit();
        }
    }

    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadBtn = document.getElementById('uploadBtn');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) handleFile(fileInput.files[0]);
    });

    function handleFile(file) {
        fileInfo.classList.remove('d-none');
        fileName.textContent = file.name;
        fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
        uploadBtn.disabled = false;
    }

    function resetFile() {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
        uploadBtn.disabled = true;
    }
</script>
@endpush
@endsection