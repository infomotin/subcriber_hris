<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organization | ZKTeco ADMS SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { background: #1e2229; color: #ced4da; font-family: 'Inter', sans-serif; display: flex; align-items: center; min-height: 100vh; padding: 2rem 0; }
        .auth-card { width: 100%; max-width: 500px; margin: auto; background: #22262b; border: 1px solid #2b3035; border-radius: 12px; }
    </style>
</head>
<body>

<div class="auth-card p-4 shadow-lg">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white"><i class="bx bx-rocket text-primary me-2"></i>Register Subscriber Account</h4>
        <p class="text-muted font-size-13">Create your organization SaaS portal</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label text-white">Company / Organization Name</label>
            <input type="text" name="name" class="form-control bg-dark border-secondary text-white" required placeholder="e.g. Acme Corporation" value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label text-white">Admin Email Address</label>
            <input type="email" name="email" class="form-control bg-dark border-secondary text-white" required placeholder="admin@acme.com" value="{{ old('email') }}">
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label text-white">Password</label>
                <input type="password" name="password" class="form-control bg-dark border-secondary text-white" required minlength="6">
            </div>
            <div class="col-6 mb-3">
                <label class="form-label text-white">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control bg-dark border-secondary text-white" required minlength="6">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-white">Select Subscription Package Plan</label>
            <select name="subscription_plan_id" class="form-select bg-dark border-secondary text-white" required>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request()->query('plan') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} ({{ number_format($plan->price_monthly, 0) }} BDT/mo - Max {{ $plan->max_devices }} Devices)
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Create SaaS Account</button>
    </form>

    <div class="text-center mt-4 border-top border-secondary pt-3">
        <span class="text-muted font-size-13">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></span>
    </div>
</div>

</body>
</html>
