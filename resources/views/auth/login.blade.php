<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | ZKTeco ADMS SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { background: #1e2229; color: #ced4da; font-family: 'Inter', sans-serif; display: flex; align-items: center; min-height: 100vh; }
        .auth-card { width: 100%; max-width: 440px; margin: auto; background: #22262b; border: 1px solid #2b3035; border-radius: 12px; }
    </style>
</head>
<body>

<div class="auth-card p-4 shadow-lg">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white"><i class="bx bx-fingerprint text-primary me-2"></i>ZKTeco ADMS SaaS</h4>
        <p class="text-muted font-size-13">Sign in to access your role dashboard</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label text-white">Email Address</label>
            <input type="email" name="email" class="form-control bg-dark border-secondary text-white" required placeholder="sysadmin@amds.test" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label text-white">Password</label>
            <input type="password" name="password" class="form-control bg-dark border-secondary text-white" required placeholder="••••••••">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label text-muted" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign In</button>
    </form>

    <div class="text-center mt-4 border-top border-secondary pt-3">
        <span class="text-muted font-size-13">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Register Organization</a></span>
    </div>
</div>

</body>
</html>
