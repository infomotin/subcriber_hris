<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMS HRIS & Payroll | Cloud HR & Attendance Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #eef2ff;
            --accent: #06b6d4;
            --accent2: #10b981;
            --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand i { color: var(--primary); font-size: 2rem; }
        .nav-link { color: rgba(255,255,255,0.65) !important; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; }
        .nav-link:hover { color: #fff !important; }

        .hero {
            background: var(--gradient-hero);
            color: #fff;
            padding: 7rem 0 8rem;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6,182,212,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 50%, #67e8f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-sub {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.55);
            max-width: 600px;
            line-height: 1.7;
        }
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(99,102,241,0.3);
        }
        .btn-outline-hero {
            background: transparent;
            border: 1.5px solid rgba(255,255,255,0.2);
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
        }
        .btn-outline-hero:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.4);
            color: #fff;
        }

        .hero-stats {
            display: flex;
            gap: 3rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-stat h3 { font-size: 2rem; font-weight: 800; margin-bottom: 0; }
        .hero-stat p { color: rgba(255,255,255,0.45); font-size: 0.85rem; margin-bottom: 0; }

        section { padding: 5rem 0; }
        .section-label {
            display: inline-block;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }
        .section-sub {
            color: #64748b;
            font-size: 1.05rem;
            max-width: 600px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            transition: all 0.4s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(99,102,241,0.08);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .feature-icon.green { background: #ecfdf5; color: var(--accent2); }
        .feature-icon.cyan { background: #ecfeff; color: var(--accent); }
        .feature-icon.amber { background: #fffbeb; color: #f59e0b; }
        .feature-icon.rose { background: #fff1f2; color: #f43f5e; }
        .feature-card h5 { font-weight: 700; margin-bottom: 0.75rem; color: #0f172a; }
        .feature-card p { color: #64748b; font-size: 0.9rem; margin-bottom: 0; line-height: 1.7; }

        .module-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.75rem;
            text-align: center;
            transition: all 0.3s;
        }
        .module-card:hover {
            border-color: var(--primary);
            box-shadow: 0 12px 30px rgba(99,102,241,0.06);
        }
        .module-card i { font-size: 2rem; color: var(--primary); margin-bottom: 1rem; }
        .module-card h6 { font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .module-card p { color: #64748b; font-size: 0.85rem; margin-bottom: 0; }

        .pricing-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 2.5rem;
            transition: all 0.3s;
            position: relative;
        }
        .pricing-card.featured {
            border: 2px solid var(--primary);
            box-shadow: 0 20px 50px rgba(99,102,241,0.12);
        }
        .pricing-card:hover {
            transform: translateY(-4px);
        }
        .pricing-card .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            padding: 4px 20px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .pricing-card .price { font-size: 3rem; font-weight: 900; color: #0f172a; }
        .pricing-card .price span { font-size: 1rem; font-weight: 400; color: #64748b; }
        .pricing-card ul { list-style: none; padding: 0; margin: 1.5rem 0; }
        .pricing-card ul li { padding: 0.5rem 0; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; }
        .pricing-card ul li i { color: var(--accent2); font-size: 1.1rem; }

        .role-card {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .role-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(99,102,241,0.06);
        }
        .role-card i { font-size: 2.2rem; flex-shrink: 0; }
        .role-card h6 { font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; }
        .role-card p { color: #64748b; font-size: 0.85rem; margin-bottom: 0; }

        .cta-section {
            background: var(--gradient-hero);
            padding: 5rem 0;
            text-align: center;
            color: #fff;
        }
        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        footer {
            background: #0f172a;
            color: rgba(255,255,255,0.5);
            padding: 2rem 0;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        footer a { color: rgba(255,255,255,0.6); text-decoration: none; }
        footer a:hover { color: #fff; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .hero { padding: 5rem 0 5rem; }
            .section-title { font-size: 1.75rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="bx bx-fingerprint"></i> ADMS HRIS</a>
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fa fa-bars text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center gap-2 gap-lg-3">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#modules">Modules</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles">Roles</a></li>
                    <li class="nav-item">
                        <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bx bx-play-circle me-1"></i> Demo
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary-custom btn-sm">
                                <i class="bx bx-tachometer me-1"></i> My Portal
                            </a>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log In</a></li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-sm">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container position-relative">
            <div class="text-center">
                <div class="hero-badge">
                    <i class="bx bx-shield-check"></i> All-in-One HRIS & Payroll SaaS Platform
                </div>
                <h1 class="hero-title mb-4">
                    HRIS. Payroll.<br>
                    Live Attendance.
                </h1>
                <p class="hero-sub mx-auto mb-5">
                    A complete cloud-based HR management system with biometric live attendance, 
                    automated payroll generation, leave management, and powerful HR analytics 
                    for enterprises of all sizes.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                    <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg">
                        <i class="bx bx-rocket me-2"></i> Start Free Trial
                    </a>
                    <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-hero btn-lg">
                        <i class="bx bx-laptop me-2"></i> Live Demo
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <h3>{{ $totalTenants > 0 ? $totalTenants : '100' }}+</h3>
                        <p>Active Organizations</p>
                    </div>
                    <div class="hero-stat">
                        <h3>50K+</h3>
                        <p>Employees Managed</p>
                    </div>
                    <div class="hero-stat">
                        <h3>99.9%</h3>
                        <p>Uptime Guaranteed</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Platform Capabilities</span>
                <h2 class="section-title">Everything You Need to Manage HR</h2>
                <p class="section-sub mx-auto">From biometric attendance to automated payroll — one platform, zero hassle.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-camera"></i></div>
                        <h5>Live Biometric Attendance</h5>
                        <p>Real-time punch logging from ZKTeco biometric devices with instant sync and SMS/email alerts.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="bx bx-dollar-circle"></i></div>
                        <h5>Auto Salary Generate</h5>
                        <p>Configure salary structures, auto-calculate based on attendance, deductions, and generate payslips.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon cyan"><i class="bx bx-calendar-check"></i></div>
                        <h5>Leave & Movement Management</h5>
                        <p>Full leave lifecycle with balance tracking, approval workflows, movement passes, and calendar view.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon amber"><i class="bx bx-line-chart"></i></div>
                        <h5>HR Analytics & Reports</h5>
                        <p>KPI tracking, increment rules, promotion pipelines, and comprehensive HR analytics dashboards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" style="background: #fff;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">HRIS Modules</span>
                <h2 class="section-title">Complete HR & Payroll Suite</h2>
                <p class="section-sub mx-auto">All the modules you need to run your HR operations seamlessly.</p>
            </div>
            <div class="row g-3">
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-user-plus"></i><h6>Employee Management</h6><p>Profiles, documents, education, experience</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-fingerprint"></i><h6>Biometric Integration</h6><p>ZKTeco ADMS cloud sync engine</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-buildings"></i><h6>Departments & Designations</h6><p>Org structure & hierarchy</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-time-five"></i><h6>Work Shifts</h6><p>Shift scheduling & roster</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-wallet"></i><h6>Payroll & Salary</h6><p>Auto salary generation & structures</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-calendar"></i><h6>Leave Management</h6><p>Types, balances, applications</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-trending-up"></i><h6>KPI & Performance</h6><p>Goals, reviews, ratings</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-up-arrow-circle"></i><h6>Promotions & Increments</h6><p>Career progression tracking</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-file"></i><h6>Bill & Expense</h6><p>Bill types, purposes, approvals</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-git-branch"></i><h6>Movement Passes</h6><p>Employee movement tracking</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-check-shield"></i><h6>Verification</h6><p>Employee document verification</p></div></div>
                <div class="col-lg-3 col-md-4 col-6"><div class="module-card"><i class="bx bx-lock"></i><h6>Roles & Permissions</h6><p>Granular access control</p></div></div>
            </div>
        </div>
    </section>

    <section id="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Pricing</span>
                <h2 class="section-title">Simple, Transparent Pricing</h2>
                <p class="section-sub mx-auto">Choose a plan that fits your organization size.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($plans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card {{ $loop->iteration == 2 ? 'featured' : '' }}">
                            @if($loop->iteration == 2)
                                <div class="popular-badge">Most Popular</div>
                            @endif
                            <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                            <p class="text-muted" style="font-size:0.9rem;">{{ $plan->description }}</p>
                            <div class="price">{{ number_format($plan->price_monthly, 0) }} <span>/mo</span></div>
                            <p style="font-size:0.85rem; color:#94a3b8;">{{ number_format($plan->price_yearly, 0) }} BDT/year</p>
                            <ul>
                                <li><i class="bx bx-check-circle"></i> Up to <strong>{{ $plan->max_devices }}</strong> biometric devices</li>
                                <li><i class="bx bx-check-circle"></i> Full HRIS module access</li>
                                <li><i class="bx bx-check-circle"></i> Auto payroll generation</li>
                                <li><i class="bx bx-check-circle"></i> Realtime attendance sync</li>
                                <li><i class="bx bx-check-circle"></i> Email & SMS support</li>
                            </ul>
                            <a href="{{ route('register', ['plan' => $plan->id]) }}" class="btn btn-primary-custom w-100">
                                Get Started
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="roles" style="background: #fff;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Access Levels</span>
                <h2 class="section-title">Four Role-Based Dashboards</h2>
                <p class="section-sub mx-auto">Tailored experiences for every stakeholder in your organization.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <i class="bx bx-shield-quarter" style="color:#ef4444;"></i>
                        <div>
                            <h6>System Admin</h6>
                            <p>Application health, monitoring, security audits, database management.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <i class="bx bx-briefcase" style="color:#3b82f6;"></i>
                        <div>
                            <h6>Business Admin</h6>
                            <p>Subscriber management, package plans, billing & revenue.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <i class="bx bx-user-check" style="color:#10b981;"></i>
                        <div>
                            <h6>Tenant Portal</h6>
                            <p>Full HRIS, attendance, payroll, devices, employee management.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <i class="bx bx-play-circle" style="color:#f59e0b;"></i>
                        <div>
                            <h6>Public Demo</h6>
                            <p>Sandbox environment to explore all features risk-free.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2 class="mb-3">Ready to Transform Your HR Operations?</h2>
            <p style="color:rgba(255,255,255,0.6); font-size:1.1rem; max-width:500px; margin:0 auto 2rem;">
                Join thousands of organizations using ADMS HRIS to streamline attendance, payroll, and HR management.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg px-5">
                    <i class="bx bx-rocket me-2"></i> Start Free Trial
                </a>
                <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-hero btn-lg px-5">
                    <i class="bx bx-laptop me-2"></i> View Demo
                </a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} ADMS HRIS & Payroll Platform. All rights reserved. Powered by <a href="#">Nexozaint</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>