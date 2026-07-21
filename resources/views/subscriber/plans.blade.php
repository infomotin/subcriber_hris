@extends('layouts.app')

@section('title', 'Subscription Plans & SSLCommerz Payment')

@section('content')
<div class="page-title-box">
    <h4>Subscription Plans & Quota Upgrade</h4>
</div>

<div class="row g-4">
    @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card h-100 {{ $tenant->subscription_plan_id == $plan->id ? 'border-2 border-primary' : '' }}">
                <div class="card-body">
                    @if($tenant->subscription_plan_id == $plan->id)
                        <span class="badge bg-success float-end">Current Active Plan</span>
                    @endif
                    <h5 class="fw-bold text-primary">{{ $plan->name }}</h5>
                    <p class="text-muted font-size-13">{{ $plan->description }}</p>
                    <h2 class="fw-bold my-3">{{ number_format($plan->price_monthly, 0) }} <span class="font-size-14 font-weight-normal text-muted">BDT/month</span></h2>

                    <form action="{{ route('subscriber.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <div class="mb-3">
                            <select name="billing_cycle" class="form-select form-select-sm">
                                <option value="monthly">Monthly Billing ({{ number_format($plan->price_monthly, 0) }} BDT)</option>
                                <option value="yearly">Yearly Billing ({{ number_format($plan->price_yearly, 0) }} BDT)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-credit-card me-1"></i> Pay via SSLCommerz</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
