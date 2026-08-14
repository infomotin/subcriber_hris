<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Business Admin') | AMDS SaaS Portal</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Business Admin Sidebar -->
    <div id="vertical-menu">
        <div class="navbar-brand-box">
            <a href="{{ route('admin.business.subscribers.index') }}" class="brand-logo">
                <i class="bx bx-briefcase text-primary me-2"></i>
                <span>Business Admin</span>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-title">Business Operations</li>
            <li class="{{ request()->routeIs('admin.business.subscribers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.business.subscribers.index') }}">
                    <i class="bx bx-group"></i>
                    <span>Subscribers</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.business.plans.*') ? 'active' : '' }}">
                <a href="{{ route('admin.business.plans.index') }}">
                    <i class="bx bx-package"></i>
                    <span>Package Plans</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navigation Header -->
    <header id="page-topbar">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect border-0" id="sidebar-toggle">
                <i class="fa fa-fw fa-bars text-secondary" style="font-size: 1.2rem;"></i>
            </button>
            <span class="badge bg-primary px-3 py-2 font-size-12">
                Business Administration Portal
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold font-size-14 text-dark">{{ auth()->user()->name ?? 'Business Manager' }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bx bx-power-off me-1"></i> Logout</button>
            </form>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
        @stack('modals')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
