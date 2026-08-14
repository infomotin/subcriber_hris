<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }} - {{ $monthLabel ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 10px; color: #1e293b; padding: 20px; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #4f46e5; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #f1f5f9; color: #475569; font-size: 8px; text-transform: uppercase; padding: 5px 4px; text-align: right; border: 1px solid #e2e8f0; font-weight: 700; }
        th:first-child, th:nth-child(2), th:nth-child(3) { text-align: left; }
        td { padding: 4px; text-align: right; border: 1px solid #e2e8f0; font-size: 9px; }
        td:first-child, td:nth-child(2), td:nth-child(3) { text-align: left; }
        tr:nth-child(even) { background: #f8fafc; }
        .subtotal { background: #eef2ff; font-weight: 700; }
        .subtotal td { border-top: 2px solid #4f46e5; font-weight: 700; }
        .grand-total { background: #4f46e5; color: #fff; font-weight: 700; }
        .grand-total td { border: 1px solid #4f46e5; font-weight: 700; padding: 6px 4px; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #4f46e5; font-weight: 700; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 8px; font-weight: 600; }
        .badge-generated { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #e5e7eb; color: #374151; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .dept-header { background: #4f46e5; color: #fff; padding: 6px 12px; font-weight: 700; font-size: 11px; margin: 15px 0 0 0; border-radius: 4px 4px 0 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        @media print { body { padding: 10px; } @page { margin: 0.5cm; } }
    </style>
</head>
<body>
