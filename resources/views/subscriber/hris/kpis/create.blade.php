@extends('layouts.subscriber')

@section('title', 'Add KPI Goal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Add KPI Goal</h4>
            <div class="page-title-right">
                <a href="{{ route('subscriber.hris.kpis.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('subscriber.hris.kpis.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="employee_profile_id" class="form-label fw-medium">Select Employee <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_profile_id') is-invalid @enderror" id="employee_profile_id" name="employee_profile_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_profile_id') == $emp->id ? 'selected' : '' }}>{{ $emp->user->name ?? 'N/A' }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                        @error('employee_profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="goal_title" class="form-label fw-medium">Goal Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('goal_title') is-invalid @enderror" id="goal_title" name="goal_title" value="{{ old('goal_title') }}" required placeholder="e.g. Complete phase 1 of ADMS project">
                        @error('goal_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium">Goal Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Describe the goal deliverables...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target_date" class="form-label fw-medium">Target Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('target_date') is-invalid @enderror" id="target_date" name="target_date" value="{{ old('target_date') }}" required>
                            @error('target_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="weightage" class="form-label fw-medium">Goal Weightage (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('weightage') is-invalid @enderror" id="weightage" name="weightage" value="{{ old('weightage', 25) }}" min="1" max="100" required>
                            @error('weightage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="defined" {{ old('status') === 'defined' ? 'selected' : '' }}>Defined</option>
                                <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="achieved" {{ old('status') === 'achieved' ? 'selected' : '' }}>Achieved</option>
                                <option value="missed" {{ old('status') === 'missed' ? 'selected' : '' }}>Missed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="score_rating" class="form-label fw-medium">Score Rating (1-10)</label>
                            <input type="number" class="form-control @error('score_rating') is-invalid @enderror" id="score_rating" name="score_rating" value="{{ old('score_rating') }}" min="1" max="10" placeholder="e.g. 8">
                            @error('score_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">
                            <i class="bx bx-save me-1"></i> Save KPI Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
