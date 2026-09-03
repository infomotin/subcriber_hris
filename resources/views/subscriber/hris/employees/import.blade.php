@extends('layouts.subscriber')

@section('title', 'Import Employees')

@section('content')
<style>
    .imp-zone {
        border: 2px dashed #cbd5e1; border-radius: 16px; padding: 2.5rem;
        text-align: center; background: #f8fafc; transition: all .2s; cursor: pointer;
    }
    .imp-zone:hover, .imp-zone.dragover {
        border-color: var(--primary); background: rgba(95,90,246,.03);
    }
    .imp-zone input[type="file"] { display: none; }
    .imp-zone .imp-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 0.5rem; }
    .imp-zone p { font-size: 0.8rem; color: #64748b; margin: 0; }
    .imp-zone .imp-hint { font-size: 0.68rem; color: #94a3b8; margin-top: 0.3rem; }

    .imp-step-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04); padding: 1rem; margin-bottom: 0.75rem;
    }
    .imp-step-num {
        width: 28px; height: 28px; border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center; font-size: 0.7rem;
        font-weight: 700; color: #fff; background: var(--primary); margin-right: 0.5rem;
        flex-shrink: 0;
    }
    .imp-step-title {
        font-weight: 700; font-size: 0.82rem; color: #1e293b;
        display: flex; align-items: center;
    }
    .imp-step-body { margin-top: 0.6rem; }

    .imp-preview-table { font-size: 0.68rem; }
    .imp-preview-table th { font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.4rem 0.5rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .imp-preview-table td { padding: 0.35rem 0.5rem; vertical-align: middle; border-bottom: 1px solid #f8fafc; }

    .imp-error-badge { font-size: 0.6rem; padding: 0.15em 0.4em; border-radius: 6px; }
    .imp-stat-box {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.8rem; border-radius: 10px; font-size: 0.72rem; font-weight: 600;
    }
</style>

<div class="page-title-box mb-3 d-flex align-items-center justify-content-between">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Directory</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-import text-primary me-1.5 align-middle"></i> Import Employees from Excel
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.employees.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bx bx-arrow-back me-1"></i> Back
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" style="font-size:0.78rem;">
        <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert" style="font-size:0.78rem;">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
@endif

 {{-- STEP 1: Download Template --}}
<div class="imp-step-card">
    <div class="imp-step-title">
        <span class="imp-step-num">1</span> Download the Excel Template
    </div>
    <div class="imp-step-body">
        <p style="font-size:0.75rem;color:#64748b;margin:0;">
            Download the template file. It contains all required column headers, an example row, and a <strong>Reference</strong> sheet with valid dropdown values (departments, designations, divisions, districts, thanas, etc.).
        </p>
        <a href="{{ route('subscriber.hris.employees.import.template') }}" class="btn btn-sm btn-success rounded-pill px-3 mt-2" style="font-size:0.72rem;">
            <i class="bx bx-download me-1"></i> Download Template (.xlsx)
        </a>
    </div>
</div>

 {{-- STEP 2: Upload Filled File --}}
<div class="imp-step-card">
    <div class="imp-step-title">
        <span class="imp-step-num">2</span> Upload Your Filled Excel File
    </div>
    <div class="imp-step-body">
        <form action="{{ route('subscriber.hris.employees.import.preview') }}" method="POST" enctype="multipart/form-data" id="imp-upload-form">
            @csrf
            <div class="imp-zone" id="imp-drop-zone" onclick="document.getElementById('imp-file-input').click()">
                <div class="imp-icon"><i class="bx bx-cloud-upload"></i></div>
                <p><strong>Click to browse</strong> or drag & drop your .xlsx file here</p>
                <div class="imp-hint">Supports .xlsx, .xls, .csv — Max 10MB</div>
                <input type="file" id="imp-file-input" name="import_file" accept=".xlsx,.xls,.csv" onchange="impFileSelected(this)">
            </div>
            <div id="imp-file-info" class="mt-2" style="display:none;">
                <span class="badge bg-primary rounded-pill px-3 py-1.5" style="font-size:0.7rem;">
                    <i class="bx bx-file me-1"></i> <span id="imp-file-name"></span>
                </span>
            </div>
            <button type="submit" id="imp-upload-btn" class="btn btn-primary rounded-pill px-4 mt-2" style="font-size:0.72rem;display:none;">
                <i class="bx bx-upload me-1"></i> Upload & Validate
            </button>
        </form>
    </div>
</div>

 {{-- STEP 3: Preview Results (after upload) --}}
@if(!empty($preview))
<div class="imp-step-card">
    <div class="imp-step-title">
        <span class="imp-step-num">3</span> Review Validation Results
    </div>
    <div class="imp-step-body">
        <div class="d-flex gap-3 mb-3 flex-wrap">
            <div class="imp-stat-box" style="background:#ecfdf5;color:#059669;">
                <i class="bx bx-check-circle"></i> {{ count($validRows) }} Valid Rows
            </div>
            <div class="imp-stat-box" style="background:#fef2f2;color:#dc2626;">
                <i class="bx bx-error"></i> {{ count($errorRows) }} Error Rows
            </div>
        </div>

        @if(!empty($errorRows))
        <div class="mb-3">
            <h6 style="font-size:0.78rem;font-weight:700;color:#dc2626;margin-bottom:0.4rem;">
                <i class="bx bx-error me-1"></i> Rows with Errors (will be skipped)
            </h6>
            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                <table class="table table-sm imp-preview-table mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Row</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Errors</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($errorRows as $rowNum => $err)
                            <tr style="background:#fef2f2;">
                                <td class="fw-bold">{{ $rowNum }}</td>
                                <td>{{ $err['data']['full_name'] ?? $err['data']['full name'] ?? '-' }}</td>
                                <td>{{ $err['data']['email_login'] ?? $err['data']['email_(login)'] ?? '-' }}</td>
                                <td>{{ $err['data']['employee_id'] ?? $err['data']['employee id'] ?? '-' }}</td>
                                <td>
                                    @foreach($err['errors'] as $er)
                                        <span class="badge bg-danger imp-error-badge d-block mb-1 text-start">{{ $er }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(!empty($validRows))
        <div class="mb-3">
            <h6 style="font-size:0.78rem;font-weight:700;color:#059669;margin-bottom:0.4rem;">
                <i class="bx bx-check-circle me-1"></i> Valid Rows Ready to Import
            </h6>
            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                <table class="table table-sm imp-preview-table mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Row</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validRows as $rowNum => $row)
                            <tr>
                                <td class="fw-bold">{{ $rowNum }}</td>
                                <td>{{ $row['full_name'] ?? $row['full name'] ?? '-' }}</td>
                                <td>{{ $row['email_login'] ?? $row['email_(login)'] ?? '-' }}</td>
                                <td>{{ $row['employee_id'] ?? $row['employee id'] ?? '-' }}</td>
                                <td>{{ $row['department'] ?? '-' }}</td>
                                <td>{{ $row['designation'] ?? '-' }}</td>
                                <td>{{ $row['joining_date'] ?? $row['joining date'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-soft-success text-success rounded-pill" style="font-size:0.6rem;">
                                        {{ $row['status'] ?? 'active' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <form action="{{ route('subscriber.hris.employees.import.execute') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success rounded-pill px-4" style="font-size:0.75rem;" onclick="return confirm('Import {{ count($validRows) }} employees? This cannot be undone.');">
                <i class="bx bx-check-circle me-1"></i> Import {{ count($validRows) }} Employees
            </button>
        </form>
        @else
        <div class="text-center py-3">
            <i class="bx bx-info-circle text-warning font-size-24 d-block mb-2"></i>
            <p style="font-size:0.78rem;color:#64748b;">No valid rows found. Please fix the errors and re-upload.</p>
        </div>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function impFileSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('imp-file-name').textContent = input.files[0].name;
        document.getElementById('imp-file-info').style.display = 'block';
        document.getElementById('imp-upload-btn').style.display = 'inline-flex';
    }
}

const dropZone = document.getElementById('imp-drop-zone');
const fileInput = document.getElementById('imp-file-input');

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        impFileSelected(fileInput);
    }
});
</script>
@endpush
