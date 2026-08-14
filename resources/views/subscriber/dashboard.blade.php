@extends('layouts.subscriber')

@section('title', 'Subscriber Tenant Dashboard')

@section('content')
<style>
    .billing-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background-color: #ffffff;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-check:checked + .billing-card {
        border-color: var(--color-primary) !important;
        background: linear-gradient(135deg, rgba(95, 90, 246, 0.06), rgba(95, 90, 246, 0.02)) !important;
        box-shadow: 0 0 0 2px var(--color-primary), 0 8px 20px rgba(95, 90, 246, 0.1) !important;
    }
    .btn-check:checked + .billing-card .bx-calendar-event {
        color: var(--color-primary) !important;
    }
    .btn-check:checked + .billing-card .bx-crown {
        color: #d97706 !important;
    }
    .billing-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
    .premium-badge {
        background: linear-gradient(135deg, rgba(95, 90, 246, 0.12), rgba(16, 185, 129, 0.12));
        border: 1px solid rgba(95, 90, 246, 0.25) !important;
        color: var(--color-primary) !important;
    }
    .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.6) !important;
        border-radius: 16px !important;
        background: rgba(255, 255, 255, 0.95) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Subscriber Dashboard</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Overview & Portal Control</h4>
    </div>
    <div class="page-title-right">
        <a href="#subscription-info-section" class="btn btn-primary btn-sm rounded-pill shadow-sm px-4">
            <i class="bx bx-crown me-1 text-amber-300"></i> Manage Subscription & Plan
        </a>
    </div>
</div>

<!-- Tenant ADMS Dedicated Push Endpoint Banner -->
<div class="card border-0 mb-4" style="background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid rgba(95, 90, 246, 0.1) !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="fw-bold text-slate-800 mb-2" style="font-family: 'Poppins', sans-serif; font-size: 0.95rem;">
                    <i class="bx bx-broadcast me-2 text-primary font-size-20 align-middle"></i> Dedicated ZKTeco Machine ADMS Endpoint
                </h6>
                <p class="mb-3 text-slate-600 font-size-13">Configure this unique URL on your ZKTeco biometric machine's <strong>COMM. &gt; ADMS Cloud Server</strong> settings:</p>

                <div class="input-group" style="max-width: 580px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                    <span class="input-group-text bg-white border-end-0 text-slate-400 font-size-13"><i class="bx bx-link"></i></span>
                    <input type="text" class="form-control font-size-13 bg-white border-start-0 border-end-0 fw-semibold text-slate-700 py-2" id="adms-url" value="{{ $tenant->admsEndpointUrl() }}" readonly>
                    <button class="btn btn-primary px-4 fw-bold font-size-13" type="button" id="copy-btn" onclick="copyAdmsUrl()">
                        <i class="bx bx-copy me-1" id="copy-icon"></i> <span id="copy-text">Copy URL</span>
                    </button>
                </div>
            </div>
            <div class="d-flex flex-column align-items-start align-items-md-end gap-1">
                <span class="badge premium-badge font-size-11 px-3 py-2 rounded-pill">Active Tenant Token</span>
                <code class="font-size-13 fw-bold text-primary bg-white border px-3 py-1.5 rounded-pill mt-1" style="border-color: rgba(95, 90, 246, 0.15) !important;">{{ $tenant->tenant_token }}</code>
            </div>
        </div>
    </div>
</div>

