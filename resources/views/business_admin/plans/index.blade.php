@extends('layouts.business_admin')

@section('title', 'Package Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Subscription Package Plans</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPlan">
        <i class="bx bx-plus me-1"></i> Add Package Plan
    </button>
</div>

<div class="row g-4">
    @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="fw-bold text-primary">{{ $plan->name }}</h5>
                    <p class="text-muted font-size-13">{{ $plan->description }}</p>
                    <h2 class="fw-bold my-3">{{ number_format($plan->price_monthly, 0) }} <span class="font-size-14 font-weight-normal text-muted">BDT/mo</span></h2>
                    <ul class="list-unstyled mb-4 font-size-13">
                        <li class="mb-2"><i class="bx bx-check text-success me-2"></i> Up to <strong>{{ $plan->max_devices }}</strong> Biometric Devices</li>
                        <li class="mb-2"><i class="bx bx-check text-success me-2"></i> Year Plan: <strong>{{ number_format($plan->price_yearly, 0) }} BDT/yr</strong></li>
                        <li class="mb-2"><i class="bx bx-check text-success me-2"></i> {{ $plan->tenants_count }} Active Subscribers</li>
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="modalAddPlan" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.plans.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Package Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Package Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Super Business">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Monthly Price (BDT)</label>
                        <input type="number" name="price_monthly" class="form-control" required value="3000">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Yearly Price (BDT)</label>
                        <input type="number" name="price_yearly" class="form-control" required value="30000">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Max Biometric Devices Limit</label>
                    <input type="number" name="max_devices" class="form-control" required value="5">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4">Create Package</button>
            </div>
        </form>
    </div>
</div>
@endsection
