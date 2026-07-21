<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'System Admin') | AMDS SaaS Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #1a1d21; color: #ced4da; }
        .sidebar { width: 250px; background: #111315; position: fixed; top: 0; bottom: 0; left: 0; padding: 1.5rem 1rem; border-right: 1px solid #2b3035; z-index: 1000; }
        .sidebar a { color: #9ab; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 6px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: #556ee6; color: #fff; }
        .topbar { margin-left: 250px; height: 60px; background: #111315; border-bottom: 1px solid #2b3035; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .main { margin-left: 250px; padding: 2rem; }
        .card { background: #22262b; border: 1px solid #2b3035; color: #e9ecef; }
        .table { color: #ced4da; }
        .table thead th { border-bottom: 2px solid #343a40; color: #8a94a6; background: #1c1f24; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h5 class="text-white fw-bold mb-4"><i class="bx bx-shield-quarter text-danger me-2"></i>SYSTEM ADMIN</h5>
        <a href="{{ route('admin.system.dashboard') }}" class="{{ request()->routeIs('admin.system.dashboard') ? 'active' : '' }}"><i class="bx bx-tachometer"></i> Health & Metrics</a>
        <a href="{{ route('admin.settings.index') }}"><i class="bx bx-cog"></i> Network & Port Setup</a>
    </div>

    <div class="topbar">
        <span class="badge bg-danger">System Administrator Panel</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white font-size-14">{{ auth()->user()->name ?? 'System Admin' }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bx bx-power-off me-1"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="main">
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success text-white mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
