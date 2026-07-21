<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'System Admin Panel') | ZKTeco ADMS SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        :root {
            --sk-sidebar-width: 260px;
            --sk-sidebar-bg: #1a1d21;
            --sk-sidebar-color: #98a6ad;
            --sk-sidebar-active: #ffffff;
            --sk-sidebar-active-bg: #262a30;
            --sk-header-height: 70px;
            --sk-body-bg: #f4f5f8;
            --sk-primary: #556ee6;
        }

        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            background-color: var(--sk-body-bg);
            color: #495057;
            overflow-x: hidden;
        }

        #vertical-menu {
            width: var(--sk-sidebar-width);
            background: var(--sk-sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1001;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.05);
            overflow-y: auto;
        }

        .navbar-brand-box {
            height: var(--sk-header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background: #14171a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-logo {
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo i {
            color: #f1b44c;
            font-size: 1.6rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        .menu-title {
            padding: 14px 20px 6px 20px;
            letter-spacing: .05em;
            font-size: 10px;
            text-transform: uppercase;
            color: #6a7187;
            font-weight: 700;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.5rem;
            color: var(--sk-sidebar-color);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a i {
            font-size: 1.25rem;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            color: var(--sk-sidebar-active);
            background: var(--sk-sidebar-active-bg);
            border-left-color: #f1b44c;
        }

        #page-topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sk-sidebar-width);
            height: var(--sk-header-height);
            background: #ffffff;
            z-index: 1000;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }

        .main-content {
            margin-left: var(--sk-sidebar-width);
            margin-top: var(--sk-header-height);
            padding: 2rem 1.5rem;
            min-height: calc(100vh - var(--sk-header-height));
        }

        .badge-system {
            background-color: #f1b44c;
            color: #111;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Dedicated System Admin Sidebar -->
    <div id="vertical-menu">
        <div class="navbar-brand-box">
            <a href="{{ route('admin.system.dashboard') }}" class="brand-logo">
                <i class="bx bx-shield-quarter"></i>
                <span>System Admin Panel</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title">Main Control</li>
            
            <li class="{{ request()->routeIs('admin.system.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.system.dashboard') }}">
                    <i class="bx bx-home-circle"></i>
                    <span>System Overview</span>
                </a>
            </li>

            <li class="menu-title">User & Access Control</li>

            <li class="{{ request()->routeIs('admin.system.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.users.index') }}">
                    <i class="bx bx-user-voice"></i>
                    <span>User Manager (SaaS)</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.system.roles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.roles.index') }}">
                    <i class="bx bx-key"></i>
                    <span>Role & Permissions</span>
                </a>
            </li>

            <li class="menu-title">Content & Infrastructure</li>

            <li class="{{ request()->routeIs('admin.system.website.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.website.index') }}">
                    <i class="bx bx-globe"></i>
                    <span>Website Manager</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.system.monitoring.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.monitoring.index') }}">
                    <i class="bx bx-line-chart"></i>
                    <span>System Monitoring</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.system.database.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.database.index') }}">
                    <i class="bx bx-data"></i>
                    <span>Databases Audit</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.system.security.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.security.index') }}">
                    <i class="bx bx-shield-x"></i>
                    <span>System Security Audit</span>
                </a>
            </li>

            <li class="menu-title">Integration & Hardware</li>

            <li class="{{ request()->routeIs('admin.system.gateways.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.gateways.index') }}">
                    <i class="bx bx-cog"></i>
                    <span>Gateway Configuration</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.system.network.*') ? 'active' : '' }}">
                <a href="{{ route('admin.system.network.index') }}">
                    <i class="bx bx-wifi"></i>
                    <span>Network & ADMS</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navigation -->
    <header id="page-topbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge badge-system px-3 py-2 font-size-12">
                <i class="bx bx-shield-quarter me-1"></i> System Admin Privilege
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'System Admin') }}&background=f1b44c&color=000" class="rounded-circle" width="32" height="32" alt="User">
                    <span class="fw-bold text-dark font-size-14">{{ auth()->user()->name ?? 'System Admin' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.system.dashboard') }}"><i class="bx bx-home-circle me-2"></i> System Overview</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.system.users.index') }}"><i class="bx bx-user-voice me-2"></i> User Manager</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.system.monitoring.index') }}"><i class="bx bx-line-chart me-2"></i> System Monitoring</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                <i class="bx bx-power-off me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
