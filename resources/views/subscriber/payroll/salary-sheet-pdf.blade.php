<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Sheet - {{ date('F Y', strtotime($month . '-01')) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 10px; color: #1e293b; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #4f46e5; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #6b7280; }
        .dept-header { background: #4f46e5; color: #fff; padding: 6px 12px; font-weight: 700; font-size: 11px; margin: 15px 0 0 0; border-radius: 4px 4px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #f1f5f9; color: #475569; font-size: 8px; text-transform: uppercase; padding: 5px 4px; text-align: right; border: 1px solid #e2e8f0; font-weight: 700; }
        th:nth-child(1), th:nth-child(2), th:nth-child(3), th:nth-child(4), th:nth-child(5) { text-align: left; }
        td { padding: 4px; text-align: right; border: 1px solid #e2e8f0; font-size: 9px; }
        td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5) { text-align: left; }
        tr:nth-child(even) { background: #f8fafc; }
        .subtotal { background: #eef2ff; font-weight: 700; }
        .subtotal td { border-top: 2px solid #4f46e5; font-weight: 700; }
        .grand-total { background: #4f46e5; color: #fff; font-weight: 700; }
        .grand-total td { border: 1px solid #4f46e5; font-weight: 700; padding: 6px 4px; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #4f46e5; font-weight: 700; }
        .status { padding: 2px 6px; border-radius: 10px; font-size: 8px; font-weight: 600; }
        .status-generated { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        @media print { body { padding: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SALARY SHEET</h1>
        <p>{{ date('F Y', strtotime($month . '-01')) }} | Generated: {{ now()->format('d M Y, h:i A') }} | Total Employees: {{ $salaryData->count() }}</p>
    </div>

    @foreach($deptGroups as $deptName => $items)
        <div class="dept-header">{{ $deptName ?: 'N/A' }} ({{ $items->count() }} employees)</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left">#</th>
                    <th style="text-align:left">ID</th>
                    <th style="text-align:left">Name</th>
                    <th style="text-align:left">Designation</th>
                    <th style="text-align:left">Join Date</th>
                    <th>Basic</th>
                    <th>House</th>
                    <th>Medical</th>
                    <th>Conv.</th>
                    <th>Other</th>
                    <th>Gross</th>
                    <th>P</th>
                    <th>A</th>
                    <th>L</th>
                    <th>Late</th>
                    <th>Late Ded</th>
                    <th>Abs Ded</th>
                    <th>Tax</th>
                    <th>PF</th>
                    <th>Bonus</th>
                    <th>OT</th>
                    <th>Adv</th>
                    <th>Net</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $s)
                <tr>
                    <td style="text-align:left">{{ $loop->iteration }}</td>
                    <td style="text-align:left">{{ $s->emp_code }}</td>
                    <td style="text-align:left">{{ $s->emp_name }}</td>
                    <td style="text-align:left">{{ $s->designation_name }}</td>
                    <td style="text-align:left">{{ $s->joining_date ? date('d M Y', strtotime($s->joining_date)) : '—' }}</td>
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
                    <td><span class="status status-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td colspan="5" style="text-align:left">Subtotal — {{ $deptName ?: 'N/A' }}</td>
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
            <td colspan="5" style="text-align:left">GRAND TOTAL ({{ $salaryData->count() }} employees)</td>
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

    <div class="footer">
        <p>This is a computer-generated salary sheet. | {{ config('app.name', 'AMDS') }} | Page 1</p>
    </div>
</body>
</html>
