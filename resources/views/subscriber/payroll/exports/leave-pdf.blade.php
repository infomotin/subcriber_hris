@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>LEAVE REPORT</h1>
    <p>{{ $monthLabel ?? '' }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total Applications: {{ $leaveData->count() }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">Employee</th>
            <th style="text-align:left">Department</th>
            <th style="text-align:left">Leave Type</th>
            <th style="text-align:left">From</th>
            <th style="text-align:left">To</th>
            <th>Days</th>
            <th style="text-align:left">Reason</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($leaveData as $l)
        <tr>
            <td style="text-align:left">{{ $l->emp_code }} - {{ $l->emp_name }}</td>
            <td style="text-align:left">{{ $l->dept_name }}</td>
            <td style="text-align:left">{{ $l->leave_type_name ?? 'N/A' }}</td>
            <td style="text-align:left">{{ $l->start_date }}</td>
            <td style="text-align:left">{{ $l->end_date }}</td>
            <td>{{ $l->total_days }}</td>
            <td style="text-align:left">{{ $l->reason }}</td>
            <td><span class="badge badge-{{ $l->status }}">{{ ucfirst($l->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
