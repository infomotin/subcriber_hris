@extends('layouts.subscriber')
@section('title', 'Create Advance Type')
@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div><span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span><h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-plus-circle text-primary me-1.5 align-middle font-size-26"></i>New Advance Type</h4></div>
    <a href="{{ route('subscriber.hris.advance-types.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-arrow-back me-1"></i> Back</a>
</div>
<div class="card border-0 shadow-sm" style="border-radius:14px;max-width:600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.advance-types.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label fw-semibold text-slate-700">Name <span class="text-danger">*</span></label><input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="e.g. Emergency Advance" required>@error('name') <div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label fw-semibold text-slate-700">Code <span class="text-danger">*</span></label><input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="e.g. EMERG" required maxlength="30">@error('code') <div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label fw-semibold text-slate-700">Payment Mode <span class="text-danger">*</span></label>
                <select class="form-select" name="payment_mode" required>
                    <option value="one_time" {{ old('payment_mode') === 'one_time' ? 'selected' : '' }}>One Time Paid (Lump Sum)</option>
                    <option value="monthly_installment" {{ old('payment_mode') === 'monthly_installment' ? 'selected' : '' }}>Paid By Monthly (Installments)</option>
                </select>
            </div>
            <div class="mb-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}><label class="form-check-label fw-semibold text-slate-700">Active</label></div></div>
            <div class="text-end pt-3 border-top"><a href="{{ route('subscriber.hris.advance-types.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a><button type="submit" class="btn btn-primary rounded-pill px-5">Create</button></div>
        </form>
    </div>
</div>
@endsection
