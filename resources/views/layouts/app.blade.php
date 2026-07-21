<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | ZKTeco ADMS Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <!-- Skote Inspired Theme Custom Stylesheet -->
    <style>
        :root {
            --sk-sidebar-width: 250px;
            --sk-sidebar-bg: #2a3042;
            --sk-sidebar-color: #a6b0cf;
            --sk-sidebar-active: #ffffff;
            --sk-sidebar-active-bg: #32394e;
            --sk-header-height: 70px;
            --sk-body-bg: #f8f8fb;
            --sk-card-bg: #ffffff;
            --sk-primary: #556ee6;
            --sk-primary-hover: #4458b8;
            --sk-success: #34c38f;
            --sk-info: #50a5f1;
            --sk-warning: #f1b44c;
            --sk-danger: #f46a6a;
            --sk-dark: #343a40;
        }

        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            background-color: var(--sk-body-bg);
            color: #495057;
            overflow-x: hidden;
        }

        /* Sidebar Style */
        #vertical-menu {
            width: var(--sk-sidebar-width);
            background: var(--sk-sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1001;
            transition: all 0.3s ease;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
        }

        .navbar-brand-box {
            height: var(--sk-header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background: var(--sk-sidebar-bg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-logo {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo i {
            color: var(--sk-primary);
            font-size: 1.6rem;
        }

        .sidebar-menu {
            padding: 1.5rem 0;
            list-style: none;
            margin: 0;
        }

        .menu-title {
            padding: 12px 20px 8px 20px;
            letter-spacing: .05em;
            pointer-events: none;
            cursor: default;
            font-size: 11px;
            text-transform: uppercase;
            color: #6a7187;
            font-weight: 600;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--sk-sidebar-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a i {
            font-size: 1.25rem;
            margin-right: 12px;
            width: 20px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            color: var(--sk-sidebar-active);
            background: var(--sk-sidebar-active-bg);
            border-left-color: var(--sk-primary);
        }

        .sidebar-menu li a:hover i {
            transform: translateX(3px);
            color: var(--sk-primary);
        }

        /* Top Header */
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
            transition: left 0.3s ease;
        }

        /* Page Content Wrapper */
        .main-content {
            margin-left: var(--sk-sidebar-width);
            margin-top: var(--sk-header-height);
            padding: 2rem 1.5rem;
            min-height: calc(100vh - var(--sk-header-height));
            transition: margin-left 0.3s ease;
        }

        /* Skote Card Design */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
            background: var(--sk-card-bg);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #eff2f7;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            color: #495057;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Mini Stat Card */
        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Custom Tables */
        .table {
            color: #495057;
            vertical-align: middle;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #eff2f7;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #74788d;
            background-color: #f8f9fa;
        }

        /* Pulse Online Indicator */
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-online {
            background-color: var(--sk-success);
            box-shadow: 0 0 0 3px rgba(52, 195, 143, 0.2);
            animation: pulse-green 2s infinite;
        }

        .status-offline {
            background-color: #74788d;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(52, 195, 143, 0.6); }
            70% { box-shadow: 0 0 0 8px rgba(52, 195, 143, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 195, 143, 0); }
        }

        .page-title-box {
            padding-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title-box h4 {
            font-size: 1.125rem;
            text-transform: uppercase;
            font-weight: 600;
            margin: 0;
            color: #495057;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Left Sidebar -->
    <div id="vertical-menu">
        <div class="navbar-brand-box">
            <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                <i class="bx bx-fingerprint"></i>
                <span>ZKTeco ADMS</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title">Main</li>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bx bx-home-circle"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-title">Management</li>
            <li class="{{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
                <a href="{{ route('admin.devices.index') }}">
                    <i class="bx bx-devices"></i>
                    <span>Biometric Devices</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <a href="{{ route('admin.attendance.index') }}">
                    <i class="bx bx-calendar-check"></i>
                    <span>Attendance Logs</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="bx bx-user-check"></i>
                    <span>Biometric Users</span>
                </a>
            </li>

            <li class="menu-title">System</li>
            <li class="{{ request()->routeIs('admin.commands.*') ? 'active' : '' }}">
                <a href="{{ route('admin.commands.index') }}">
                    <i class="bx bx-terminal"></i>
                    <span>Command Queue</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}">
                    <i class="bx bx-cog"></i>
                    <span>Network Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Header Navigation -->
    <header id="page-topbar">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect border-0" id="sidebar-toggle">
                <i class="fa fa-fw fa-bars text-secondary" style="font-size: 1.2rem;"></i>
            </button>
            <div class="ms-3 d-none d-md-block">
                <span class="badge bg-soft-success text-success p-2 rounded-pill font-size-12">
                    <span class="status-indicator status-online"></span> ADMS Server Active
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=556ee6&color=fff" class="rounded-circle" width="32" height="32" alt="User">
                    <span class="fw-medium text-dark font-size-14">{{ auth()->user()->name ?? 'User Account' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bx bx-home-alt me-2"></i> Public Home</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bx bx-cog me-2"></i> Settings</a></li>
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

    <!-- Main Body Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bx bx-error-circle me-2 font-size-18 align-middle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('vertical-menu');
            const topbar = document.getElementById('page-topbar');
            const main = document.querySelector('.main-content');

            if (sidebar.style.left === '-250px') {
                sidebar.style.left = '0';
                topbar.style.left = '250px';
                main.style.marginLeft = '250px';
            } else {
                sidebar.style.left = '-250px';
                topbar.style.left = '0';
                main.style.marginLeft = '0';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
