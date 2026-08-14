@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>PUNCH DATA REPORT</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total Punches: {{ $punchData->count() }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">Date</th>
            <th style="text-align:left">Time</th>
            <th style="text-align:left">Employee</th>
            <th style="text-align:left">PIN</th>
            <th>Status</th><th>Verify</th><th>Source</th>
        </tr>
    </thead>
    <tbody>
        @foreach($punchData as $p)
        <tr>
            <td style="text-align:left">{{ \Carbon\Carbon::parse($p->punch_date_time)->format('d M Y') }}</td>
            <td style="text-align:left">{{ \Carbon\Carbon::parse($p->punch_date_time)->format('h:i A') }}</td>
            <td style="text-align:left">{{ $p->emp_name }}</td>
            <td style="text-align:left">{{ $p->employee_id }}</td>
            <td>{{ $p->status == 0 ? 'Check In' : 'Check Out' }}</td>
            <td>{{ $p->verify_type }}</td>
            <td>{{ $p->source ?? 'ADMS' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