<!-- Subscriber Information & Subscription Management Section -->
<div id="subscription-info-section" class="card border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3.5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-slate-800" style="font-family: 'Poppins', sans-serif;">
                <i class="bx bx-id-card text-primary me-2 font-size-22 align-middle"></i> Subscription & Account Overview
            </h5>
            <span class="badge {{ $tenant->status === 'active' ? 'bg-success' : 'bg-danger' }} font-size-12 px-3 py-2 rounded-pill shadow-sm">
                {{ strtoupper($tenant->status) }} SUBSCRIBER
            </span>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <!-- Left Side: Subscriber & Active Subscription Details -->
            <div class="col-lg-6 border-end pe-lg-4">
                <h6 class="text-uppercase text-muted font-size-11 tracking-wider fw-bold mb-3.5">Account Details</h6>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=5f5af6&color=fff" class="rounded-4 border" width="56" height="56" alt="Org Logo">
                    <div>
                        <span class="text-muted d-block font-size-12 mb-0.5">Organization / Subscriber</span>
                        <h5 class="fw-bold text-slate-800 mb-0" style="font-family: 'Poppins', sans-serif;">{{ $tenant->name }}</h5>
                    </div>
                </div>

                <div class="bg-light p-3.5 rounded-3 mb-4 border border-slate-100">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted font-size-12 d-block">Admin Email</span>
                            <strong class="text-slate-800 font-size-13">{{ auth()->user()->email ?? 'subscriber@amds.test' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-size-12 d-block">Device Status</span>
                            <strong class="text-slate-800 font-size-13">{{ $devicesCount }} / {{ $tenant->max_devices }} Machines</strong>
                        </div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted font-size-11 tracking-wider fw-bold mb-3">Subscription Details</h6>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="bg-indigo-50 border border-indigo-100 p-3 rounded-3">
                            <span class="text-primary font-size-12 d-block fw-semibold">Active Plan</span>
                            <h5 class="fw-bold text-indigo-900 mb-0" style="font-family: 'Poppins', sans-serif;">{{ $currentPlan->name ?? 'Starter Plan' }}</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-amber-50 border border-amber-100 p-3 rounded-3">
                            <span class="text-amber-700 font-size-12 d-block fw-semibold">Remaining Days</span>
                            <h5 class="fw-bold {{ $remainingDays <= 5 ? 'text-danger' : 'text-amber-800' }} mb-0" style="font-family: 'Poppins', sans-serif;">
                                <i class="bx bx-time me-1 align-middle"></i> {{ $remainingDays }} Days
                            </h5>
                        </div>
                    </div>
                </div>

                <ul class="list-unstyled font-size-13 mb-0 text-slate-600 mt-4">
                    <li class="mb-2.5 d-flex align-items-center"><i class="bx bx-calendar text-primary me-2.5 font-size-16"></i> Expiration Date: &nbsp;<strong class="text-slate-800">{{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y (h:i A)') : 'Lifetime' }}</strong></li>
                    <li class="d-flex align-items-center"><i class="bx bx-shield-check text-success me-2.5 font-size-16"></i> Current Service Status: &nbsp;<strong class="text-success">Active & Online</strong></li>
                </ul>
            </div>

            <!-- Right Side: Upgrade / Renewal Server Package Purchase Form -->
            <div class="col-lg-6 ps-lg-4">
                <h6 class="text-uppercase text-primary font-size-11 tracking-wider fw-bold mb-3.5"><i class="bx bx-shopping-bag me-1"></i> Buy / Upgrade Subscription Plan</h6>

                <form action="{{ route('subscriber.checkout') }}" method="POST" id="formSubCheckout">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold text-slate-700 font-size-12 tracking-wide uppercase">Select Server Package Plan</label>
                        <select name="plan_id" id="selectPackagePlan" class="form-select border-secondary" required style="border: 2px solid #e2e8f0 !important; border-radius: 10px;">
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

                    <div class="mb-4">
                        <label class="form-label fw-bold text-slate-700 font-size-12 tracking-wide uppercase">Choose Billing Cycle</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="d-block cursor-pointer">
                                    <input class="btn-check" type="radio" name="billing_cycle" id="cycleMonthly" value="monthly" class="d-none" checked>
                                    <div class="p-3 billing-card text-center">
                                        <i class="bx bx-calendar-event font-size-22 mb-1.5 text-slate-400 d-block"></i>
                                        <div class="fw-bold font-size-13 text-slate-800">Monthly</div>
                                        <small class="text-slate-500 font-size-11">Month-to-month</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="d-block cursor-pointer">
                                    <input class="btn-check" type="radio" name="billing_cycle" id="cycleYearly" value="yearly" class="d-none">
                                    <div class="p-3 billing-card text-center">
                                        <i class="bx bx-crown font-size-22 mb-1.5 text-slate-400 d-block"></i>
                                        <div class="fw-bold font-size-13 text-slate-800">Yearly</div>
                                        <small class="text-success fw-bold font-size-11">Save on pricing</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Server Amount Price Calculation Box -->
                    <div class="p-3.5 rounded-4 d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, rgba(95, 90, 246, 0.04), rgba(16, 185, 129, 0.04)); border: 1px dashed rgba(95, 90, 246, 0.2);">
                        <div>
                            <span class="text-muted font-size-11 d-block mb-0.5">Calculated Amount:</span>
                            <h3 class="fw-bold text-primary mb-0" id="displayServerAmount" style="font-family: 'Poppins', sans-serif;">0.00 BDT</h3>
                        </div>
                        <span class="badge bg-primary px-3 py-2 font-size-12 rounded-pill shadow-sm" id="displayMaxDevices">Max 2 Machines</span>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold btn-lg shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669) !important; border: 0 !important; border-radius: 12px !important; min-height: 48px;">
                        <i class="bx bx-credit-card me-1.5 font-size-18 align-middle"></i> Pay & Renew via SSLCommerz
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quotas & Metrics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Machine Quota</span>
                    <h3 class="mt-2 mb-0 fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;">{{ $devicesCount }} <span class="font-size-16 text-muted">/ {{ $tenant->max_devices }}</span></h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-sm">
                    <i class="bx bx-chip"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Online Machines</span>
                    <h3 class="mt-2 mb-0 fw-bold text-success" id="statOnlineDevices" style="font-family: 'Poppins', sans-serif;">{{ $onlineDevicesCount }}</h3>
                </div>
                <div class="stat-icon bg-emerald-50 border border-emerald-100 text-emerald-600 shadow-sm">
                    <i class="bx bx-wifi"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Today's Punches</span>
                    <h3 class="mt-2 mb-0 fw-bold text-sky-600" id="statTodayPunches" style="font-family: 'Poppins', sans-serif;">{{ $todayPunches }}</h3>
                </div>
                <div class="stat-icon bg-sky-50 border border-sky-100 text-sky-600 shadow-sm">
                    <i class="bx bx-fingerprint"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="text-muted text-uppercase font-size-11 tracking-wider fw-bold">Biometric Users</span>
                    <h3 class="mt-2 mb-0 fw-bold text-amber-600" id="statUsersCount" style="font-family: 'Poppins', sans-serif;">{{ $usersCount }}</h3>
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600 shadow-sm">
                    <i class="bx bx-user-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tenant Attendance Log Feed -->
<div class="card border-0">
    <div class="card-header bg-white border-bottom py-3.5 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-slate-800" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-time me-1 text-primary align-middle font-size-18"></i> Realtime Punch Logs Feed</span>
        <div class="d-flex align-items-center gap-2.5">
            <span class="badge bg-success font-size-11 px-2.5 py-1.5" id="liveBadge"><i class="bx bx-pulse me-1"></i> Live feed (5s)</span>
            <a href="{{ route('subscriber.attendance.index') }}" class="btn btn-sm btn-outline-primary font-size-12 px-3 py-1.5 rounded-pill">View All Logs</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="border: 0 !important; border-radius: 0 !important;">
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
                        <tr><td colspan="6" class="text-center text-muted py-5">No attendance punches recorded for your organization yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyAdmsUrl() {
        const copyText = document.getElementById("adms-url");
        navigator.clipboard.writeText(copyText.value);

        const copyBtn = document.getElementById("copy-btn");
        const copyIcon = document.getElementById("copy-icon");
        const copyTextSpan = document.getElementById("copy-text");

        copyBtn.classList.remove("btn-primary");
        copyBtn.classList.add("btn-success");
        copyIcon.className = "bx bx-check me-1";
        copyTextSpan.textContent = "Copied URL";

        setTimeout(() => {
            copyBtn.classList.remove("btn-success");
            copyBtn.classList.add("btn-primary");
            copyIcon.className = "bx bx-copy me-1";
            copyTextSpan.textContent = "Copy URL";
        }, 2000);
    }

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
                        let statusBadge = 'bg-soft-info text-info';
                        if (log.status_label === 'Check In') {
                            statusBadge = 'bg-soft-success text-success';
                        } else if (log.status_label === 'Check Out') {
                            statusBadge = 'bg-soft-danger text-danger';
                        }

                        punchFeed.innerHTML += `
                            <tr>
                                <td><span class="fw-bold text-primary">${log.pin}</span></td>
                                <td>${log.user_name}</td>
                                <td><code>${log.device_serial}</code></td>
                                <td>${log.punched_at}</td>
                                <td><span class="badge ${statusBadge}">${log.status_label}</span></td>
                                <td><span class="badge bg-soft-secondary text-secondary">${log.verify_type_label}</span></td>
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
