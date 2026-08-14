@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
@php
    $totalGross = $salaryData->sum('gross_salary');
    $totalNet = $salaryData->sum('net_payable');
    $totalBonus = $salaryData->sum('bonus_amount');
    $totalLate = $salaryData->sum('late_deduction');
    $totalTax = $salaryData->sum('tax_deduction');
    $totalPf = $salaryData->sum('pf_deduction');
    $totalAbsent = $salaryData->sum('absent_deduction');
    $totalAdvance = $salaryData->sum('advance_deduction');
    $presentCount = $attendanceData->where('status', 'present')->count();
    $absentCount = $attendanceData->where('status', 'absent')->count();
    $leaveCount = $attendanceData->where('status', 'leave')->count();
@endphp

<div class="header">
    <h1>VISUAL OVERVIEW</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<table>
    <thead><tr><th colspan="2" style="text-align:left; background:#4f46e5; color:#fff;">SALARY SUMMARY</th></tr></thead>
    <tbody>
        <tr><td style="text-align:left">Total Employees Processed</td><td style="text-align:right">{{ $salaryData->count() }}</td></tr>
        <tr><td style="text-align:left">Total Gross Salary</td><td style="text-align:right">৳{{ number_format($totalGross, 0) }}</td></tr>
        <tr><td style="text-align:left">Total Net Payable</td><td style="text-align:right" class="text-primary">৳{{ number_format($totalNet, 0) }}</td></tr>
        <tr><td style="text-align:left">Total Bonus</td><td style="text-align:right" class="text-success">৳{{ number_format($totalBonus, 0) }}</td></tr>
        <tr><td style="text-align:left">Total Deductions</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalLate + $totalTax + $totalPf + $totalAbsent + $totalAdvance, 0) }}</td></tr>
        <tr><td style="text-align:left">Average Net Salary</td><td style="text-align:right">৳{{ number_format($salaryData->avg('net_payable'), 0) }}</td></tr>
    </tbody>
</table>

<table>
    <thead><tr><th colspan="2" style="text-align:left; background:#4f46e5; color:#fff;">DEDUCTION BREAKDOWN</th></tr></thead>
    <tbody>
        <tr><td style="text-align:left">Late Deduction</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalLate, 0) }}</td></tr>
        <tr><td style="text-align:left">Absent Deduction</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalAbsent, 0) }}</td></tr>
        <tr><td style="text-align:left">Tax Deduction</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalTax, 0) }}</td></tr>
        <tr><td style="text-align:left">PF Deduction</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalPf, 0) }}</td></tr>
        <tr><td style="text-align:left">Advance Deduction</td><td style="text-align:right" class="text-danger">৳{{ number_format($totalAdvance, 0) }}</td></tr>
    </tbody>
</table>

<table>
    <thead><tr><th colspan="2" style="text-align:left; background:#4f46e5; color:#fff;">ATTENDANCE SUMMARY</th></tr></thead>
    <tbody>
        <tr><td style="text-align:left">Total Working Days</td><td style="text-align:right">{{ $attendanceData->count() }}</td></tr>
        <tr><td style="text-align:left">Days Present</td><td style="text-align:right" class="text-success">{{ $presentCount }}</td></tr>
        <tr><td style="text-align:left">Days Absent</td><td style="text-align:right" class="text-danger">{{ $absentCount }}</td></tr>
        <tr><td style="text-align:left">Leave Days</td><td style="text-align:right">{{ $leaveCount }}</td></tr>
    </tbody>
</table>

<table>
    <thead><tr><th style="text-align:left; background:#4f46e5; color:#fff;">DEPARTMENT</th><th style="background:#4f46e5; color:#fff;">Employees</th><th style="background:#4f46e5; color:#fff;">Total Gross</th><th style="background:#4f46e5; color:#fff;">Total Net</th></tr></thead>
    <tbody>
        @foreach($deptSalarySummary as $d)
        <tr>
            <td style="text-align:left">{{ $d['dept_name'] }}</td>
            <td>{{ $d['count'] }}</td>
            <td>৳{{ number_format($d['total_gross'], 0) }}</td>
            <td class="text-primary">৳{{ number_format($d['total_net'], 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
