@extends('layouts.subscriber')

@section('title', 'ADMS Overview & Portal Control')

@section('content')
<style>
    .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.95);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">ADMS Management</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Overview & Portal Control</h4>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Machine Quota</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $devicesCount }} <span class="font-size-16 text-muted">/ {{ $tenant->max_devices }}</span></h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600">
                    <i class="bx bx-chip"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Online Machines</span>
                    <h3 class="mt-2 mb-0 fw-bold text-success" id="statOnlineDevices" style="font-family: 'Poppins', sans-serif;">{{ $onlineDevicesCount }}</h3>
                </div>
                <div class="stat-icon bg-emerald-50 border border-emerald-100 text-emerald-600">
                    <i class="bx bx-wifi"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Today's Punches</span>
                    <h3 class="mt-2 mb-0 fw-bold text-sky-600" id="statTodayPunches" style="font-family: 'Poppins', sans-serif;">{{ $todayPunches }}</h3>
                </div>
                <div class="stat-icon bg-sky-50 border border-sky-100 text-sky-600">
                    <i class="bx bx-fingerprint"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Biometric Users</span>
                    <h3 class="mt-2 mb-0 fw-bold text-amber-600" id="statUsersCount" style="font-family: 'Poppins', sans-serif;">{{ $usersCount }}</h3>
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600">
                    <i class="bx bx-user-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">
                    <i class="bx bx-info-circle text-primary me-1 font-size-18 align-middle"></i> Quick Links
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('subscriber.adms.endpoint') }}" class="text-decoration-none">
                            <div class="border rounded-3 p-3 text-center hover-shadow-sm" style="transition: all 0.2s;">
                                <i class="bx bx-broadcast font-size-28 text-primary d-block mb-2"></i>
                                <span class="fw-medium font-size-13 text-slate-700">ADMS Endpoint</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('subscriber.adms.punch-logs') }}" class="text-decoration-none">
                            <div class="border rounded-3 p-3 text-center hover-shadow-sm" style="transition: all 0.2s;">
                                <i class="bx bx-time font-size-28 text-success d-block mb-2"></i>
                                <span class="fw-medium font-size-13 text-slate-700">Punch Logs Feed</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('subscriber.devices.index') }}" class="text-decoration-none">
                            <div class="border rounded-3 p-3 text-center hover-shadow-sm" style="transition: all 0.2s;">
                                <i class="bx bx-chip font-size-28 text-info d-block mb-2"></i>
                                <span class="fw-medium font-size-13 text-slate-700">Biometric Machines</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('subscriber.attendance.index') }}" class="text-decoration-none">
                            <div class="border rounded-3 p-3 text-center hover-shadow-sm" style="transition: all 0.2s;">
                                <i class="bx bx-calendar-check font-size-28 text-warning d-block mb-2"></i>
                                <span class="fw-medium font-size-13 text-slate-700">Attendance Records</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection