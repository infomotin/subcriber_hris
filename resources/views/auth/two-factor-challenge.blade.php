<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication | ZKTeco ADMS SaaS</title>
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
        <h4 class="fw-bold text-white"><i class="bx bx-shield-quarter text-primary me-2"></i>Two-Factor Authentication</h4>
        <p class="text-muted font-size-13">A one-time code has been sent to your email. Enter it below.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('two-factor.verify') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label text-white">OTP Code</label>
            <input type="text" name="code" class="form-control bg-dark border-secondary text-white text-center" placeholder="000000" required maxlength="6" inputmode="numeric" autocomplete="one-time-code">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Verify OTP</button>
    </form>

    <div class="text-center mt-4">
        <form action="{{ route('two-factor.send-otp') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none text-muted font-size-13">
                <i class="bx bx-envelope me-1"></i> Resend OTP
            </button>
        </form>
    </div>

    <div class="text-center mt-2 border-top border-secondary pt-3">
        <a href="{{ route('logout') }}" class="text-muted text-decoration-none font-size-13"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bx bx-log-out me-1"></i> Cancel & Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>

</body>
</html>
