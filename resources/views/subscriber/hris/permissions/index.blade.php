@extends('layouts.subscriber')

@section('title', 'Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-check-shield text-primary me-2"></i> All Permissions</h4>
        <p class="text-muted font-size-13 mb-0">Global permission definitions available for role assignment.</p>
    </div>
    <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back me-1"></i> Back to Roles
    </a>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2"><i class="bx bx-check-shield"></i></div>
                    <div><span class="text-muted font-size-11">Total Permissions</span><br><span class="fw-bold font-size-14">{{ $permissions->flatten()->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2"><i class="bx bx-folder"></i></div>
                    <div><span class="text-muted font-size-11">Permission Groups</span><br><span class="fw-bold font-size-14">{{ $permissions->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2"><i class="bx bx-shield"></i></div>
                    <div><span class="text-muted font-size-11">Roles</span><br><span class="fw-bold font-size-14">{{ $roles->count() }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($permissions->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-4">
            <i class="bx bx-check-shield font-size-36 text-muted d-block mb-2"></i>
            <h6 class="fw-bold text-dark">No Permissions Defined</h6>
            <p class="text-muted mb-0 font-size-13">Run <code>php artisan db:seed --class=PermissionSeeder</code> to seed permissions.</p>
        </div>
    </div>
@else
    @foreach($permissions as $group => $perms)
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-header bg-white border-bottom py-2 px-3">
                <h6 class="fw-bold mb-0 font-size-13">
                    <i class="bx bx-folder text-primary me-2"></i> {{ $group ?? 'General' }}
                    <span class="badge bg-primary ms-1 font-size-10">{{ $perms->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-1">
                    @foreach($perms as $perm)
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;">
                                <i class="bx bx-check-circle text-success font-size-14"></i>
                                <span class="font-size-11 text-dark">{{ $perm->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
