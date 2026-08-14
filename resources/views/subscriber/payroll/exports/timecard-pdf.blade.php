@extends('subscriber.payroll.exports.layout-pdf')
@section('content')
<div class="header">
    <h1>TIME CARD</h1>
    <p>{{ $monthLabel }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total Records: {{ $attendanceData->count() }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="text-align:left">Date</th>
            <th style="text-align:left">Employee</th>
            <th style="text-align:left">Dept</th>
            <th style="text-align:left">Shift</th>
            <th>In</th><th>Out</th><th>Hours</th><th>Late</th><th>Early</th><th>OT</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendanceData as $a)
        <tr>
            <td style="text-align:left">{{ \Carbon\Carbon::parse($a->date)->format('d M, D') }}</td>
            <td style="text-align:left">{{ $a->emp_code }} - {{ $a->emp_name }}</td>
            <td style="text-align:left">{{ $a->dept_name }}</td>
            <td style="text-align:left">{{ $a->shift_name }}</td>
            <td>{{ $a->in_time ? \Carbon\Carbon::parse($a->in_time)->format('h:i A') : '—' }}</td>
            <td>{{ $a->out_time ? \Carbon\Carbon::parse($a->out_time)->format('h:i A') : '—' }}</td>
            <td>{{ number_format($a->total_hours, 2) }}h</td>
            <td class="{{ $a->late_minutes > 0 ? 'text-danger' : '' }}">{{ $a->late_minutes }}m</td>
            <td>{{ $a->early_minutes }}m</td>
            <td class="{{ $a->overtime_minutes > 0 ? 'text-success' : '' }}">{{ $a->overtime_minutes }}m</td>
            <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">This is a computer-generated report. | {{ config('app.name', 'AMDS') }}</div>
@endsection
