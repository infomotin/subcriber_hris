<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZKTeco ADMS SaaS | Cloud Biometric Attendance Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & FontAwesome / Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #0f172a 100%);
            --hero-bg: #0f172a;
            --accent: #10b981;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            overflow-x: hidden;
        }

        /* Top Navbar */
        .navbar-custom {
            background: rgba(30, 34, 41, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            color: #556ee6;
            font-size: 1.8rem;
        }

        /* Hero Section */
        .hero-section {
            background: var(--hero-bg);
            color: #fff;
            padding: 6rem 0 7rem 0;
            position: relative;
            background-image: radial-gradient(circle at 80% 20%, rgba(85, 110, 230, 0.15) 0%, transparent 50%);
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            background: linear-gradient(135deg, #ffffff 0%, #a6b0cf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .badge-hero {
            background: rgba(85, 110, 230, 0.2);
            color: #798ec4;
            border: 1px solid rgba(85, 110, 230, 0.4);
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .feature-card {
            border: none;
            border-radius: 12px;
            padding: 2rem;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(85, 110, 230, 0.1);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: rgba(85, 110, 230, 0.1);
            color: #556ee6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        /* Plan Pricing Card */
        .pricing-card {
            border: 1px solid #eff2f7;
            border-radius: 16px;
            background: #fff;
            padding: 2.5rem 2rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .pricing-card.featured {
            border: 2px solid #556ee6;
            box-shadow: 0 15px 40px rgba(85, 110, 230, 0.15);
        }

        .pricing-card:hover {
            transform: translateY(-5px);
        }

        .btn-primary-custom {
            background: #4f46e5;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            color: #fff;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: #4338ca;
            color: #fff;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        /* Role Pill Cards */
        .role-pill-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            border-left: 4px solid #556ee6;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bx bx-fingerprint"></i> ZKTeco ADMS SaaS
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50 fw-medium" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50 fw-medium" href="#pricing">Pricing Plans</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50 fw-medium" href="#roles">SaaS Roles</a></li>

                    <li class="nav-item">
                        <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bx bx-play-circle me-1"></i> Public Demo Sandbox
                        </a>
                    </li>

                    @auth
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary-custom btn-sm">
                                <i class="bx bx-tachometer me-1"></i> My Portal
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-link text-white text-decoration-none fw-medium">Log In</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-sm">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <span class="badge-hero mb-3 d-inline-block">
                <i class="bx bx-shield-check me-1"></i> Multi-Tenant ZKTeco Biometric Cloud ADMS Engine
            </span>
            <h1 class="hero-title mb-4 max-w-800 mx-auto">
                Connect Physical ZKTeco Biometric Terminals to the Cloud
            </h1>
            <p class="lead text-white-50 max-w-700 mx-auto mb-5">
                Multi-tenant attendance automation for enterprises, small businesses, and institutions. Dedicated tenant ADMS token endpoints, real-time punch feeds, automated command queue, and SSLCommerz payment billing.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg">
                    <i class="bx bx-rocket me-2"></i> Register Organization Plan
                </a>
                <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-light btn-lg rounded-3">
                    <i class="bx bx-laptop me-2"></i> Instant Public Demo Sandbox
                </a>
            </div>

            <!-- Fast Stats Row -->
            <div class="row mt-5 pt-4 text-center justify-content-center">
                <div class="col-md-3 col-6 mb-3">
                    <h2 class="fw-bold text-white mb-0">100%</h2>
                    <small class="text-white-50">ZKTeco Push ADMS Compatible</small>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <h2 class="fw-bold text-success mb-0">Single DB</h2>
                    <small class="text-white-50">Strict Tenant Data Isolation</small>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <h2 class="fw-bold text-info mb-0">Realtime</h2>
                    <small class="text-white-50">Instant Punch Sync & SMS Alerts</small>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary text-uppercase fw-bold font-size-12">Core Capabilities</span>
                <h2 class="fw-bold">Enterprise SaaS Features</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-key"></i></div>
                        <h5 class="fw-bold">Dedicated Tenant Endpoints</h5>
                        <p class="text-muted font-size-14">Each subscriber receives a tokenized ADMS URL (<code>/iclock/{token}/cdata</code>) to point their physical biometric terminals.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: rgba(52, 195, 143, 0.1); color: #34c38f;"><i class="bx bx-terminal"></i></div>
                        <h5 class="fw-bold">Remote Command Queue</h5>
                        <p class="text-muted font-size-14">Queue remote commands like reboot, log clearance, user sync, and device info queries to execute on physical machines.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: rgba(80, 165, 241, 0.1); color: #50a5f1;"><i class="bx bx-credit-card"></i></div>
                        <h5 class="fw-bold">SSLCommerz Billing</h5>
                        <p class="text-muted font-size-14">Automated subscription payments via card, bKash, and mobile banking with instant quota extensions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary text-uppercase fw-bold font-size-12">Flexible Pricing</span>
                <h2 class="fw-bold">Subscription Package Plans</h2>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($plans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card {{ $loop->iteration == 2 ? 'featured' : '' }}">
                            @if($loop->iteration == 2)
                                <span class="badge bg-primary position-absolute top-0 end-0 m-3 px-3 py-2">Most Popular</span>
                            @endif
                            <h5 class="fw-bold text-dark mb-1">{{ $plan->name }}</h5>
                            <p class="text-muted font-size-13">{{ $plan->description }}</p>
                            <h1 class="fw-bold my-4 text-dark">{{ number_format($plan->price_monthly, 0) }} <span class="font-size-14 text-muted font-weight-normal">BDT / mo</span></h1>

                            <ul class="list-unstyled mb-4 font-size-14">
                                <li class="mb-3"><i class="bx bx-check-circle text-success me-2 font-size-18"></i> Up to <strong>{{ $plan->max_devices }}</strong> Biometric Terminals</li>
                                <li class="mb-3"><i class="bx bx-check-circle text-success me-2 font-size-18"></i> Realtime Push Punch Logging</li>
                                <li class="mb-3"><i class="bx bx-check-circle text-success me-2 font-size-18"></i> Year Discount: <strong>{{ number_format($plan->price_yearly, 0) }} BDT/yr</strong></li>
                            </ul>

                            <a href="{{ route('register', ['plan' => $plan->id]) }}" class="btn btn-primary-custom w-100">
                                Select {{ $plan->name }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SaaS Roles Overview Section -->
    <section id="roles" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary text-uppercase fw-bold font-size-12">Access Control</span>
                <h2 class="fw-bold">4 Role-Based Dashboard Panels</h2>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="role-pill-card">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bx bx-shield-quarter text-danger font-size-36"></i>
                            <div>
                                <h6 class="fw-bold mb-1">System Admin Dashboard</h6>
                                <p class="text-muted mb-0 font-size-13">Application health, log viewer, network diagnostics, DB ping metrics, system audit.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="role-pill-card" style="border-left-color: #50a5f1;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bx bx-briefcase text-info font-size-36"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Business Admin Dashboard</h6>
                                <p class="text-muted mb-0 font-size-13">Subscriber management, package builder, SSLCommerz transaction logs, broadcast SMS.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="role-pill-card" style="border-left-color: #34c38f;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bx bx-user-check text-success font-size-36"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Subscriber Tenant Portal</h6>
                                <p class="text-muted mb-0 font-size-13">Organization attendance dashboard, machine limits, biometric user roster, custom ADMS setup.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="role-pill-card" style="border-left-color: #f1b44c;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bx bx-play-circle text-warning font-size-36"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Public Sandbox Demo Account</h6>
                                <p class="text-muted mb-0 font-size-13">Sandbox environment to test physical ZKTeco machine setups; auto-destroys on logout.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white-50 py-4 text-center border-top border-secondary">
        <div class="container">
            <p class="mb-0 font-size-14">&copy; {{ date('Y') }} ZKTeco ADMS SaaS Platform. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
