@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>DEPARTMENT SALARY REPORT</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">Department</th>
            <th>Employees</th><th>Total Gross</th><th>Total Bonus</th><th>Total Deduction</th><th>Total Net</th><th>Avg Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($deptSalarySummary as $d)
        <tr>
            <td style="text-align:left">{{ $d['dept_name'] }}</td>
            <td>{{ $d['count'] }}</td>
            <td>৳{{ number_format($d['total_gross'], 0) }}</td>
            <td class="text-success">৳{{ number_format($d['total_bonus'], 0) }}</td>
            <td class="text-danger">৳{{ number_format($d['total_deduction'], 0) }}</td>
            <td class="text-primary">৳{{ number_format($d['total_net'], 0) }}</td>
            <td>৳{{ number_format($d['avg_net'], 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tr class="grand-total">
        <td style="text-align:left">TOTAL</td>
        <td>{{ $deptSalarySummary->sum('count') }}</td>
        <td>৳{{ number_format($deptSalarySummary->sum('total_gross'), 0) }}</td>
        <td>৳{{ number_format($deptSalarySummary->sum('total_bonus'), 0) }}</td>
        <td>৳{{ number_format($deptSalarySummary->sum('total_deduction'), 0) }}</td>
        <td>৳{{ number_format($deptSalarySummary->sum('total_net'), 0) }}</td>
        <td>৳{{ number_format($salaryData->avg('net_payable'), 0) }}</td>
    </tr>
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
