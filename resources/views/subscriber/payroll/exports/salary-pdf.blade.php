@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>SALARY SHEET</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total Employees: {{ $salaryData->count() }}</p>
</div>

@php $deptGroups = $salaryData->groupBy('dept_name'); @endphp
@foreach($deptGroups as $deptName => $items)
    <div class="dept-header">{{ $deptName ?: 'N/A' }} ({{ $items->count() }} employees)</div>
    <table>
        <thead>
            <tr>
                <th style="text-align:left">#</th>
                <th style="text-align:left">ID</th>
                <th style="text-align:left">Name</th>
                <th style="text-align:left">Designation</th>
                <th>Basic</th><th>House</th><th>Medical</th><th>Conv.</th><th>Other</th><th>Gross</th>
                <th>P</th><th>A</th><th>L</th><th>Late</th><th>Late Ded</th><th>Abs Ded</th><th>Tax</th><th>PF</th><th>Bonus</th><th>OT</th><th>Adv</th><th>Net</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $s)
            <tr>
                <td style="text-align:left">{{ $loop->iteration }}</td>
                <td style="text-align:left">{{ $s->emp_code }}</td>
                <td style="text-align:left">{{ $s->emp_name }}</td>
                <td style="text-align:left">{{ $s->designation_name }}</td>
                <td>{{ number_format($s->basic_salary, 0) }}</td>
                <td>{{ number_format($s->house_rent, 0) }}</td>
                <td>{{ number_format($s->medical, 0) }}</td>
                <td>{{ number_format($s->conveyance, 0) }}</td>
                <td>{{ number_format($s->other_allowances, 0) }}</td>
                <td class="text-primary">{{ number_format($s->gross_salary, 0) }}</td>
                <td>{{ $s->present_days }}</td>
                <td class="{{ $s->absent_days > 0 ? 'text-danger' : '' }}">{{ $s->absent_days }}</td>
                <td>{{ $s->leave_days }}</td>
                <td>{{ $s->total_late_minutes }}m</td>
                <td class="{{ $s->late_deduction > 0 ? 'text-danger' : '' }}">{{ $s->late_deduction > 0 ? number_format($s->late_deduction, 0) : '—' }}</td>
                <td class="{{ $s->absent_deduction > 0 ? 'text-danger' : '' }}">{{ $s->absent_deduction > 0 ? number_format($s->absent_deduction, 0) : '—' }}</td>
                <td>{{ $s->tax_deduction > 0 ? number_format($s->tax_deduction, 0) : '—' }}</td>
                <td>{{ $s->pf_deduction > 0 ? number_format($s->pf_deduction, 0) : '—' }}</td>
                <td class="{{ $s->bonus_amount > 0 ? 'text-success' : '' }}">{{ $s->bonus_amount > 0 ? number_format($s->bonus_amount, 0) : '—' }}</td>
                <td class="{{ $s->ot_payable > 0 ? 'text-success' : '' }}">{{ $s->ot_payable > 0 ? number_format($s->ot_payable, 0) : '—' }}</td>
                <td>{{ $s->advance_deduction > 0 ? number_format($s->advance_deduction, 0) : '—' }}</td>
                <td class="text-primary">{{ number_format($s->net_payable, 0) }}</td>
                <td><span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
            </tr>
            @endforeach
            <tr class="subtotal">
                <td colspan="4" style="text-align:left">Subtotal — {{ $deptName ?: 'N/A' }}</td>
                <td>{{ number_format($items->sum('basic_salary'), 0) }}</td>
                <td>{{ number_format($items->sum('house_rent'), 0) }}</td>
                <td>{{ number_format($items->sum('medical'), 0) }}</td>
                <td>{{ number_format($items->sum('conveyance'), 0) }}</td>
                <td>{{ number_format($items->sum('other_allowances'), 0) }}</td>
                <td>{{ number_format($items->sum('gross_salary'), 0) }}</td>
                <td>{{ $items->sum('present_days') }}</td>
                <td class="text-danger">{{ $items->sum('absent_days') }}</td>
                <td>{{ $items->sum('leave_days') }}</td>
                <td>{{ $items->sum('total_late_minutes') }}m</td>
                <td class="text-danger">{{ number_format($items->sum('late_deduction'), 0) }}</td>
                <td class="text-danger">{{ number_format($items->sum('absent_deduction'), 0) }}</td>
                <td>{{ number_format($items->sum('tax_deduction'), 0) }}</td>
                <td>{{ number_format($items->sum('pf_deduction'), 0) }}</td>
                <td class="text-success">{{ number_format($items->sum('bonus_amount'), 0) }}</td>
                <td class="text-success">{{ number_format($items->sum('ot_payable'), 0) }}</td>
                <td>{{ number_format($items->sum('advance_deduction'), 0) }}</td>
                <td class="text-primary">{{ number_format($items->sum('net_payable'), 0) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endforeach

<table>
    <tr class="grand-total">
        <td colspan="4" style="text-align:left">GRAND TOTAL ({{ $salaryData->count() }} employees)</td>
        <td>{{ number_format($salaryData->sum('basic_salary'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('house_rent'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('medical'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('conveyance'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('other_allowances'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('gross_salary'), 0) }}</td>
        <td>{{ $salaryData->sum('present_days') }}</td>
        <td>{{ $salaryData->sum('absent_days') }}</td>
        <td>{{ $salaryData->sum('leave_days') }}</td>
        <td>{{ $salaryData->sum('total_late_minutes') }}m</td>
        <td>{{ number_format($salaryData->sum('late_deduction'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('absent_deduction'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('tax_deduction'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('pf_deduction'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('bonus_amount'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('ot_payable'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('advance_deduction'), 0) }}</td>
        <td>{{ number_format($salaryData->sum('net_payable'), 0) }}</td>
        <td></td>
    </tr>
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
