@extends('layouts.system_admin')

@section('title', "Table: {$table}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-table text-primary me-2 font-size-22"></i> Table: <code>{{ $table }}</code></h4>
        <p class="text-muted font-size-13 mb-0">{{ $columns->count() }} columns &middot; {{ number_format($total) }} total rows</p>
    </div>
    <div>
        <a href="{{ route('admin.system.database.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bx bx-arrow-back me-1"></i> Back to Overview
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<div class="row g-4">
    {{-- Columns Info --}}
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-list-ul text-primary me-1"></i> Columns</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($columns as $col)
                        <div class="list-group-item py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <code class="fw-bold text-dark">{{ $col->Field }}</code>
                                <small class="text-muted">{{ $col->Type }}</small>
                            </div>
                            <small class="text-muted">
                                @if($col->Key === 'PRI')<span class="badge bg-warning text-dark">PK</span>@endif
                                @if($col->Null === 'NO')<span class="badge bg-danger">NOT NULL</span>@endif
                                @if($col->Default !== null)<span class="text-info">default: {{ $col->Default }}</span>@endif
                                @if($col->Extra) <span class="text-success">{{ $col->Extra }}</span>@endif
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Insert Row --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-plus-circle text-success me-1"></i> Insert Row</h6>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.system.database.table.insert', $table) }}" method="POST">
                    @csrf
                    @foreach($columns as $col)
                        @if($col->Extra === 'auto_increment') @continue @endif
                        <div class="mb-2">
                            <label class="form-label font-size-12 fw-bold mb-0">{{ $col->Field }}</label>
                            <input type="text" name="{{ $col->Field }}" class="form-control form-control-sm" placeholder="{{ $col->Type }}">
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-success btn-sm fw-bold w-100 mt-2">
                        <i class="bx bx-plus me-1"></i> Insert
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Table Data --}}
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-data me-1"></i> Row Data</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="bg-dark text-white sticky-top">
                            <tr>
                                @foreach($columns as $col)
                                    <th><small>{{ $col->Field }}</small></th>
                                @endforeach
                                <th class="text-end"><small>Actions</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    @foreach($columns as $col)
                                        <td>
                                            <small class="text-truncate d-inline-block" style="max-width: 200px;">
                                                @php $val = $row->{$col->Field}; @endphp
                                                @if(is_null($val))
                                                    <span class="text-muted fst-italic">NULL</span>
                                                @elseif($col->Field === $primaryKey)
                                                    <code class="fw-bold text-primary">{{ $val }}</code>
                                                @else
                                                    {{ $val }}
                                                @endif
                                            </small>
                                        </td>
                                    @endforeach
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#edit-{{ $row->{$primaryKey} }}">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.system.database.table.delete', [$table, $row->{$primaryKey}]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete row {{ $row->{$primaryKey} }}?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                {{-- Edit Row (collapsible inline form) --}}
                                <tr class="collapse" id="edit-{{ $row->{$primaryKey} }}">
                                    <td colspan="{{ $columns->count() + 1 }}" class="bg-light p-3">
                                        <form action="{{ route('admin.system.database.table.update', [$table, $row->{$primaryKey}]) }}" method="POST" class="row g-2">
                                            @csrf
                                            @foreach($columns as $col)
                                                @if($col->Extra === 'auto_increment') @continue @endif
                                                <div class="col-md-4">
                                                    <label class="form-label font-size-11 fw-bold mb-0">{{ $col->Field }}</label>
                                                    <input type="text" name="{{ $col->Field }}" class="form-control form-control-sm" value="{{ $row->{$col->Field} ?? '' }}">
                                                </div>
                                            @endforeach
                                            <div class="col-12 mt-2">
                                                <button type="submit" class="btn btn-warning btn-sm fw-bold">
                                                    <i class="bx bx-save me-1"></i> Update
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-{{ $row->{$primaryKey} }}">Cancel</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $columns->count() + 1 }}" class="text-center text-muted py-4">No rows in this table.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted font-size-13">Page {{ $page }} of {{ max(1, (int) ceil($total / $perPage)) }}</span>
                <div>
                    @if($page > 1)
                        <a href="{{ route('admin.system.database.table', [$table, 'page' => $page - 1]) }}" class="btn btn-sm btn-outline-primary">Prev</a>
                    @endif
                    @if($page * $perPage < $total)
                        <a href="{{ route('admin.system.database.table', [$table, 'page' => $page + 1]) }}" class="btn btn-sm btn-outline-primary">Next</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
