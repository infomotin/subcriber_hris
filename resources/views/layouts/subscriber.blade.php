@php
    $tenantTheme = \App\Models\TenantConfig::getGroup('theme');
    $primaryColor = $tenantTheme['primary_color'] ?? '#5f5af6';
    $fontFamily = $tenantTheme['font_family'] ?? 'Poppins';
    $sidebarStyle = $tenantTheme['sidebar_style'] ?? 'dark';
    $subscriberConfig = \App\Models\TenantConfig::getGroup('subscriber');
    $companyLogo = $subscriberConfig['company_logo'] ?? null;
    $tenant = auth()->user()->tenant ?? null;
    $tenantName = $tenant->name ?? 'ADMS Portal';
    $demoLogo = 'https://ui-avatars.com/api/?name=' . urlencode(substr($tenantName, 0, 2)) . '&background=' . ltrim($primaryColor, '#') . '&color=fff&size=120&bold=true&font-size=0.45';
@endphp
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

    <style>
        :root { --primary: {{ $primaryColor }}; --font: '{{ $fontFamily }}', sans-serif; }
        body, .font-family-custom { font-family: var(--font) !important; }
        .sidebar-menu .active > a, .sidebar-menu .active > a i { color: {{ $primaryColor }} !important; }
        #vertical-menu {{ $sidebarStyle === 'light' ? 'background: #ffffff !important; border-right: 1px solid #e2e8f0;' : '' }}
        #vertical-menu .sidebar-menu .menu-title {{ $sidebarStyle === 'light' ? 'color: #64748b !important;' : '' }}
        #vertical-menu .sidebar-menu li a {{ $sidebarStyle === 'light' ? 'color: #475569 !important;' : '' }}
        #vertical-menu .sidebar-menu li a i {{ $sidebarStyle === 'light' ? 'color: #94a3b8 !important;' : '' }}
        #vertical-menu .sidebar-menu li a:hover {{ $sidebarStyle === 'light' ? 'background: #f1f5f9 !important; color: #0f172a !important;' : '' }}
        .badge.bg-primary, .btn-primary, .text-primary { color: {{ $primaryColor }} !important; }
        .bg-primary { background-color: {{ $primaryColor }} !important; }

        /* Global Compact Design System */
        .content-body { padding: 0.75rem 1.25rem; }
        .content-body > .d-flex { margin-bottom: 0.5rem !important; }
        .content-body h4 { font-size: 0.9rem; margin-bottom: 0.1rem; }
        .content-body h5 { font-size: 0.85rem; margin-bottom: 0.1rem; }
        .content-body p.text-muted { font-size: 0.7rem; margin-bottom: 0.2rem; }
        .content-body .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06); margin-bottom: 0.75rem; }
        .content-body .card-header { padding: 0.4rem 0.75rem; background: #fff; border-bottom: 1px solid #f1f5f9; }
        .content-body .card-header h5, .content-body .card-header h6 { font-size: 0.8rem; margin: 0; }
        .content-body .card-body { padding: 0.5rem 0.75rem; }
        .content-body .table { font-size: 0.72rem; }
        .content-body .table thead th { padding: 0.35rem 0.5rem; font-size: 0.68rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
        .content-body .table tbody td { padding: 0.3rem 0.5rem; vertical-align: middle; border-bottom: 1px solid #f8fafc; }
        .content-body .table tbody tr:hover { background-color: #f8fafc; }
        .content-body .badge { font-size: 0.6rem; padding: 0.2em 0.45em; }
        .content-body .btn-sm { font-size: 0.68rem; padding: 0.15rem 0.4rem; }
        .content-body .form-control, .content-body .form-select { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .content-body .form-label { font-size: 0.7rem; margin-bottom: 0.15rem; }
        .content-body .mb-3 { margin-bottom: 0.5rem !important; }
        .content-body .mb-4 { margin-bottom: 0.6rem !important; }
        .content-body .mb-2 { margin-bottom: 0.35rem !important; }
        .content-body .py-3 { padding-top: 0.4rem !important; padding-bottom: 0.4rem !important; }
        .content-body .px-3 { padding-left: 0.6rem !important; padding-right: 0.6rem !important; }
        .content-body .g-3, .content-body .g-4 { --bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem; }
        .content-body .row.g-3 > [class*="col-"] { padding-left: calc(var(--bs-gutter-x) * 0.5); padding-right: calc(var(--bs-gutter-x) * 0.5); }
        .content-body .alert { padding: 0.4rem 0.75rem; font-size: 0.72rem; margin-bottom: 0.5rem; }
        .content-body .pagination { margin-top: 0.5rem; }
        .content-body .page-link { padding: 0.2rem 0.5rem; font-size: 0.7rem; }
        .content-body code { font-size: 0.65rem; }
        .content-body .avatar-circle { width: 28px; height: 28px; font-size: 0.55rem; }
        .content-body .modal-header { padding: 0.5rem 0.75rem; }
        .content-body .modal-body { padding: 0.5rem 0.75rem; }
        .content-body .modal-footer { padding: 0.4rem 0.75rem; }
        .content-body .modal-title { font-size: 0.85rem; }
        .content-body .dropdown-menu { font-size: 0.72rem; }
        .content-body .dropdown-item { padding: 0.3rem 0.75rem; font-size: 0.72rem; }
        .content-body .form-check { margin-bottom: 0.3rem; }
        .content-body .form-check-label { font-size: 0.72rem; }
        .content-body pre { font-size: 0.65rem; padding: 0.5rem; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Subscriber Sidebar -->
    <div id="vertical-menu">
        <div class="navbar-brand-box">
            <a href="{{ route('subscriber.dashboard') }}" class="brand-logo d-flex align-items-center gap-2">
                @if($companyLogo && file_exists(public_path('storage/' . $companyLogo)))
                    <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $tenantName }}" style="height: 36px; width: auto; border-radius: 6px; object-fit: contain;">
                @else
                    <img src="{{ $demoLogo }}" alt="{{ $tenantName }}" style="height: 36px; width: 36px; border-radius: 8px; object-fit: cover;">
                @endif
                <span style="font-family: 'Poppins', sans-serif; font-size: 0.85rem; letter-spacing: 1px; font-weight: 700; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;">{{ $tenantName }}</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title">ADMS Management</li>

            <li class="{{ request()->routeIs('subscriber.hr-dashboard') ? 'active' : '' }}">
                <a href="{{ route('subscriber.hr-dashboard') }}">
                    <i class="bx bx-grid-alt"></i>
                    <span>Dashboard & Sub</span>
                </a>
            </li>

            <li class="menu-title">HR Modules</li>

            <!-- Setup Submenu -->
            <li>
                <a href="#setupSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.departments.*', 'subscriber.hris.designations.*', 'subscriber.hris.shifts.*', 'subscriber.hris.master.*', 'subscriber.hris.increment-rules.*', 'subscriber.hris.movement-types.*', 'subscriber.hris.bill-types.*', 'subscriber.hris.bill-purposes.*', 'subscriber.hris.system-parameters.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['calendar'])) ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-cog"></i>
                        <span>Setup</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.departments.*', 'subscriber.hris.designations.*', 'subscriber.hris.shifts.*', 'subscriber.hris.master.*', 'subscriber.hris.increment-rules.*', 'subscriber.hris.movement-types.*', 'subscriber.hris.bill-types.*', 'subscriber.hris.bill-purposes.*', 'subscriber.hris.system-parameters.*') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['calendar'])) ? 'show' : '' }}" id="setupSubmenu">
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
                    <li class="{{ request()->routeIs('subscriber.hris.system-parameters.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.system-parameters.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-slider me-2"></i> System Parameters
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.increment-rules.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.increment-rules.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-rule me-2"></i> Increment Rules
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.movement-types.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.movement-types.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-transfer me-2"></i> Movement Types
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.bill-types.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.bill-types.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-category me-2"></i> Bill Types
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.bill-purposes.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.bill-purposes.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-target-lock me-2"></i> Bill Purposes
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.advance-types.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.advance-types.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-category me-2"></i> Advance Types
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.advance-sources.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.advance-sources.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-dollar me-2"></i> Advance Sources
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Databases Submenu -->
            <li>
                <a href="#databasesSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.employees.*', 'subscriber.hris.kpis.*', 'subscriber.hris.promotions.*', 'subscriber.hris.increments.*', 'subscriber.hris.leaves.apply', 'subscriber.hris.leaves.balance') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['verification', 'increments'])) ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-data"></i>
                        <span>Databases</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.employees.*', 'subscriber.hris.kpis.*', 'subscriber.hris.promotions.*', 'subscriber.hris.increments.*', 'subscriber.hris.leaves.apply', 'subscriber.hris.leaves.balance') || (request()->routeIs('subscriber.hris.general.show') && in_array(request()->route('module'), ['verification', 'increments'])) ? 'show' : '' }}" id="databasesSubmenu">
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
                    <li class="{{ request()->routeIs('subscriber.hris.increments.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.increments.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-trending-up me-2"></i> Increments
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.leaves.apply', 'subscriber.hris.leaves.balance') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.leaves.apply') }}" class="font-size-13 py-2">
                            <i class="bx bx-calendar-check me-2"></i> Leave Application
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.promotions.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.promotions.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-trending-up me-2"></i> Promotions
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
                <a href="#toolsSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.leaves.*', 'subscriber.hris.movement-passes.*', 'subscriber.hris.bills.*', 'subscriber.hris.advances.*') || (request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'advances') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-wrench"></i>
                        <span>Tools</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.leaves.*', 'subscriber.hris.movement-passes.*', 'subscriber.hris.bills.*', 'subscriber.hris.advances.*') || (request()->routeIs('subscriber.hris.general.show') && request()->route('module') == 'advances') ? 'show' : '' }}" id="toolsSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.bills.apply') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.bills.apply') }}" class="font-size-13 py-2">
                            <i class="bx bx-receipt me-2"></i> Bill Submission
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.bills.index') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.bills.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-list-check me-2"></i> My Bills
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.bills.approval') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.bills.approval') }}" class="font-size-13 py-2">
                            <i class="bx bx-clipboard me-2"></i> Bill Approval
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.leaves.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.leaves.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-calendar-exclamation me-2"></i> Leaves Management
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.movement-passes.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.movement-passes.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-transfer-alt me-2"></i> Movement Passes
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.advances.apply') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.advances.apply') }}" class="font-size-13 py-2">
                            <i class="bx bx-dollar me-2"></i> Apply Advance
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.advances.index') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.advances.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-list-check me-2"></i> My Advances
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.advances.approval') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.advances.approval') }}" class="font-size-13 py-2">
                            <i class="bx bx-clipboard me-2"></i> Advance Approval
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

            <!-- Roles & Permissions -->
            @php
                $isAdmin = false;
                try {
                    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
                } catch (\Exception $e) {
                    $isAdmin = false;
                }
            @endphp
            @if($isAdmin)
            <li>
                <a href="#rolesSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.users.*', 'subscriber.hris.roles.*', 'subscriber.hris.permissions.*') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-shield-quarter"></i>
                        <span>Roles & Permissions</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.users.*', 'subscriber.hris.roles.*', 'subscriber.hris.permissions.*') ? 'show' : '' }}" id="rolesSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.users.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.users.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-group me-2"></i> Users
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.roles.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-shield me-2"></i> Roles
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.permissions.*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.permissions.index') }}" class="font-size-13 py-2">
                            <i class="bx bx-lock me-2"></i> Permissions
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- System Setup -->
            <li>
                <a href="#systemSetupSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.hris.setup.*', 'subscriber.adms.*', 'subscriber.subscription.*') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-slider"></i>
                        <span>System Setup</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.hris.setup.*', 'subscriber.adms.*', 'subscriber.subscription.*') ? 'show' : '' }}" id="systemSetupSubmenu">
                    <li class="{{ request()->routeIs('subscriber.hris.setup.subscriber') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.setup.subscriber') }}" class="font-size-13 py-2">
                            <i class="bx bx-building me-2"></i> Subscriber Info
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.setup.theme') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.setup.theme') }}" class="font-size-13 py-2">
                            <i class="bx bx-palette me-2"></i> System Theme
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.setup.mail') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.setup.mail') }}" class="font-size-13 py-2">
                            <i class="bx bx-envelope me-2"></i> Mail Config
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.setup.sms') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.setup.sms') }}" class="font-size-13 py-2">
                            <i class="bx bx-message me-2"></i> SMS Gateway
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.hris.setup.backup') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.hris.setup.backup') }}" class="font-size-13 py-2">
                            <i class="bx bx-data me-2"></i> Database Backup
                        </a>
                    </li>

                    <!-- ADMS Submenu -->
                    <li>
                        <a href="#admsSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.adms.*') ? '' : 'collapsed' }}">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-shield-quarter me-2"></i>
                                <span>ADMS</span>
                            </div>
                            <i class="bx bx-chevron-down font-size-14"></i>
                        </a>
                        <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.adms.*') ? 'show' : '' }}" id="admsSubmenu">
                            <li class="{{ request()->routeIs('subscriber.devices.*') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.devices.index') }}" class="font-size-13 py-2">
                                    <i class="bx bx-chip me-2"></i> Biometric Machines
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('subscriber.adms.overview') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.adms.overview') }}" class="font-size-13 py-2">
                                    <i class="bx bx-grid-alt me-2"></i> Show Subscriber Dashboard
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('subscriber.adms.endpoint') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.adms.endpoint') }}" class="font-size-13 py-2">
                                    <i class="bx bx-broadcast me-2"></i> Dedicated ZKTeco Machine ADMS Endpoint
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('subscriber.adms.punch-logs') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.adms.punch-logs') }}" class="font-size-13 py-2">
                                    <i class="bx bx-time me-2"></i> Realtime Punch Logs Feed
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('subscriber.adms.handshake-test') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.adms.handshake-test') }}" class="font-size-13 py-2">
                                    <i class="bx bx-test-tube me-2"></i> Handshake & Protocol Test
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('subscriber.adms.listener-config') ? 'active' : '' }}">
                                <a href="{{ route('subscriber.adms.listener-config') }}" class="font-size-13 py-2">
                                    <i class="bx bx-server me-2"></i> Listener & Server Config
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Subscription & Account Overview -->
                    <li class="{{ request()->routeIs('subscriber.subscription.overview') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.subscription.overview') }}" class="font-size-13 py-2">
                            <i class="bx bx-crown me-2"></i> Subscription & Account Overview
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-title">Payroll Module</li>

            <!-- Setup Submenu -->
            <li>
                <a href="#payrollSetupSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.payroll.setup', 'subscriber.payroll.salary-role*') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-cog"></i>
                        <span>Setup</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.payroll.setup', 'subscriber.payroll.salary-role*') ? 'show' : '' }}" id="payrollSetupSubmenu">
                    <li class="{{ request()->routeIs('subscriber.payroll.salary-role*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.salary-role') }}" class="font-size-13 py-2 text-primary fw-medium">
                            <i class="bx bx-percentage me-2 text-primary font-size-15"></i> Salary Role
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Databases Submenu -->
            <li>
                <a href="#payrollDbSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.payroll.database', 'subscriber.payroll.process-attendance*', 'subscriber.payroll.punch-data-upload*') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-data"></i>
                        <span>Databases</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.payroll.database', 'subscriber.payroll.process-attendance*', 'subscriber.payroll.punch-data-upload*') ? 'show' : '' }}" id="payrollDbSubmenu">
                    <li class="{{ request()->routeIs('subscriber.payroll.database') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.database') }}" class="font-size-13 py-2">
                            <i class="bx bx-coin-stack me-2"></i> Salary Structures
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.payroll.punch-data-upload*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.punch-data-upload') }}" class="font-size-13 py-2">
                            <i class="bx bx-upload me-2"></i> Upload Punch Data
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.payroll.process-attendance*') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.process-attendance') }}" class="font-size-13 py-2">
                            <i class="bx bx-check-double me-2"></i> Process Attendance
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Tools Submenu -->
            <li>
                <a href="#payrollToolsSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.payroll.salary-generate', 'subscriber.payroll.payslip') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-wrench"></i>
                        <span>Tools</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.payroll.salary-generate', 'subscriber.payroll.payslip') ? 'show' : '' }}" id="payrollToolsSubmenu">
                    <li class="{{ request()->routeIs('subscriber.payroll.salary-generate') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.salary-generate') }}" class="font-size-13 py-2">
                            <i class="bx bx-calculator me-2"></i> Generate Salary
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('subscriber.payroll.payslip') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.payslip') }}" class="font-size-13 py-2">
                            <i class="bx bx-receipt me-2"></i> Generate Payslips
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Reports Submenu -->
            <li>
                <a href="#payrollReportSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center {{ request()->routeIs('subscriber.payroll.report*') ? '' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-file"></i>
                        <span>Reports</span>
                    </div>
                    <i class="bx bx-chevron-down font-size-14"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 {{ request()->routeIs('subscriber.payroll.report*') ? 'show' : '' }}" id="payrollReportSubmenu">
                    <li class="{{ request('tab') === 'overview' || !request('tab') ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'overview']) }}" class="font-size-13 py-2">
                            <i class="bx bx-bar-chart-alt-2 me-2"></i> Visual Overview
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'employee' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'employee']) }}" class="font-size-13 py-2">
                            <i class="bx bx-user me-2"></i> Employee Report
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'department' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'department']) }}" class="font-size-13 py-2">
                            <i class="bx bx-building me-2"></i> Department Report
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'punch' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'punch']) }}" class="font-size-13 py-2">
                            <i class="bx bx-data me-2"></i> Punch Report
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'leave' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'leave']) }}" class="font-size-13 py-2">
                            <i class="bx bx-calendar me-2"></i> Leave Report
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'timecard' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'timecard']) }}" class="font-size-13 py-2">
                            <i class="bx bx-time me-2"></i> Time Card
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'salary' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'salary']) }}" class="font-size-13 py-2">
                            <i class="bx bx-money me-2"></i> Salary Sheet
                        </a>
                    </li>
                    <li class="{{ request('tab') === 'advance' ? 'active' : '' }}">
                        <a href="{{ route('subscriber.payroll.report', ['tab' => 'advance']) }}" class="font-size-13 py-2">
                            <i class="bx bx-dollar me-2"></i> Advance Report
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Top Navigation -->
    <header id="page-topbar">
        <div class="d-flex align-items-center gap-2.5">
            <button type="button" class="btn btn-sm px-2 font-size-18 header-item waves-effect border-0" id="sidebar-toggle">
                <i class="bx bx-menu-alt-left text-primary" style="font-size: 1.4rem;"></i>
            </button>
            <span class="badge bg-primary px-3 py-2 font-size-11 rounded-pill d-none d-sm-inline-flex">
                @if($companyLogo && file_exists(public_path('storage/' . $companyLogo)))
                    <img src="{{ asset('storage/' . $companyLogo) }}" alt="" style="height: 16px; width: auto; border-radius: 3px; margin-right: 6px; object-fit: contain;">
                @else
                    <i class="bx bx-building-house me-1 align-middle"></i>
                @endif
                {{ $tenantName }}
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
                    <li><a class="dropdown-item py-2" href="{{ route('subscriber.hris.setup.subscriber') }}"><i class="bx bx-slider me-2 font-size-16"></i> System Setup</a></li>
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

            @yield('modals')
        </div>

        <!-- Footer -->
        <footer class="footer mt-5 py-3 border-top text-slate-500 text-xs bg-white border-slate-100" style="border-radius: 12px; margin-top: 3rem !important;">
            <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>Subscriber Portal</strong> &nbsp;•&nbsp; {{ $tenantName }}
                    <span class="badge bg-soft-success text-success ms-2 rounded-pill font-size-9">{{ strtoupper($tenant->status ?? 'ACTIVE') }} SUBSCRIBER</span>
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
