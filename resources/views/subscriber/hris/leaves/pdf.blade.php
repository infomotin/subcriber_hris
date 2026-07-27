@php
    $subscriberConfig = \App\Models\TenantConfig::getGroup('subscriber');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #555; }
        .title { text-align: center; font-size: 16px; font-weight: 700; margin: 15px 0 20px; text-decoration: underline; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.info td { padding: 6px 10px; vertical-align: top; border: 1px solid #ccc; }
        table.info td.label { width: 25%; font-weight: 700; background: #f5f5f5; color: #333; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .reason-box { border: 1px solid #ccc; padding: 12px; border-radius: 4px; margin-bottom: 20px; background: #fafafa; min-height: 60px; }
        .status-badge { display: inline-block; padding: 3px 14px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 15px; }
        .signature-area { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature-area .sig-block { width: 45%; text-align: center; }
        .signature-area .sig-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-size: 11px; }
        .ref { font-size: 10px; color: #777; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        @if(!empty($subscriberConfig['company_logo']))
            <img src="{{ Storage::disk('public')->url($subscriberConfig['company_logo']) }}" style="max-height:50px;margin-bottom:8px;">
        @endif
        <h1>{{ $subscriberConfig['report_header_text'] ?? $companyName }}</h1>
        <p>{{ $subscriberConfig['company_name'] ?? $companyName }} &middot; Human Resources Department</p>
        @if(!empty($subscriberConfig['short_description']))
            <p style="font-size:10px;color:#94a3b8;margin-top:3px;">{{ $subscriberConfig['short_description'] }}</p>
        @endif
    </div>

    <div class="title">LEAVE APPLICATION</div>

    <table class="info">
        <tr>
            <td class="label">Application Ref</td>
            <td>LA-{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="label">Date Applied</td>
            <td>{{ $leave->created_at->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Employee Name</td>
            <td>{{ $leave->employee?->user?->name ?? 'N/A' }}</td>
            <td class="label">Employee ID</td>
            <td>{{ $leave->employee?->employee_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Department</td>
            <td>{{ $leave->employee?->department?->name ?? 'N/A' }}</td>
            <td class="label">Designation</td>
            <td>{{ $leave->employee?->designation?->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Leave Details</div>
    <table class="info">
        <tr>
            <td class="label">Leave Type</td>
            <td>{{ $leave->leaveType?->name ?? 'N/A' }} ({{ $leave->leaveType?->code ?? '' }})</td>
            <td class="label">Status</td>
            <td>
                <span class="status-badge status-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">Start Date</td>
            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M, Y (l)') }}</td>
            <td class="label">End Date</td>
            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M, Y (l)') }}</td>
        </tr>
        <tr>
            <td class="label">Total Days</td>
            <td colspan="3">{{ $leave->total_days }} day{{ $leave->total_days > 1 ? 's' : '' }}</td>
        </tr>
    </table>

    <div class="section-title">Reason for Leave</div>
    <div class="reason-box">
        {{ $leave->reason }}
    </div>

    @if($leave->action_remarks)
    <div class="section-title">Remarks</div>
    <div class="reason-box">
        {{ $leave->action_remarks }}
    </div>
    @endif

    <div class="signature-area">
        <div class="sig-block">
            <div class="sig-line">Employee Signature</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Authorized By ({{ $leave->actionedBy?->name ?? 'Pending' }})</div>
        </div>
    </div>

    <div class="ref">
        {{ $subscriberConfig['report_footer_text'] ?? 'System-generated document.' }}
        <br>Generated on {{ now()->format('d M, Y h:i A') }} | {{ $subscriberConfig['company_name'] ?? $companyName }} HR System
        @if(!empty($subscriberConfig['report_footer_notes']))
            <br><em style="font-size:9px;color:#999;">{{ $subscriberConfig['report_footer_notes'] }}</em>
        @endif
    </div>

</body>
</html>
