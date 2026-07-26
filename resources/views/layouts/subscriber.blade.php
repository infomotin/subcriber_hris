<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Subscriber Portal') | ZKTeco ADMS SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Dedicated Subscriber Sidebar (Carbon Theme) -->
    <div id="vertical-menu" style="border-right: 1px solid rgba(255, 255, 255, 0.04) !important;">
        <div class="navbar-brand-box" style="border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;">
            <a href="{{ route('subscriber.dashboard') }}" class="brand-logo">
                <i class="bx bx-shield-quarter text-emerald-400 font-size-22"></i>
                <span style="font-family: 'Poppins', sans-serif; font-size: 1rem; letter-spacing: 1.5px; font-weight: 700; color: #ffffff;">ADMS PORTAL</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title text-slate-500 font-size-10 font-weight-700 tracking-wider">ADMS Management</li>
            
            <li class="{{ request()->routeIs('subscriber.dashboard') ? 'active' : '' }}">
                <a href="{{ route('subscriber.dashboard') }}">
                    <i class="bx bx-grid-alt"></i>
                    <span>Dashboard & Sub</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.devices.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.devices.index') }}">
                    <i class="bx bx-chip"></i>
                    <span>Biometric Machines</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.attendance.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.attendance.index') }}">
                    <i class="bx bx-calendar-check"></i>
                    <span>Attendance Records</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.users.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.users.index') }}">
                    <i class="bx bx-group"></i>
                    <span>Biometric Users</span>
                </a>
            </li>

            <li class="menu-title text-slate-500 font-size-10 font-weight-700 tracking-wider">Integration</li>

            <li class="{{ request()->routeIs('subscriber.webhook.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.webhook.index') }}">
                    <i class="bx bx-data"></i>
                    <span>Data Push to Server</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.mock.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.mock.viewer') }}">
                    <i class="bx bx-terminal"></i>
                    <span>Mock Inspector</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.plans') ? 'active' : '' }}">
                <a href="{{ route('subscriber.plans') }}">
                    <i class="bx bx-crown"></i>
                    <span>Subscription Quota</span>
                </a>
            </li>

            <li class="menu-title text-slate-500 font-size-10 font-weight-700 tracking-wider">HR Modules</li>
            
            <!-- Setup Submenu -->
            <li>
                <a href="#setupSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.departments.*', 'subscriber.hris.designations.*', 'subscriber.hris.shifts.*', 'subscriber.hris.master.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['calendar', 'addresses', 'other'])) ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-cog"></i>
                        <span>Setup</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.departments.*', 'subscriber.hris.designations.*', 'subscriber.hris.shifts.*', 'subscriber.hris.master.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['calendar', 'addresses', 'other'])) ? 'show' : '' }}" id="setupSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.master.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.master.index') }}" class="font-size-13 py-2 text-primary fw-medium">
                            <i class="bx bx-slider-alt me-2 text-primary font-size-15"></i> Master Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.departments.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.departments.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-git-branch me-2"></i> Department Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.designations.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.designations.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-user-voice me-2"></i> Designation Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.shifts.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.shifts.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-time-five me-2"></i> Shift Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'calendar' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'calendar') }}" class="font-size-13 py-2">
                            <i class="bx bx-calendar-event me-2"></i> Calendar Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'addresses' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'addresses') }}" class="font-size-13 py-2">
                            <i class="bx bx-map-pin me-2"></i> Address Setup
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'other' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'other') }}" class="font-size-13 py-2">
                            <i class="bx bx-dots-horizontal-rounded me-2"></i> Other Setup
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Databases Submenu -->
            <li>
                <a href="#databasesSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.employees.*', 'subscriber.hris.kpis.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['verification', 'increments'])) ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-data"></i>
                        <span>Databases</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.employees.*', 'subscriber.hris.kpis.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['verification', 'increments'])) ? 'show' : '' }}" id="databasesSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.employees.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.employees.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-user-plus me-2"></i> Employee Entry
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'verification' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'verification') }}" class="font-size-13 py-2">
                            <i class="bx bx-shield-quarter me-2"></i> Data Verification
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'increments' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'increments') }}" class="font-size-13 py-2">
                            <i class="bx bx-trending-up me-2"></i> Increments
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.kpis.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.kpis.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-bar-chart-alt-2 me-2"></i> KPI Goals
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Tools Submenu -->
            <li>
                <a href="#toolsSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.leaves.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['applications', 'advances'])) ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-wrench"></i>
                        <span>Tools</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.leaves.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['applications', 'advances'])) ? 'show' : '' }}" id="toolsSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'applications' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'applications') }}" class="font-size-13 py-2">
                            <i class="bx bx-receipt me-2"></i> Applications
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.leaves.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.leaves.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-calendar-exclamation me-2"></i> Leaves Management
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'advances' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'advances') }}" class="font-size-13 py-2">
                            <i class="bx bx-credit-card-front me-2"></i> Salary Advances
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Reports Submenu -->
            <li>
                <a href="#reportsSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ (request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'reports') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-file"></i>
                        <span>Reports</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ (request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'reports') ? 'show' : '' }}" id="reportsSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'reports' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.general.show', 'reports') }}" class="font-size-13 py-2">
                            <i class="bx bx-file-blank me-2"></i> Related Reports
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-title text-slate-500 font-size-10 font-weight-700 tracking-wider">Payroll Module</li>
            <li>
                <a href="#" class="opacity-60 cursor-not-allowed" onclick="return false;">
                    <i class="bx bx-money text-slate-500"></i>
                    <span>Salary Sheets</span>
                    <span class="badge bg-soft-primary text-primary ms-auto font-size-9">Soon</span>
                </a>
            </li>
            <li>
                <a href="#" class="opacity-60 cursor-not-allowed" onclick="return false;">
                    <i class="bx bx-receipt text-slate-500"></i>
                    <span>Generate Payslips</span>
                    <span class="badge bg-soft-primary text-primary ms-auto font-size-9">Soon</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navigation (Glass panel) -->
    <header id="page-topbar">
        <div class="d-flex align-items-center gap-2.5">
            <button type="button" class="btn btn-sm px-2 font-size-18 header-item waves-effect border-0" id="sidebar-toggle" style="min-height: auto; height: 38px; width: 38px; border-radius: 50% !important; background: rgba(95, 90, 246, 0.05);">
                <i class="bx bx-menu-alt-left text-primary" style="font-size: 1.4rem;"></i>
            </button>
            <span class="badge bg-primary px-3 py-2 font-size-11 rounded-pill" style="border: 1px solid rgba(95, 90, 246, 0.15) !important;">
                <i class="bx bx-building-house me-1 align-middle"></i> {{ auth()->user()->tenant->name ?? 'Organization' }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent py-1 px-2" type="button" data-bs-toggle="dropdown" style="border-radius: 30px;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Subscriber') }}&background=5f5af6&color=fff" class="rounded-circle border" width="34" height="34" alt="User">
                    <span class="fw-bold text-slate-700 font-size-13 d-none d-sm-inline">{{ auth()->user()->name ?? 'Subscriber Account' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2">
                    <li><a class="dropdown-item py-2" href="{{ route('subscriber.dashboard') }}"><i class="bx bx-tachometer me-2 font-size-16"></i> My Dashboard</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('subscriber.webhook.index') }}"><i class="bx bx-send me-2 font-size-16"></i> Data Push Config</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('subscriber.plans') }}"><i class="bx bx-crown me-2 font-size-16"></i> Subscription Plan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start py-2">
                                <i class="bx bx-power-off me-2 font-size-16"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <div class="main-content d-flex flex-column justify-content-between">
        <div class="content-body flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #ecfdf5 !important; border-left: 4px solid #10b981 !important; color: #065f46 !important; border-radius: 8px !important;">
                    <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fff1f2 !important; border-left: 4px solid #f43f5e !important; color: #9f1239 !important; border-radius: 8px !important;">
                    <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Tiny Footer -->
        <footer class="footer mt-5 py-3 border-top text-slate-500 text-xs bg-white border-slate-100" style="border-radius: 12px; margin-top: 3rem !important;">
            <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>Subscriber Portal</strong> &nbsp;•&nbsp; {{ auth()->user()->tenant->name ?? 'Organization' }}
                    <span class="badge bg-soft-success text-success ms-2 rounded-pill font-size-9">{{ strtoupper(auth()->user()->tenant->status ?? 'ACTIVE') }} SUBSCRIBER</span>
                </div>
                <div>
                    <span>Powered by <a href="https://nexozaint.com" target="_blank" class="fw-bold text-primary text-decoration-none">Nexozaint</a></span>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
