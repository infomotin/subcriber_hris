<div class="salary-breakdown">
    <div class="p-3 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold font-size-13">{{ $emp->employee_id }}</span>
            <span class="font-size-12 text-muted">{{ $emp->user?->name ?? $emp->employee_id }}</span>
        </div>
        <div class="font-size-12">
            <div class="d-flex justify-content-between py-1"><span>Present / Absent</span><span class="fw-medium">{{ $pd['present_days'] }} / {{ $pd['absent_days'] }}</span></div>
            <div class="d-flex justify-content-between py-1"><span>Leave / Holiday / Weekend</span><span class="fw-medium">{{ $pd['leave_days'] }} / {{ $pd['holiday_days'] }} / {{ $pd['weekend_days'] }}</span></div>
            <div class="d-flex justify-content-between py-1"><span>Late Minutes</span><span class="fw-medium {{ $pd['total_late_minutes'] > 0 ? 'text-danger' : '' }}">{{ $pd['total_late_minutes'] }}m</span></div>
            <div class="d-flex justify-content-between py-1"><span>OT Minutes</span><span class="fw-medium {{ $pd['total_ot_minutes'] > 0 ? 'text-success' : '' }}">{{ $pd['total_ot_minutes'] }}m</span></div>
            @if($pd['tenure_months'] > 0)
                <div class="d-flex justify-content-between py-1"><span>Tenure</span><span class="fw-medium">{{ $pd['tenure_months'] }}mo</span></div>
                <div class="d-flex justify-content-between py-1"><span>Bonus Eligible</span><span class="fw-medium">{{ $pd['bonus_eligible_percent'] }}%</span></div>
            @endif
        </div>
    </div>

    <div class="p-3 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
        <h6 class="fw-bold font-size-12 mb-2">Earnings</h6>
        <div class="font-size-12">
            @php $earnings = [
                'Basic Salary' => $pd['basic_salary'],
                'House Rent' => $pd['house_rent'],
                'Medical' => $pd['medical'],
                'Conveyance' => $pd['conveyance'],
                'Other Allowances' => $pd['other_allowances'],
                'OT Payable' => $pd['ot_payable'],
                'Bonus' => $pd['bonus_amount'],
            ]; @endphp
            @foreach($earnings as $label => $amount)
                <div class="d-flex justify-content-between py-1">
                    <span>{{ $label }}</span>
                    <span class="fw-medium {{ $label === 'OT Payable' && $amount > 0 ? 'text-success' : '' }}">{{ number_format($amount, 0) }}</span>
                </div>
            @endforeach
            <div class="border-top pt-1 mt-1 d-flex justify-content-between fw-bold font-size-13">
                <span>Gross</span>
                <span>{{ number_format($pd['gross_salary'] + $pd['ot_payable'] + $pd['bonus_amount'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="p-3 rounded-3 mb-3" style="background:#fef2f2; border:1px solid #fecaca;">
        <h6 class="fw-bold font-size-12 mb-2 text-danger">Deductions</h6>
        <div class="font-size-12">
            @php $deductions = [
                'Late Deduction' => $pd['late_deduction'],
                'Absent Deduction' => $pd['absent_deduction'],
                'Advance Deduction' => $pd['advance_deduction'],
                'Tax Deduction' => $pd['tax_deduction'],
                'PF Deduction' => $pd['pf_deduction'],
            ]; @endphp
            @foreach($deductions as $label => $amount)
                @if($amount > 0)
                    <div class="d-flex justify-content-between py-1">
                        <span>{{ $label }}</span>
                        <span class="fw-medium">- {{ number_format($amount, 0) }}</span>
                    </div>
                @endif
            @endforeach
            @if(array_sum($deductions) == 0)
                <span class="text-muted">No deductions</span>
            @endif
            <div class="border-top pt-1 mt-1 d-flex justify-content-between fw-bold font-size-13 text-danger">
                <span>Total Deductions</span>
                <span>- {{ number_format($pd['late_deduction'] + $pd['absent_deduction'] + $pd['advance_deduction'] + $pd['tax_deduction'] + $pd['pf_deduction'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="p-3 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0;">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold font-size-14">Net Payable</span>
            <span class="fw-bold font-size-16 text-success">{{ number_format($pd['net_payable'], 2) }}</span>
        </div>
        <div class="font-size-11 text-muted mt-1">
            Daily Rate: {{ number_format($pd['daily_rate'], 2) }} &bull;
            Per-min Rate: {{ number_format($pd['per_minute_rate'], 4) }}
        </div>
    </div>
</div>