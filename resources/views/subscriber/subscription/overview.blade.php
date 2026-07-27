@extends('layouts.subscriber')

@section('title', 'Subscription & Account Overview')

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
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">System Setup</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">Subscription & Account Overview</h4>
    </div>
</div>

<div class="card border-0 mb-4">
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
                    <li class="mb-2.5 d-flex align-items-center"><i class="bx bx-calendar text-primary me-2.5 font-size-16"></i> Expiration: &nbsp;<strong class="text-slate-800">{{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y (h:i A)') : 'Lifetime' }}</strong></li>
                    <li class="d-flex align-items-center"><i class="bx bx-shield-check text-success me-2.5 font-size-16"></i> Status: &nbsp;<strong class="text-success">Active & Online</strong></li>
                    <li class="d-flex align-items-center"><i class="bx bx-chip text-info me-2.5 font-size-16"></i> Devices: &nbsp;<strong class="text-slate-800">{{ $devicesCount }} / {{ $tenant->max_devices }}</strong></li>
                </ul>
            </div>

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
                                    <input class="btn-check" type="radio" name="billing_cycle" id="cycleMonthly" value="monthly" checked>
                                    <div class="p-3 billing-card text-center">
                                        <i class="bx bx-calendar-event font-size-22 mb-1.5 text-slate-400 d-block"></i>
                                        <div class="fw-bold font-size-13 text-slate-800">Monthly</div>
                                        <small class="text-slate-500 font-size-11">Month-to-month</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="d-block cursor-pointer">
                                    <input class="btn-check" type="radio" name="billing_cycle" id="cycleYearly" value="yearly">
                                    <div class="p-3 billing-card text-center">
                                        <i class="bx bx-crown font-size-22 mb-1.5 text-slate-400 d-block"></i>
                                        <div class="fw-bold font-size-13 text-slate-800">Yearly</div>
                                        <small class="text-success fw-bold font-size-11">Save on pricing</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

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
    });
</script>
@endpush