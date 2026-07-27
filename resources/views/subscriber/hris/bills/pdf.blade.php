@php
    $subscriberConfig = \App\Models\TenantConfig::getGroup('subscriber');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill Invoice #{{ $bill->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #5f5af6; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { font-size: 22px; color: #5f5af6; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #64748b; }
        .bill-no { text-align: right; margin-bottom: 15px; }
        .bill-no strong { font-size: 14px; color: #5f5af6; }
        .info-grid { display: flex; gap: 30px; margin-bottom: 25px; }
        .info-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; }
        .info-box h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #5f5af6; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .info-box p { font-size: 11px; margin-bottom: 4px; }
        .info-box .label { color: #94a3b8; }
        .info-box .value { font-weight: 600; }
        .bill-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .bill-table th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        .bill-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .bill-table .amount-cell { text-align: right; font-weight: 700; font-size: 13px; }
        .total-row td { border-top: 3px solid #5f5af6; font-weight: 700; font-size: 14px; padding-top: 15px; }
        .signature-section { display: flex; gap: 40px; margin-top: 50px; page-break-inside: avoid; }
        .sig-box { flex: 1; text-align: center; }
        .sig-line { border-top: 1px solid #1e293b; margin-top: 60px; padding-top: 8px; font-size: 10px; color: #64748b; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
        .modification-box { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 10px 15px; margin-bottom: 20px; }
        .modification-box h4 { font-size: 11px; color: #92400e; margin-bottom: 5px; }
        .modification-box p { font-size: 10px; color: #78350f; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($subscriberConfig['company_logo']))
            <img src="{{ Storage::disk('public')->url($subscriberConfig['company_logo']) }}" style="max-height:50px;margin-bottom:8px;">
        @endif
        <h1>{{ $subscriberConfig['report_header_text'] ?? 'BILL / INVOICE' }}</h1>
        <p>{{ $subscriberConfig['company_name'] ?? $bill->employee?->tenant?->name ?? 'Organization' }}</p>
        @if(!empty($subscriberConfig['short_description']))
            <p style="font-size:10px;color:#94a3b8;margin-top:3px;">{{ $subscriberConfig['short_description'] }}</p>
        @endif
    </div>

    <div class="bill-no">
        <strong>Bill #{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</strong><br>
        <span style="font-size:10px;color:#64748b;">Date: {{ $bill->created_at->format('d M, Y') }}</span>
    </div>

    @if($bill->approved_amount && $bill->approved_amount != $bill->amount)
    <div class="modification-box">
        <h4>Amount Modified</h4>
        <p>Original: {{ number_format($bill->amount, 2) }} BDT &rarr; Approved: {{ number_format($bill->approved_amount, 2) }} BDT</p>
        @if($bill->modifications->count())
            <p>Reason: {{ $bill->modifications->last()->reason }}</p>
        @endif
    </div>
    @endif

    <div class="info-grid">
        <div class="info-box">
            <h3>Employee Details</h3>
            <p><span class="label">Name:</span> <span class="value">{{ $bill->employee?->user?->name ?? 'N/A' }}</span></p>
            <p><span class="label">ID:</span> <span class="value">{{ $bill->employee?->employee_id ?? '--' }}</span></p>
            <p><span class="label">Department:</span> <span class="value">{{ $bill->employee?->department?->name ?? '--' }}</span></p>
            <p><span class="label">Designation:</span> <span class="value">{{ $bill->employee?->designation?->title ?? '--' }}</span></p>
        </div>
        <div class="info-box">
            <h3>Bill Details</h3>
            <p><span class="label">Type:</span> <span class="value">{{ $bill->billType?->name ?? '--' }}</span></p>
            <p><span class="label">Purpose:</span> <span class="value">{{ $bill->billPurpose?->name ?? '--' }}</span></p>
            <p><span class="label">Bill No:</span> <span class="value">{{ $bill->bill_no ?? '--' }}</span></p>
            <p><span class="label">Submitted:</span> <span class="value">{{ $bill->created_at->format('d M, Y h:i A') }}</span></p>
        </div>
    </div>

    <table class="bill-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:25%">Bill Type</th>
                <th style="width:25%">Purpose</th>
                <th style="width:25%">Description</th>
                <th style="width:20%" class="amount-cell">Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $bill->billType?->name ?? '--' }}</td>
                <td>{{ $bill->billPurpose?->name ?? '--' }}</td>
                <td>{{ $bill->description ?? '-' }}</td>
                <td class="amount-cell">{{ number_format($bill->approved_amount ?? $bill->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">TOTAL APPROVED AMOUNT:</td>
                <td class="amount-cell" style="color:#5f5af6;">{{ number_format($bill->approved_amount ?? $bill->amount, 2) }} BDT</td>
            </tr>
        </tbody>
    </table>

    @if($bill->status === 'approved' || $bill->status === 'modified')
    <div class="info-box" style="margin-bottom:25px;">
        <h3>Approval Information</h3>
        <p><span class="label">Approved By:</span> <span class="value">{{ $bill->actionedBy?->name ?? 'System' }}</span></p>
        <p><span class="label">Status:</span> <span class="value" style="color:{{ $bill->status === 'approved' ? '#16a34a' : '#0891b2' }}; text-transform:uppercase;">{{ $bill->status }}</span></p>
        @if($bill->action_remarks)
            <p><span class="label">Remarks:</span> <span class="value">{{ $bill->action_remarks }}</span></p>
        @endif
        <p><span class="label">Date:</span> <span class="value">{{ $bill->updated_at->format('d M, Y h:i A') }}</span></p>
    </div>
    @endif

    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-line">
                Applicant's Signature<br>
                <strong>{{ $bill->employee?->user?->name ?? '' }}</strong>
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line">
                Approved By<br>
                <strong>{{ $bill->actionedBy?->name ?? '________________' }}</strong>
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line">
                Authorized Signature<br>
                <strong>________________</strong>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>{{ $subscriberConfig['report_footer_text'] ?? 'This is a system-generated bill invoice.' }}</p>
        <p style="font-size:8px;margin-top:3px;color:#94a3b8;">Generated on {{ now()->format('d M, Y h:i A') }}</p>
        @if(!empty($subscriberConfig['report_footer_notes']))
            <p style="font-size:8px;margin-top:5px;color:#94a3b8;font-style:italic;">{{ $subscriberConfig['report_footer_notes'] }}</p>
        @endif
    </div>
</body>
</html>
