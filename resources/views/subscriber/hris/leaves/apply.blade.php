@extends('layouts.subscriber')

@section('title', 'Leave Application')

@section('content')
<style>
    .balance-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
        background: #fff;
    }
    .balance-card:hover { border-color: #5f5af6; box-shadow: 0 4px 12px rgba(95,90,246,0.08); }
    .balance-card .type-code {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #5f5af6;
    }
    .balance-card .available {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .balance-card .label {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .balance-card .spent {
        font-size: 0.75rem;
        color: #dc2626;
    }
    .balance-card .allocated {
        font-size: 0.75rem;
        color: #64748b;
    }
    .balance-card.selected {
        border-color: #5f5af6;
        background: rgba(95,90,246,0.04);
    }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-calendar-check text-primary me-1.5 align-middle font-size-26"></i>Leave Application
        </h4>
    </div>
</div>

<form method="POST" action="{{ route('subscriber.hris.leaves.store') }}" id="leaveForm">
    @csrf

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                <i class="bx bx-user text-primary me-1.5 align-middle font-size-20"></i> Employee
            </h5>
            <select class="form-select" name="employee_profile_id" id="employeeSelect" required>
                <option value="">— Select Employee —</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ old('employee_profile_id') == $emp->id ? 'selected' : '' }}>{{ $emp->user?->name }} ({{ $emp->employee_id }})</option>
                @endforeach
            </select>
            @error('employee_profile_id') <div class="text-danger font-size-12 mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div id="balanceSection" style="display: none;">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                    <i class="bx bx-wallet text-primary me-1.5 align-middle font-size-20"></i> Leave Balance
                </h5>
                <div class="row g-3" id="balanceCards"></div>
            </div>
        </div>

        <input type="hidden" name="leave_type_id" id="leave_type_id">

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-slate-800 mb-3" style="font-family: 'Poppins', sans-serif;">
                    <i class="bx bx-detail text-primary me-1.5 align-middle font-size-20"></i> Leave Details
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-slate-700">Total Days</label>
                        <input type="text" class="form-control" id="totalDaysDisplay" readonly value="0">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-slate-700">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('subscriber.hris.leaves.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-pill px-5" style="height: 44px;">
                <i class="bx bx-send me-1.5 align-middle font-size-18"></i> Submit Application
            </button>
        </div>
    </div>
</form>

@if ($errors->any())
    <div class="alert alert-danger rounded-pill px-4 mt-3">
        <i class="bx bx-error-circle me-1 align-middle"></i>
        @foreach ($errors->all() as $error) {{ $error }} @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('employeeSelect').addEventListener('change', function() {
    const empId = this.value;
    const section = document.getElementById('balanceSection');
    const cards = document.getElementById('balanceCards');

    if (!empId) {
        section.style.display = 'none';
        return;
    }

    cards.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="bx bx-loader bx-spin font-size-24 d-block mb-2"></i>Loading balance...</div>';
    section.style.display = 'block';

    fetch('{{ route("subscriber.hris.leaves.balance") }}?employee_profile_id=' + empId)
        .then(r => r.json())
        .then(data => {
            cards.innerHTML = '';
            data.forEach(t => {
                const pct = t.allocated > 0 ? Math.round((t.spent / t.allocated) * 100) : 0;
                const col = document.createElement('div');
                col.className = 'col-md-4 col-lg-3';
                col.innerHTML = `
                    <div class="balance-card" data-type-id="${t.id}" onclick="selectType(this, ${t.id}, ${t.available})">
                        <div class="type-code">${t.code}</div>
                        <div class="available">${t.available}</div>
                        <div class="label">Days Available</div>
                        <div class="d-flex justify-content-between mt-2 px-1">
                            <span class="allocated">${t.allocated} allotted</span>
                            <span class="spent">${t.spent} used</span>
                        </div>
                        <div class="progress mt-2" style="height: 3px;">
                            <div class="progress-bar" style="width: ${pct}%; background: ${pct >= 80 ? '#dc2626' : pct >= 50 ? '#f59e0b' : '#10b981'};"></div>
                        </div>
                    </div>
                `;
                cards.appendChild(col);
            });
        });
});

function selectType(el, typeId, available) {
    if (available <= 0) return;
    document.querySelectorAll('.balance-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('leave_type_id').value = typeId;
}

document.querySelectorAll('input[name="start_date"], input[name="end_date"]').forEach(el => {
    el.addEventListener('change', calcDays);
});

function calcDays() {
    const start = document.querySelector('input[name="start_date"]').value;
    const end = document.querySelector('input[name="end_date"]').value;
    if (start && end) {
        const s = new Date(start), e = new Date(end);
        const diff = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('totalDaysDisplay').value = diff > 0 ? diff + ' days' : '0';
    }
}
</script>
@endpush
