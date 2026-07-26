@extends('layouts.subscriber')

@section('title', 'Employee Profile Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Employee Details: {{ $employee->user->name ?? 'N/A' }}</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 g-4">
    <!-- Left Column: Summary Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-5">
                <div class="avatar-lg mx-auto mb-4 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                    <i class="bx bx-user font-size-48"></i>
                </div>
                <h5 class="fw-bold mb-1 text-dark">{{ $employee->user->name ?? 'N/A' }}</h5>
                <p class="text-muted font-size-13 mb-3">{{ $employee->designation->title ?? 'No Designation' }}</p>
                
                <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-warning' }} px-3 py-2 font-size-12 rounded-pill mb-4">
                    {{ strtoupper($employee->status) }}
                </span>

                <div class="border-top pt-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Employee ID:</span>
                        <strong class="text-dark"><code>{{ $employee->employee_id }}</code></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email:</span>
                        <strong class="text-dark">{{ $employee->user->email ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phone:</span>
                        <strong class="text-dark">{{ $employee->phone_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Joining Date:</span>
                        <strong class="text-dark">{{ \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Tabs for Address & Bank details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bx bx-list-check text-primary me-2 font-size-20"></i> Employee Credentials & Master Data
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted font-size-12 fw-bold mb-3"><i class="bx bx-map me-1"></i> Geographic Address Details</h6>
                        @php
                            $address = $employee->addresses()->first();
                        @endphp
                        @if($address)
                            <div class="bg-light p-3 rounded">
                                <p class="mb-1 text-dark"><strong>Address:</strong> {{ $address->address_line_1 }}</p>
                                <p class="mb-1 text-dark"><strong>City & State:</strong> {{ $address->city }}, {{ $address->state }}</p>
                                <p class="mb-0 text-dark"><strong>Zip Code & Country:</strong> {{ $address->zip_code }}, {{ $address->country }}</p>
                            </div>
                        @else
                            <p class="text-muted">No address details registered.</p>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted font-size-12 fw-bold mb-3"><i class="bx bx-credit-card me-1"></i> Bank Account details</h6>
                        @php
                            $bank = $employee->bankInfo()->first();
                        @endphp
                        @if($bank)
                            <div class="bg-light p-3 rounded">
                                <p class="mb-1 text-dark"><strong>Bank Name:</strong> {{ $bank->bank_name }}</p>
                                <p class="mb-1 text-dark"><strong>Branch:</strong> {{ $bank->branch_name }}</p>
                                <p class="mb-1 text-dark"><strong>Account Name:</strong> {{ $bank->account_name }}</p>
                                <p class="mb-1 text-dark"><strong>Account Number:</strong> <code>{{ $bank->account_number }}</code></p>
                                <p class="mb-0 text-dark"><strong>Payment Mode:</strong> <span class="badge bg-soft-info text-info">{{ strtoupper($bank->payment_mode) }}</span></p>
                            </div>
                        @else
                            <p class="text-muted">No bank details registered.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
