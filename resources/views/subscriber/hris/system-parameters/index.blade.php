@extends('layouts.subscriber')

@section('title', 'System Parameters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-slider text-primary me-2"></i> System Parameters</h4>
        <p class="text-muted font-size-13 mb-0">Manage tenant-specific configuration keys and values.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bx bx-plus me-1"></i> Add Parameter
    </button>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fff1f2 !important; border-left: 4px solid #f43f5e !important; color: #9f1239 !important; border-radius: 8px !important;">
        <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #ecfdf5 !important; border-left: 4px solid #10b981 !important; color: #065f46 !important; border-radius: 8px !important;">
        <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2"><i class="bx bx-slider"></i></div>
                    <div><span class="text-muted font-size-11">Total Parameters</span><br><span class="fw-bold font-size-14">{{ number_format($parameters->total()) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2"><i class="bx bx-check-circle"></i></div>
                    <div><span class="text-muted font-size-11">Active</span><br><span class="fw-bold font-size-14 text-success">{{ $parameters->getCollection()->where('status', 'active')->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2"><i class="bx bx-x-circle"></i></div>
                    <div><span class="text-muted font-size-11">Inactive</span><br><span class="fw-bold font-size-14 text-secondary">{{ $parameters->getCollection()->where('status', 'inactive')->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="max-width: 200px;">
                <span class="input-group-text bg-light border-end-0 py-1"><i class="bx bx-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Search key or value..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select form-select-sm bg-light border-0 py-1" style="max-width: 120px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bx bx-search me-1"></i> Filter</button>
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('subscriber.hris.system-parameters.index') }}" class="btn btn-sm btn-outline-secondary px-2"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="text-muted font-size-11 ms-auto">Showing {{ $parameters->firstItem() ?? 0 }}-{{ $parameters->lastItem() ?? 0 }} of {{ number_format($parameters->total()) }}</span>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:35px">#</th>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th style="width:90px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parameters as $param)
                        <tr>
                            <td class="text-muted">{{ $parameters->firstItem() + $loop->index }}</td>
                            <td><code class="text-primary fw-semibold">{{ $param->key_name }}</code></td>
                            <td>
                                <span class="text-dark">{{ $param->value ?? '—' }}</span>
                            </td>
                            <td><span class="text-muted font-size-11">{{ $param->description ?? '—' }}</span></td>
                            <td>
                                @if($param->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td><span class="text-muted font-size-11">{{ $param->updated_at->diffForHumans() }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" onclick='editParam({{ json_encode($param) }})' title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form action="{{ route('subscriber.hris.system-parameters.destroy', $param) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this parameter?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bx bx-slider me-1"></i> No parameters configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($parameters->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $parameters->links() }}
    </div>
    @endif
</div>

<!-- Modal: Add Parameter -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('subscriber.hris.system-parameters.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold"><i class="bx bx-plus-circle text-primary me-2"></i> Add Parameter</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                @if($errors->any() && !session('success'))
                    <div class="alert alert-danger py-2 font-size-12">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Parameter Key <span class="text-danger">*</span></label>
                    <input type="text" name="key_name" class="form-control form-control-sm" placeholder="e.g. LATE_BUFFER_IN_MINUTES" value="{{ old('key_name') }}" required>
                    <small class="text-muted">Will be stored in UPPERCASE.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Value</label>
                    <input type="text" name="value" class="form-control form-control-sm" placeholder="e.g. 15" value="{{ old('value') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Description</label>
                    <input type="text" name="description" class="form-control form-control-sm" placeholder="Brief description of this parameter" value="{{ old('description') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bx bx-save me-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Parameter -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="editForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold"><i class="bx bx-edit text-primary me-2"></i> Edit Parameter</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Parameter Key <span class="text-danger">*</span></label>
                    <input type="text" name="key_name" id="editKeyName" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Value</label>
                    <input type="text" name="value" id="editValue" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Description</label>
                    <input type="text" name="description" id="editDescription" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Status</label>
                    <select name="status" id="editStatus" class="form-select form-select-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bx bx-save me-1"></i> Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editParam(param) {
    document.getElementById('editForm').action = '{{ url("subscriber/hris/system-parameters") }}/' + param.id;
    document.getElementById('editKeyName').value = param.key_name;
    document.getElementById('editValue').value = param.value || '';
    document.getElementById('editDescription').value = param.description || '';
    document.getElementById('editStatus').value = param.status;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
@endpush
@endsection
