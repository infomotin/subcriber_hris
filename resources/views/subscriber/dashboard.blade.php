@extends('layouts.subscriber')

@section('title', 'Subscriber Tenant Dashboard')

@section('content')
<div class="page-title-box">
    <h4>Subscriber Portal: {{ $tenant->name ?? 'My Organization' }}</h4>
    <div class="page-title-right">
        <a href="#subscription-info-section" class="btn btn-warning btn-sm rounded-pill shadow-sm">
            <i class="bx bx-crown me-1"></i> Manage Subscription & Plan
        </a>
    </div>
</div>

<!-- Tenant ADMS Dedicated Push Endpoint Banner -->
<div class="alert alert-info border-0 shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1"><i class="bx bx-broadcast me-2"></i> Dedicated ZKTeco Machine ADMS Endpoint</h6>
            <p class="mb-0 font-size-13">Configure this unique URL on your ZKTeco biometric machine's <strong>COMM. &gt; ADMS Cloud Server</strong> settings:</p>
            <code class="font-size-14 text-dark bg-white p-2 rounded d-inline-block mt-2">
                http://amds.test/iclock/{{ $tenant->tenant_token }}/cdata
            </code>
        </div>
        <div class="text-end">
            <span class="badge bg-primary font-size-13 p-2">Tenant Token: {{ $tenant->tenant_token }}</span>
        </div>
    </div>
</div>

