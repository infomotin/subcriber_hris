<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Business Admin') | AMDS SaaS Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; color: #333; }
        .sidebar { width: 250px; background: #2a3042; position: fixed; top: 0; bottom: 0; left: 0; padding: 1.5rem 1rem; color: #a6b0cf; z-index: 1000; }
        .sidebar a { color: #a6b0cf; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 6px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: #32394e; color: #fff; }
        .topbar { margin-left: 250px; height: 60px; background: #fff; border-bottom: 1px solid #eef2f7; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .main { margin-left: 250px; padding: 2rem; }
        .card { border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h5 class="text-white fw-bold mb-4"><i class="bx bx-briefcase text-primary me-2"></i>BUSINESS ADMIN</h5>
        <a href="{{ route('admin.business.subscribers.index') }}" class="{{ request()->routeIs('admin.business.subscribers.*') ? 'active' : '' }}"><i class="bx bx-group"></i> Subscribers Management</a>
        <a href="{{ route('admin.business.plans.index') }}" class="{{ request()->routeIs('admin.business.plans.*') ? 'active' : '' }}"><i class="bx bx-package"></i> Package Plans</a>
    </div>

    <div class="topbar">
        <span class="badge bg-primary">Business Administration Portal</span>
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold font-size-14 text-dark">{{ auth()->user()->name ?? 'Business Manager' }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bx bx-power-off me-1"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="main">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
