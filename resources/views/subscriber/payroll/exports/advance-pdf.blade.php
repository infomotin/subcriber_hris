@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>ADVANCE REPORT</h1>
    <p>{{ $monthLabel ?? '' }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total: {{ $advanceData->count() }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">Employee</th>
            <th style="text-align:left">Department</th>
            <th style="text-align:left">Type</th>
            <th>Amount</th><th>Approved</th><th>Installments</th><th>Monthly Ded</th><th>Status</th><th style="text-align:left">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($advanceData as $a)
        <tr>
            <td style="text-align:left">{{ $a->emp_code }} - {{ $a->emp_name }}</td>
            <td style="text-align:left">{{ $a->dept_name }}</td>
            <td style="text-align:left">{{ $a->advance_type_name ?? 'N/A' }}</td>
            <td>৳{{ number_format($a->amount, 0) }}</td>
            <td>৳{{ number_format($a->approved_amount, 0) }}</td>
            <td>{{ $a->installments }}</td>
            <td>৳{{ number_format($a->monthly_deduction, 0) }}</td>
            <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
            <td style="text-align:left">{{ \Carbon\Carbon::parse($a->created_at)->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    @if($advanceData->count())
    <tr class="grand-total">
        <td colspan="3" style="text-align:left">TOTAL</td>
        <td>৳{{ number_format($advanceData->sum('amount'), 0) }}</td>
        <td>৳{{ number_format($advanceData->sum('approved_amount'), 0) }}</td>
        <td></td>
        <td>৳{{ number_format($advanceData->sum('monthly_deduction'), 0) }}</td>
        <td></td>
        <td></td>
    </tr>
    @endif
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
