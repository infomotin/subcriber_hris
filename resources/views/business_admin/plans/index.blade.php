@extends('layouts.business_admin')

@section('title', 'Package Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bx bx-package me-2 text-primary"></i> Subscription Package Plans
    </h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPlan">
        <i class="bx bx-plus me-1"></i> Add Package Plan
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #fff1f2 !important; border-left: 4px solid #f43f5e !important;">
        <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-primary mb-0" style="font-family: 'Poppins', sans-serif;">
                            {{ $plan->name }}
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-soft-secondary rounded-pill" data-bs-toggle="dropdown" style="border: none; background: transparent;">
                                <i class="bx bx-dots-vertical-rounded font-size-18"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                <li>
                                    <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalEditPlan{{ $plan->id }}">
                                        <i class="bx bx-edit me-2 text-primary"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.business.plans.destroy', $plan) }}" method="POST"
                                          onsubmit="return confirm('Delete this plan? Subscribers on this plan may be affected.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item py-2 text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="bx bx-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-muted font-size-13 mb-3">{{ $plan->description ?? 'No description' }}</p>
                    <h2 class="fw-bold my-3" style="font-family: 'Poppins', sans-serif;">
                        {{ number_format($plan->price_monthly, 0) }}
                        <span class="font-size-14 font-weight-normal text-muted">BDT/mo</span>
                    </h2>
                    <ul class="list-unstyled mb-4 font-size-13">
                        <li class="mb-2">
                            <i class="bx bx-chip text-primary me-2"></i>
                            Up to <strong>{{ $plan->max_devices }}</strong> Biometric Devices
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-user-plus text-success me-2"></i>
                            Up to <strong>{{ $plan->max_employees }}</strong> Employees
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-calendar text-info me-2"></i>
                            Year Plan: <strong>{{ number_format($plan->price_yearly, 0) }} BDT/yr</strong>
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-building text-warning me-2"></i>
                            {{ $plan->tenants_count }} Active Subscribers
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Edit Plan Modal -->
        <div class="modal fade" id="modalEditPlan{{ $plan->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.business.plans.update', $plan) }}" method="POST" class="modal-content" style="border-radius: 16px;">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;">
                            <i class="bx bx-edit text-primary me-1"></i> Edit Plan: {{ $plan->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold font-size-13">Package Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ $plan->name }}">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold font-size-13">Monthly Price (BDT)</label>
                                <input type="number" name="price_monthly" class="form-control" required value="{{ $plan->price_monthly }}" step="0.01">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold font-size-13">Yearly Price (BDT)</label>
                                <input type="number" name="price_yearly" class="form-control" required value="{{ $plan->price_yearly }}" step="0.01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold font-size-13">Max Biometric Devices</label>
                                <input type="number" name="max_devices" class="form-control" required value="{{ $plan->max_devices }}" min="1">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold font-size-13">
                                    <i class="bx bx-user-plus text-success me-1"></i> Max Employees
                                </label>
                                <input type="number" name="max_employees" class="form-control" required value="{{ $plan->max_employees }}" min="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold font-size-13">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ $plan->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Update Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="modalAddPlan" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.plans.store') }}" method="POST" class="modal-content" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="font-family: 'Poppins', sans-serif;">
                    <i class="bx bx-plus-circle text-primary me-1"></i> Create Package Plan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Package Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Super Business">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">Monthly Price (BDT)</label>
                        <input type="number" name="price_monthly" class="form-control" required value="3000" step="0.01">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">Yearly Price (BDT)</label>
                        <input type="number" name="price_yearly" class="form-control" required value="30000" step="0.01">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">Max Biometric Devices</label>
                        <input type="number" name="max_devices" class="form-control" required value="5" min="1">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">
                            <i class="bx bx-user-plus text-success me-1"></i> Max Employees
                        </label>
                        <input type="number" name="max_employees" class="form-control" required value="50" min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="What's included in this plan?"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bx bx-check-circle me-1"></i> Create Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
