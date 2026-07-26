@extends('layouts.subscriber')

@section('title', 'Edit Increment Rule')

@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-rule text-primary me-1.5 align-middle font-size-26"></i>Edit Increment Rule
        </h4>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('subscriber.hris.increment-rules.update', $incrementRule) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700">Rule Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $incrementRule->name) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700">Increment Based On <span class="text-danger">*</span></label>
                    <select class="form-select" name="increment_based_on" required>
                        <option value="basic" {{ old('increment_based_on', $incrementRule->increment_based_on) === 'basic' ? 'selected' : '' }}>Basic Salary</option>
                        <option value="gross" {{ old('increment_based_on', $incrementRule->increment_based_on) === 'gross' ? 'selected' : '' }}>Gross Salary</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-slate-700">Special Max %</label>
                    <input type="number" class="form-control" name="special_max_percentage" value="{{ old('special_max_percentage', $incrementRule->special_max_percentage) }}" step="0.01" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Joining Date From</label>
                    <input type="date" class="form-control" name="joining_date_from" value="{{ old('joining_date_from', $incrementRule->joining_date_from?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Joining Date To</label>
                    <input type="date" class="form-control" name="joining_date_to" value="{{ old('joining_date_to', $incrementRule->joining_date_to?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700">Year Start Date</label>
                    <input type="date" class="form-control" name="year_start_date" value="{{ old('year_start_date', $incrementRule->year_start_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $incrementRule->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-slate-700" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="{{ route('subscriber.hris.increment-rules.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5">Update Rule</button>
            </div>
        </form>
    </div>
</div>
@endsection
