<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Sandbox Demo | ZKTeco ADMS SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .demo-nav { background: #556ee6; color: #fff; padding: 1rem 2rem; }
    </style>
</head>
<body>

<div class="demo-nav d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <i class="bx bx-play-circle font-size-24"></i>
        <h5 class="mb-0 fw-bold">Public Demo Sandbox Mode</h5>
    </div>
    <form action="{{ route('demo.destroy') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-light btn-sm" onclick="return confirm('End session and purge sandbox test data?')">
            <i class="bx bx-trash me-1"></i> End Demo & Destroy Sandbox Data
        </button>
    </form>
</div>

<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <h5 class="fw-bold"><i class="bx bx-info-circle me-1"></i> Temporary ZKTeco Test Environment</h5>
        <p class="mb-1">You are using the public demo sandbox account. Point your physical ZKTeco machine or simulation script to:</p>
        <code class="font-size-14 text-dark bg-white p-2 rounded d-inline-block">
            http://amds.test/iclock/{{ $demoTenant->tenant_token }}/cdata
        </code>
        <p class="mt-2 mb-0 text-muted font-size-13">* All sandbox test machines and logs are automatically destroyed upon session logout.</p>
    </div>

    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card p-4 shadow-sm border-0">
                <span class="text-muted text-uppercase">Demo Machines</span>
                <h2 class="fw-bold text-primary mt-2">{{ $devicesCount }} / {{ $demoTenant->max_devices }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 shadow-sm border-0">
                <span class="text-muted text-uppercase">Demo Attendance Punches</span>
                <h2 class="fw-bold text-success mt-2">{{ $logsCount }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 shadow-sm border-0">
                <span class="text-muted text-uppercase">Demo Users</span>
                <h2 class="fw-bold text-info mt-2">{{ $usersCount }}</h2>
            </div>
        </div>
    </div>
</div>

</body>
</html>
