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
        :root {
            --sk-sidebar-width: 250px;
            --sk-sidebar-bg: #1e2229;
            --sk-sidebar-color: #a6b0cf;
            --sk-sidebar-active: #ffffff;
            --sk-sidebar-active-bg: #2b3035;
            --sk-header-height: 70px;
            --sk-body-bg: #f8f8fb;
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
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
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
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            color: var(--sk-sidebar-active);
            background: var(--sk-sidebar-active-bg);
            border-left-color: var(--sk-primary);
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
</head>
<body>

    <!-- Dedicated Subscriber Sidebar -->
    <div id="vertical-menu">
        <div class="navbar-brand-box">
            <a href="{{ route('subscriber.dashboard') }}" class="brand-logo">
                <i class="bx bx-fingerprint"></i>
                <span>Subscriber Portal</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title">Subscriber Navigation</li>
            
            <li class="{{ request()->routeIs('subscriber.dashboard') ? 'active' : '' }}">
                <a href="{{ route('subscriber.dashboard') }}">
                    <i class="bx bx-tachometer"></i>
                    <span>Dashboard & Subscription</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.devices.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.devices.index') }}">
                    <i class="bx bx-chip"></i>
                    <span>My Biometric Machines</span>
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
                    <i class="bx bx-user-check"></i>
                    <span>Biometric Users</span>
                </a>
            </li>

            <li class="menu-title">Integration & Push</li>

            <li class="{{ request()->routeIs('subscriber.webhook.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.webhook.index') }}">
                    <i class="bx bx-send"></i>
                    <span>Data Push to Server</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.mock.*') ? 'active' : '' }}">
                <a href="{{ route('subscriber.mock.viewer') }}">
                    <i class="bx bx-server"></i>
                    <span>Mock Server Inspector</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('subscriber.plans') ? 'active' : '' }}">
                <a href="{{ route('subscriber.plans') }}">
                    <i class="bx bx-crown"></i>
                    <span>Subscription Quota</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navigation -->
    <header id="page-topbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2 font-size-12">
                Subscriber Portal: {{ auth()->user()->tenant->name ?? 'Organization' }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Subscriber') }}&background=556ee6&color=fff" class="rounded-circle" width="32" height="32" alt="User">
                    <span class="fw-medium text-dark font-size-14">{{ auth()->user()->name ?? 'Subscriber Account' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('subscriber.dashboard') }}"><i class="bx bx-tachometer me-2"></i> My Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('subscriber.webhook.index') }}"><i class="bx bx-send me-2"></i> Data Push to Server</a></li>
                    <li><a class="dropdown-item" href="{{ route('subscriber.mock.viewer') }}"><i class="bx bx-server me-2"></i> Mock Server Inspector</a></li>
                    <li><a class="dropdown-item" href="{{ route('subscriber.plans') }}"><i class="bx bx-crown me-2"></i> Subscription Plan</a></li>
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
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bx bx-check-circle me-2 font-size-18 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
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
