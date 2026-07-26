@extends('layouts.subscriber')

@section('title', $config['title'] ?? 'HRIS Module')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ $config['title'] ?? 'HRIS Module' }}</h4>
        </div>
        <p class="text-muted font-size-14">{{ $config['subtitle'] ?? '' }}</p>
    </div>
</div>

<div class="row g-4">
    <!-- Form Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bx bx-edit text-primary me-2 font-size-20"></i> Add New Record
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('subscriber.hris.general.submit', $module) }}" method="POST">
                    @csrf
                    @foreach($config['fields'] ?? [] as $field)
                        <div class="mb-3">
                            <label for="{{ $field['name'] }}" class="form-label fw-medium">{{ $field['label'] }}</label>
                            
                            @if(($field['type'] ?? 'text') === 'select')
                                <select class="form-select" id="{{ $field['name'] }}" name="{{ $field['name'] }}">
                                    @foreach($field['options'] ?? [] as $val => $lbl)
                                        <option value="{{ $val }}" {{ isset($field['value']) && $field['value'] == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" 
                                       class="form-control" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}" 
                                       placeholder="{{ $field['placeholder'] ?? '' }}" 
                                       value="{{ $field['value'] ?? '' }}">
                            @endif
                        </div>
                    @endforeach
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">
                            <i class="bx bx-save me-1"></i> Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data List Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bx bx-list-ul text-primary me-2 font-size-20"></i> Existing Records
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                @foreach($config['headers'] ?? [] as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                                <th class="text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($config['dummy_data'] ?? [] as $row)
                                <tr>
                                    <td>{{ $row['col1'] ?? '' }}</td>
                                    <td>{{ $row['col2'] ?? '' }}</td>
                                    <td>{{ $row['col3'] ?? '' }}</td>
                                    <td>
                                        @if(($row['col4'] ?? '') === 'Paid Holiday' || ($row['col4'] ?? '') === 'Active' || ($row['col4'] ?? '') === 'Approved & Verified' || ($row['col4'] ?? '') === 'Approved & Disbursed' || ($row['col4'] ?? '') === 'Disbursed')
                                            <span class="badge bg-soft-success text-success px-2 py-1">{{ $row['col4'] }}</span>
                                        @elseif(($row['col4'] ?? '') === 'Pending Review' || ($row['col4'] ?? '') === 'Pending manager approval' || ($row['col4'] ?? '') === 'Pending Verification')
                                            <span class="badge bg-soft-warning text-warning px-2 py-1">{{ $row['col4'] }}</span>
                                        @elseif(($row['col4'] ?? '') === 'Generate Report')
                                            <button class="btn btn-sm btn-outline-info rounded-pill py-1 font-size-12">
                                                <i class="bx bx-download me-1"></i> Generate
                                            </button>
                                        @else
                                            {{ $row['col4'] ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <button class="btn btn-sm btn-light border-0 me-1" title="Edit">
                                            <i class="bx bx-edit text-muted"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0 text-danger" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($config['headers'] ?? []) + 1 }}" class="text-center py-4 text-muted">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
