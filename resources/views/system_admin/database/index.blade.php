@extends('layouts.system_admin')

@section('title', 'Database Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-data text-warning me-2 font-size-22"></i> Database Manager</h4>
        <p class="text-muted font-size-13 mb-0">Backup, restore, browse tables, and run SQL queries.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<ul class="nav nav-tabs card-header-tabs mb-4 bg-white p-2 rounded shadow-sm border-0" style="gap: 4px;">
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request('tab') === 'backup' ? '' : 'active' }}" href="{{ route('admin.system.database.index') }}">
            <i class="bx bx-stats me-1"></i> Overview
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request('tab') === 'backup' ? 'active' : '' }}" href="{{ route('admin.system.database.index', ['tab' => 'backup']) }}">
            <i class="bx bx-export me-1"></i> Backup & Restore
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request('tab') === 'tenant' ? 'active' : '' }}" href="{{ route('admin.system.database.index', ['tab' => 'tenant']) }}">
            <i class="bx bx-building me-1"></i> Tenant Export
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold" href="#" onclick="event.preventDefault(); document.getElementById('table-select-form').submit();">
            <i class="bx bx-table me-1"></i> Browse Tables
        </a>
        <form id="table-select-form" action="{{ route('admin.system.database.table', '__first__') }}" method="GET" class="d-none">
            <select name="_table" onchange="if(this.value) window.location.href='{{ route('admin.system.database.index') }}/table/'+this.value;">
                @foreach($tablesInfo as $t)
                    <option value="{{ $t['name'] }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
        </form>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request('tab') === 'sql' ? 'active' : '' }}" href="{{ route('admin.system.database.index', ['tab' => 'sql']) }}">
            <i class="bx bx-terminal me-1"></i> SQL Runner
        </a>
    </li>
</ul>

