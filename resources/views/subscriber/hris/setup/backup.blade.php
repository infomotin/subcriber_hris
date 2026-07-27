@extends('layouts.subscriber')

@section('title', 'Database Backup')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-data text-primary me-1.5 align-middle font-size-26"></i>Database Backup
        </h4>
    </div>
    <form method="POST" action="{{ route('subscriber.hris.setup.backup.create') }}" onsubmit="return confirm('Create a new tenant backup? Only your tenant data will be exported.')">
        @csrf
        <button type="submit" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
            <i class="bx bx-download me-1"></i> Create Backup
        </button>
    </form>
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

{{-- Tenant Isolation Notice --}}
<div class="alert alert-info rounded-pill px-4 mb-3" role="alert" style="background:#eff6ff;border:1px solid #bfdbfe;">
    <i class="bx bx-shield-quarter me-1 align-middle text-info"></i>
    <strong>Tenant Isolated:</strong> All backups contain only your tenant's data. Other tenants' data is never included. Backups are stored in a secure tenant-specific directory.
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Backup File</th>
                    <th>Size</th>
                    <th>Created</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-data font-size-20 text-primary"></i>
                                <div>
                                    <code class="font-size-12">{{ $backup['name'] }}</code>
                                    <div class="font-size-10 text-muted">Tenant #{{ auth()->user()->tenant_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-size-12">{{ $backup['size'] }} MB</td>
                        <td class="font-size-12">{{ $backup['date'] }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('subscriber.hris.setup.backup.download', $backup['name']) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 font-size-11" title="Download">
                                    <i class="bx bx-download me-0.5"></i> Download
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 font-size-11" onclick="confirmRestore('{{ $backup['name'] }}')" title="Restore">
                                    <i class="bx bx-reset me-0.5"></i> Restore
                                </button>
                                <form action="{{ route('subscriber.hris.setup.backup.delete', $backup['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup permanently?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11" title="Delete"><i class="bx bx-trash me-0.5"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bx bx-data font-size-40 d-block mb-2"></i> No backups yet. Click "Create Backup" to start.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Restore Confirmation Modal --}}
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;">
            <div class="modal-header border-bottom-0 pb-2 px-4 pt-4">
                <h5 class="fw-bold text-slate-800" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-reset text-warning me-1.5 align-middle"></i> Restore Backup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="alert alert-warning rounded-3 mb-3" style="background:#fef3c7;border:1px solid #f59e0b;">
                    <i class="bx bx-error-circle me-1 align-middle text-warning"></i>
                    <strong>Warning:</strong> This will overwrite your current tenant data with the backup. This action cannot be undone.
                </div>
                <p class="font-size-13 text-slate-700">Are you sure you want to restore backup: <code id="restoreFileName"></code>?</p>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="restoreForm" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                        <i class="bx bx-reset me-1"></i> Restore Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmRestore(filename) {
    document.getElementById('restoreFileName').textContent = filename;
    document.getElementById('restoreForm').action = '{{ route("subscriber.hris.setup.backup.restore", "PLACEHOLDER") }}'.replace('PLACEHOLDER', filename);
    new bootstrap.Modal(document.getElementById('restoreModal')).show();
}
</script>
@endpush
@endsection
