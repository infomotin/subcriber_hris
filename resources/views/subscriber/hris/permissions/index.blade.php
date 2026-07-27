@extends('layouts.subscriber')

@section('title', 'Permissions')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Roles & Permissions</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-lock text-primary me-1.5 align-middle font-size-26"></i>All Permissions
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;">
        <i class="bx bx-arrow-back me-1"></i> Back to Roles
    </a>
</div>

@if($permissions->isEmpty())
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body text-center py-5">
            <i class="bx bx-lock font-size-48 text-muted d-block mb-2"></i>
            <h5 class="fw-bold text-slate-800">No Permissions Defined</h5>
            <p class="text-muted mb-0">Permissions are typically created by running: <code>php artisan permission:cache-reset</code></p>
        </div>
    </div>
@else
    @foreach($permissions as $group => $perms)
        <div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-slate-800 mb-3" style="font-family:'Poppins',sans-serif;">
                    <i class="bx bx-folder text-primary me-1.5 align-middle font-size-18"></i> {{ $group ?? 'General' }}
                    <span class="badge bg-soft-primary text-primary ms-2 font-size-10">{{ $perms->count() }}</span>
                </h6>
                <div class="row g-2">
                    @foreach($perms as $perm)
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;">
                                <i class="bx bx-check-shield text-success font-size-14"></i>
                                <span class="font-size-12 text-slate-700">{{ $perm->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