@if(request('tab') === 'backup')
    {{-- Backup & Restore Tab --}}
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-export text-primary me-2"></i> Create Backup</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.system.database.backup') }}" method="POST">
                        @csrf
                        <p class="text-muted font-size-13">Creates a full MySQL dump of the entire database.</p>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bx bx-download me-1"></i> Create Backup Now
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-import text-danger me-2"></i> Restore from Backup</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-danger font-size-13 fw-bold mb-3"><i class="bx bx-error-circle me-1"></i> This will overwrite the current database.</p>
                    <form action="{{ route('admin.system.database.restore') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark font-size-13">Select Backup File</label>
                            <select name="backup_file" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach($backups as $b)
                                    <option value="{{ $b['filename'] }}">{{ $b['filename'] }} ({{ $b['size'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger fw-bold px-4" onclick="return confirm('Restore will overwrite the entire database. Continue?')">
                            <i class="bx bx-import me-1"></i> Restore Database
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-primary me-2"></i> Available Backups</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $b)
                                    <tr>
                                        <td><code class="fw-bold text-dark">{{ $b['filename'] }}</code></td>
                                        <td>{{ $b['size'] }}</td>
                                        <td><small class="text-muted">{{ $b['date'] }}</small></td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.system.database.backup.download', $b['filename']) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-download"></i></a>
                                            <form action="{{ route('admin.system.database.backup.delete', $b['filename']) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this backup?')"><i class="bx bx-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No backups created yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif(request('tab') === 'tenant')
    {{-- Tenant Export Tab --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-building text-primary me-2"></i> Export Tenant Data</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tenant</th>
                            <th>Devices</th>
                            <th>Users</th>
                            <th>Attendance Logs</th>
                            <th>Payments</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $tenant->name }}</span>
                                    <br><small class="text-muted">ID: {{ $tenant->id }} | {{ $tenant->status }}</small>
                                </td>
                                <td>{{ $tenant->devices_count }}</td>
                                <td>{{ $tenant->users_count }}</td>
                                <td>{{ $tenant->attendance_logs_count }}</td>
                                <td>{{ $tenant->payment_logs_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.system.database.export-tenant', $tenant->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-download me-1"></i> Download SQL
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif(request('tab') === 'sql')
    {{-- SQL Runner Tab --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-terminal text-primary me-2"></i> SQL Query Runner</h5>
        </div>
        <div class="card-body p-4">
            @if(session('sql_error'))
                <div class="alert alert-danger border-0 shadow-sm mb-3">
                    <i class="bx bx-error-circle me-1"></i> {{ session('sql_error') }}
                </div>
            @endif

            @if($sqlResult = session('sql_result'))
                <div class="alert alert-{{ $sqlResult['type'] === 'select' ? 'info' : 'success' }} border-0 shadow-sm mb-3">
                    <strong>{{ $sqlResult['type'] === 'select' ? "{$sqlResult['count']} row(s) returned" : "Statement executed, {$sqlResult['affected']} row(s) affected" }}</strong>
                    <pre class="mb-0 mt-1 font-size-12 bg-dark text-light p-2 rounded"><code>{{ $sqlResult['sql'] }}</code></pre>
                </div>

                @if($sqlResult['type'] === 'select' && count($sqlResult['rows']) > 0)
                    <div class="table-responsive mb-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="bg-dark text-white sticky-top">
                                <tr>
                                    @foreach($sqlResult['columns'] as $col)
                                        <th><small>{{ $col }}</small></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sqlResult['rows'] as $row)
                                    <tr>
                                        @foreach($sqlResult['columns'] as $col)
                                            <td><small>{{ is_null($row->$col) ? 'NULL' : $row->$col }}</small></td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($sqlResult['count'] > 100)
                        <p class="text-muted font-size-12">Showing all {{ $sqlResult['count'] }} rows.</p>
                    @endif
                @endif
            @endif

            <form action="{{ route('admin.system.database.execute-sql') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark font-size-13">Enter SQL Query</label>
                    <textarea name="sql" class="form-control font-monospace" rows="6" placeholder="SELECT * FROM users LIMIT 10;" required></textarea>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bx bx-play me-1"></i> Execute
                    </button>
                    <small class="text-muted"><i class="bx bx-info-circle me-1"></i> SELECT queries return results. Other statements show affected row count.</small>
                </div>
            </form>
        </div>
    </div>

@else
    {{-- Overview Tab (default) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-shield-check text-success me-2"></i> Multi-Tenant Data Isolation Audit</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded border">
                        <small class="text-muted font-size-11 fw-bold d-block">Architecture</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $isolationAudit['strategy'] }}</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded border">
                        <small class="text-muted font-size-11 fw-bold d-block">Scoping Trait</small>
                        <code class="d-block font-size-13 fw-bold text-primary mt-1">{{ $isolationAudit['scoping_trait'] }}</code>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded border text-end">
                        <small class="text-muted font-size-11 fw-bold d-block">Isolation Status</small>
                        <span class="badge bg-success font-size-14 mt-1"><i class="bx bx-check-double me-1"></i> {{ $isolationAudit['isolation_status'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-table text-primary me-2"></i> All Tables</h5>
            <form action="{{ route('admin.system.database.table', '__dummy__') }}" method="GET" class="d-inline" id="table-jump-form">
                <select name="table" class="form-select form-select-sm d-inline w-auto" onchange="if(this.value) window.location.href='{{ route('admin.system.database.index') }}/table/'+this.value;">
                    <option value="">Jump to table...</option>
                    @foreach($tablesInfo as $t)
                        <option value="{{ $t['name'] }}">{{ $t['name'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Table</th>
                            <th>Engine</th>
                            <th>Rows</th>
                            <th>Size</th>
                            <th>Collation</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablesInfo as $t)
                            <tr>
                                <td><code class="fw-bold text-dark">{{ $t['name'] }}</code></td>
                                <td><span class="badge bg-light text-dark">{{ $t['engine'] ?? '-' }}</span></td>
                                <td>{{ number_format($t['rows']) }}</td>
                                <td>{{ $t['size'] }}</td>
                                <td><small class="text-muted">{{ $t['collation'] ?? '-' }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.system.database.table', $t['name']) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-show"></i> Browse</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
