@extends('layouts.system_admin')

@section('title', 'Database & Multi-Tenant Isolation Audit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-data text-warning me-2 font-size-22"></i> Database Manager & Tenant Data Isolation Audit</h4>
        <p class="text-muted font-size-13 mb-0">Database health, table record sizes, backup snapshot controls, and single-database multi-tenant data isolation status.</p>
    </div>
    <form action="{{ route('admin.system.database.backup') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
            <i class="bx bx-download me-1"></i> Trigger Database Backup Snapshot
        </button>
    </form>
</div>

<!-- Multi-Tenant Isolation Architecture Audit Banner -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-shield-check text-success me-2"></i> Single-Database Multi-Tenant Data Isolation Audit</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-4">
                <div class="bg-light p-3 rounded border">
                    <small class="text-muted font-size-11 font-uppercase fw-bold d-block">Architecture Strategy</small>
                    <h6 class="fw-bold text-dark mb-0">{{ $isolationAudit['strategy'] }}</h6>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light p-3 rounded border">
                    <small class="text-muted font-size-11 font-uppercase fw-bold d-block">Automatic Scoping Trait</small>
                    <code class="d-block font-size-13 font-bold text-primary mt-1">{{ $isolationAudit['scoping_trait'] }}</code>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light p-3 rounded border text-end">
                    <small class="text-muted font-size-11 font-uppercase fw-bold d-block">Isolation Status</small>
                    <span class="badge bg-success font-size-14 mt-1"><i class="bx bx-check-double me-1"></i> {{ $isolationAudit['isolation_status'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Statistics Matrix -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-table text-primary me-2"></i> Database Table Sizes & Record Counts</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Table Name</th>
                        <th>Record Count</th>
                        <th>Multi-Tenant Scoped</th>
                        <th>Data Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tablesStats as $tableName => $count)
                        <tr>
                            <td><code class="fw-bold text-dark font-size-14">{{ $tableName }}</code></td>
                            <td><span class="badge bg-dark font-size-13">{{ number_format($count) }} Records</span></td>
                            <td>
                                @if(in_array($tableName, ['devices', 'zkteco_users', 'attendance_logs', 'tenant_webhook_settings', 'tenant_push_logs']))
                                    <span class="badge bg-success"><i class="bx bx-check me-1"></i> Tenant Scoped (tenant_id)</span>
                                @else
                                    <span class="badge bg-secondary">Global / System Table</span>
                                @endif
                            </td>
                            <td><span class="badge bg-soft-success text-success">Healthy</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
