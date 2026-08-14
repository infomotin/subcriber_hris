@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>EMPLOYEE SALARY REPORT</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total: {{ $salaryData->count() }} employees</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">#</th>
            <th style="text-align:left">Employee</th>
            <th style="text-align:left">Dept</th>
            <th style="text-align:left">Designation</th>
            <th>Gross</th><th>Present</th><th>Absent</th><th>Late (min)</th><th>Late Ded</th><th>Tax</th><th>PF</th><th>Bonus</th><th>OT</th><th>Advance</th><th>Net</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salaryData as $i => $s)
        <tr>
            <td style="text-align:left">{{ $i + 1 }}</td>
            <td style="text-align:left">{{ $s->emp_code }} - {{ $s->emp_name }}</td>
            <td style="text-align:left">{{ $s->dept_name }}</td>
            <td style="text-align:left">{{ $s->designation_name }}</td>
            <td>{{ number_format($s->gross_salary, 0) }}</td>
            <td>{{ $s->present_days }}</td>
            <td class="{{ $s->absent_days > 0 ? 'text-danger' : '' }}">{{ $s->absent_days }}</td>
            <td>{{ $s->total_late_minutes }}</td>
            <td class="{{ $s->late_deduction > 0 ? 'text-danger' : '' }}">{{ $s->late_deduction > 0 ? number_format($s->late_deduction, 0) : '—' }}</td>
            <td>{{ $s->tax_deduction > 0 ? number_format($s->tax_deduction, 0) : '—' }}</td>
            <td>{{ $s->pf_deduction > 0 ? number_format($s->pf_deduction, 0) : '—' }}</td>
            <td class="{{ $s->bonus_amount > 0 ? 'text-success' : '' }}">{{ $s->bonus_amount > 0 ? number_format($s->bonus_amount, 0) : '—' }}</td>
            <td class="{{ $s->ot_payable > 0 ? 'text-success' : '' }}">{{ $s->ot_payable > 0 ? number_format($s->ot_payable, 0) : '—' }}</td>
            <td>{{ $s->advance_deduction > 0 ? number_format($s->advance_deduction, 0) : '—' }}</td>
            <td class="text-primary">{{ number_format($s->net_payable, 0) }}</td>
            <td><span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tr class="grand-total">
        <td colspan="4" style="text-align:left">TOTAL</td>
        <td>{{ number_format($salaryData->sum('gross_salary'), 0) }}</td>
        <td>{{ $salaryData->sum('present_days') }}</td>
        <td>{{ $salaryData->sum('absent_days') }}</td>
        <td>{{ $salaryData->sum('total_late_minutes') }}</td>
        <td>{{ number_format($salaryData->sum('late_deduction'), 0) }}</td>
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