<!-- Subscriber Information & Subscription Management Section -->
<div id="subscription-info-section" class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bx bx-id-card text-primary me-2 font-size-20"></i> Subscriber Information & Subscription Plan
            </h5>
            <span class="badge {{ $tenant->status === 'active' ? 'bg-success' : 'bg-danger' }} font-size-13 px-3 py-2">
                {{ strtoupper($tenant->status) }} SUBSCRIBER
            </span>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <!-- Left Side: Subscriber & Active Subscription Details -->
            <div class="col-lg-6 border-end">
                <h6 class="text-uppercase text-muted font-size-12 fw-bold mb-3">Account Details</h6>
                
                <div class="mb-3">
                    <span class="text-muted d-block font-size-13">Organization / Subscriber Name:</span>
                    <h5 class="fw-bold text-dark mb-0">{{ $tenant->name }}</h5>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block font-size-13">Admin Account Email:</span>
                    <strong class="text-dark">{{ auth()->user()->email ?? 'subscriber@amds.test' }}</strong>
                </div>

                <hr class="my-3">

                <h6 class="text-uppercase text-muted font-size-12 fw-bold mb-3">Current Active Subscription</h6>
                
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="bg-light p-3 rounded">
                            <span class="text-muted font-size-12 d-block">Active Package Plan</span>
                            <h5 class="fw-bold text-primary mb-0">{{ $currentPlan->name ?? 'Starter Plan' }}</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-3 rounded">
                            <span class="text-muted font-size-12 d-block">Remaining Days</span>
                            <h5 class="fw-bold {{ $remainingDays <= 5 ? 'text-danger' : 'text-success' }} mb-0">
                                <i class="bx bx-time me-1"></i> {{ $remainingDays }} Days
                            </h5>
                        </div>
                    </div>
                </div>

                <ul class="list-unstyled font-size-13 mb-0 text-secondary">
                    <li class="mb-2"><i class="bx bx-calendar text-primary me-2"></i> Expiration Date: <strong>{{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y (h:i A)') : 'Lifetime' }}</strong></li>
                    <li class="mb-2"><i class="bx bx-chip text-primary me-2"></i> Machine Limit: <strong>{{ $devicesCount }} / {{ $tenant->max_devices }} Machines Registered</strong></li>
                </ul>
            </div>

            <!-- Right Side: Upgrade / Renewal Server Package Purchase Form -->
            <div class="col-lg-6">
                <h6 class="text-uppercase text-primary font-size-12 fw-bold mb-3"><i class="bx bx-shopping-bag me-1"></i> Buy / Upgrade Subscription Plan</h6>
                
                <form action="{{ route('subscriber.checkout') }}" method="POST" id="formSubCheckout">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Select Server Package Plan</label>
                        <select name="plan_id" id="selectPackagePlan" class="form-select border-secondary" required>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" 
                                        data-monthly="{{ $plan->price_monthly }}" 
                                        data-yearly="{{ $plan->price_yearly }}" 
                                        data-devices="{{ $plan->max_devices }}"
                                        {{ ($currentPlan->id ?? null) == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} (Up to {{ $plan->max_devices }} ZKTeco Devices)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Choose Billing Cycle</label>
                        <div class="d-flex gap-3">
                            <div class="form-check flex-fill bg-light p-3 rounded border">
                                <input class="form-check-input" type="radio" name="billing_cycle" id="cycleMonthly" value="monthly" checked>
                                <label class="form-check-label fw-bold cursor-pointer" for="cycleMonthly">
                                    Monthly Billing
                                </label>
                            </div>
                            <div class="form-check flex-fill bg-light p-3 rounded border">
                                <input class="form-check-input" type="radio" name="billing_cycle" id="cycleYearly" value="yearly">
                                <label class="form-check-label fw-bold cursor-pointer" for="cycleYearly">
                                    Yearly Billing (Discounted)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Server Amount Price Calculation Box -->
                    <div class="bg-soft-primary p-3 rounded d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted font-size-12 d-block">Server Price Amount to Pay:</span>
                            <h3 class="fw-bold text-primary mb-0" id="displayServerAmount">0.00 BDT</h3>
                        </div>
                        <span class="badge bg-primary px-3 py-2 font-size-13" id="displayMaxDevices">Max 2 Machines</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                        <i class="bx bx-credit-card me-1"></i> Pay & Renew via SSLCommerz
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quotas & Metrics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Machine Quota</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $devicesCount }} / {{ $tenant->max_devices }}</h3>
                </div>
                <div class="stat-icon bg-soft-primary text-primary">
                    <i class="bx bx-chip"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Online Machines</span>
                    <h3 class="mt-2 mb-0 fw-bold text-success" id="statOnlineDevices">{{ $onlineDevicesCount }}</h3>
                </div>
                <div class="stat-icon text-success" style="background: rgba(52, 195, 143, 0.1);">
                    <i class="bx bx-wifi"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Today's Punches</span>
                    <h3 class="mt-2 mb-0 fw-bold text-info" id="statTodayPunches">{{ $todayPunches }}</h3>
                </div>
                <div class="stat-icon text-info" style="background: rgba(80, 165, 241, 0.1);">
                    <i class="bx bx-fingerprint"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-12 fw-medium">Biometric Users</span>
                    <h3 class="mt-2 mb-0 fw-bold text-warning" id="statUsersCount">{{ $usersCount }}</h3>
                </div>
                <div class="stat-icon text-warning" style="background: rgba(241, 180, 76, 0.1);">
                    <i class="bx bx-user-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tenant Attendance Log Feed -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bx bx-time me-1 text-primary"></i> Tenant Realtime Punch Logs Feed</span>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success font-size-12" id="liveBadge"><i class="bx bx-pulse me-1"></i> Live (5s)</span>
            <a href="{{ route('subscriber.attendance.index') }}" class="btn btn-sm btn-outline-primary">View All Logs</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>User PIN</th>
                        <th>User Name</th>
                        <th>Machine</th>
                        <th>Punched Time</th>
                        <th>Status</th>
                        <th>Verification</th>
                    </tr>
                </thead>
                <tbody id="livePunchFeed">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $log->pin }}</span></td>
                            <td>{{ $log->zktecoUser->name ?? 'User #' . $log->pin }}</td>
                            <td><code>{{ $log->device->serial_number ?? 'N/A' }}</code></td>
                            <td>{{ $log->punched_at->format('M d, Y h:i:s A') }}</td>
                            <td><span class="badge bg-soft-info text-info">{{ $log->status_label }}</span></td>
                            <td><span class="badge bg-soft-secondary text-secondary">{{ $log->verify_type_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No attendance punches recorded for your organization yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectPlan = document.getElementById('selectPackagePlan');
        const radioMonthly = document.getElementById('cycleMonthly');
        const radioYearly = document.getElementById('cycleYearly');
        const displayAmount = document.getElementById('displayServerAmount');
        const displayDevices = document.getElementById('displayMaxDevices');

        function updateServerPrice() {
            if (!selectPlan) return;
            const selectedOption = selectPlan.options[selectPlan.selectedIndex];
            const isYearly = radioYearly.checked;
            
            const monthlyPrice = parseFloat(selectedOption.getAttribute('data-monthly') || 0);
            const yearlyPrice = parseFloat(selectedOption.getAttribute('data-yearly') || 0);
            const devices = selectedOption.getAttribute('data-devices') || 2;

            const finalPrice = isYearly ? yearlyPrice : monthlyPrice;
            const cycleText = isYearly ? '/ year' : '/ month';

            displayAmount.textContent = finalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' BDT ' + cycleText;
            displayDevices.textContent = 'Up to ' + devices + ' Machines';
        }

        selectPlan?.addEventListener('change', updateServerPrice);
        radioMonthly?.addEventListener('change', updateServerPrice);
        radioYearly?.addEventListener('change', updateServerPrice);

        updateServerPrice();

        // Real-time polling for attendance feed
        const punchFeed = document.getElementById('livePunchFeed');
        const todayPunchesEl = document.getElementById('statTodayPunches');
        let lastLogId = {{ $recentLogs->first()?->id ?? 0 }};

        function fetchLiveStats() {
            fetch('{{ route("subscriber.dashboard.stats") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (todayPunchesEl && data.today_punches !== undefined) {
                    todayPunchesEl.textContent = data.today_punches;
                }

                if (data.recent_logs && data.recent_logs.length > 0) {
                    punchFeed.innerHTML = '';
                    data.recent_logs.forEach(log => {
                        const statusBadge = log.status_label === 'Check In'
                            ? 'bg-success text-white'
                            : log.status_label === 'Check Out'
                                ? 'bg-danger text-white'
                                : 'bg-info text-white';
                        punchFeed.innerHTML += `
                            <tr>
                                <td><span class="fw-bold text-primary">${log.pin}</span></td>
                                <td>${log.user_name}</td>
                                <td><code>${log.device_serial}</code></td>
                                <td>${log.punched_at}</td>
                                <td><span class="badge ${statusBadge}">${log.status_label}</span></td>
                                <td><span class="badge bg-secondary text-white">${log.verify_type_label}</span></td>
                            </tr>`;
                    });
                }
            })
            .catch(() => {});
        }

        setInterval(fetchLiveStats, 5000);
    });
</script>
@endpush
