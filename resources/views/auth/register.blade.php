<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organization | ZKTeco ADMS SaaS</title>
    
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
            padding: 2rem 1.5rem;
        }
        .auth-card { 
            width: 100%; 
            max-width: 520px; 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08); 
            border-radius: 16px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            padding: 2.5rem !important;
            margin: auto;
        }
        .auth-card input, .auth-card select {
            background-color: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        .auth-card select option {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }
        .auth-card input:focus, .auth-card select:focus {
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
        <h4 class="fw-bold text-white mb-2"><i class="bx bx-rocket text-primary me-2" style="font-size: 2rem; vertical-align: middle;"></i>Register Subscriber Account</h4>
        <p class="text-slate-400 font-size-13">Create your organization SaaS portal</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        @if(isset($securityConfig) && $securityConfig->honeypot_enabled)
            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                <input type="text" name="hp_name" tabindex="-1" autocomplete="off">
                <input type="hidden" name="hp_time" value="{{ time() }}">
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Company / Organization Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Acme Corporation" value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Admin Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="admin@acme.com" value="{{ old('email') }}">
        </div>

        <div class="row">
            <div class="col-sm-6 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6" placeholder="••••••••">
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="6" placeholder="••••••••">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Subscription Package Plan</label>
            <select name="subscription_plan_id" class="form-select" required>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request()->query('plan') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} ({{ number_format($plan->price_monthly, 0) }} BDT/mo - Max {{ $plan->max_devices }} Devices)
                    </option>
                @endforeach
            </select>
        </div>

        @if(isset($securityConfig) && $securityConfig->captcha_enabled)
            <div class="mb-3 bg-light p-3 rounded border text-dark">
                <label class="form-label fw-bold text-dark mb-2"><i class="bx bx-calculator me-1"></i> Math Verification</label>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark font-size-16">{{ $captchaData['question'] }}</span>
                    <input type="number" name="captcha_answer" class="form-control form-control-sm w-25 text-dark" placeholder="?" required style="background-color: #fff !important; border-color: #cbd5e1 !important; color: #0f172a !important;">
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-2">Create SaaS Account</button>
    </form>

    <div class="text-center mt-4 border-top border-slate-700 pt-3">
        <span class="text-slate-400 font-size-13">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></span>
    </div>
</div>

</body>
</html>
