<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication | ZKTeco ADMS SaaS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { 
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); 
            color: #f1f5f9; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            padding: 1.5rem;
        }
        .auth-card { 
            width: 100%; 
            max-width: 440px; 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08); 
            border-radius: 16px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            padding: 2.5rem !important;
            margin: auto;
        }
        .auth-card input {
            background-color: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        .auth-card input:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25) !important;
        }
        .auth-card .form-label {
            color: #e2e8f0 !important;
        }
    </style>
</head>
<body>

<div class="auth-card shadow-lg">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white mb-2"><i class="bx bx-shield-quarter text-primary me-2" style="font-size: 2rem; vertical-align: middle;"></i>Two-Factor Verification</h4>
        <p class="text-slate-400 font-size-13">A security code has been sent to your email. Enter it below.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('two-factor.verify') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label">OTP Verification Code</label>
            <input type="text" name="code" class="form-control text-center tracking-widest font-bold font-monospace" placeholder="000000" required maxlength="6" inputmode="numeric" autocomplete="one-time-code" style="font-size: 24px !important; letter-spacing: 0.25em !important; height: 55px !important;">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Verify Security Code</button>
    </form>

    <div class="text-center mt-4">
        <form action="{{ route('two-factor.send-otp') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none text-slate-400 font-size-13 p-0">
                <i class="bx bx-envelope me-1"></i> Resend Verification Code
            </button>
        </form>
    </div>

    <div class="text-center mt-3 border-top border-slate-700 pt-3">
        <a href="{{ route('logout') }}" class="text-slate-400 text-decoration-none font-size-13"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bx bx-log-out me-1"></i> Cancel & Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>

</body>
</html>
