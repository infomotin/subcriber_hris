@extends('layouts.subscriber')

@section('title', 'Promotional Letter - ' . ($promotion->employee?->user?->name ?? ''))

@section('content')
<style>
    .letter-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
    }
    .letter-header {
        border-bottom: 3px double #5f5af6;
        padding-bottom: 1.5rem;
    }
    .letter-title {
        font-family: 'Georgia', serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        letter-spacing: 0.02em;
    }
    .letter-subtitle {
        text-align: center;
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
    }
    .letter-ref {
        font-size: 0.75rem;
        color: #94a3b8;
        text-align: right;
    }
    .letter-body {
        font-size: 0.92rem;
        line-height: 1.7;
        color: #1e293b;
    }
    .letter-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.25rem 0;
    }
    .letter-table th, .letter-table td {
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        font-size: 0.88rem;
    }
    .letter-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #475569;
        text-align: left;
        width: 35%;
    }
    .letter-table td {
        color: #0f172a;
        font-weight: 500;
    }
    .letter-stamp {
        width: 120px;
        height: 120px;
        border: 3px solid #5f5af6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5f5af6;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.3;
        transform: rotate(-15deg);
        margin-left: auto;
    }
    .print-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }
    @media print {
        .print-btn, .page-title-box, .no-print { display: none !important; }
        .letter-container { box-shadow: none; border: none; }
        body { background: #fff !important; }
    }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-file text-primary me-1.5 align-middle font-size-26"></i>Promotional Letter
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscriber.hris.promotions.index') }}" class="btn btn-outline-secondary rounded-pill px-4 no-print" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 no-print" style="height: 40px; font-size: 0.85rem;">
            <i class="bx bx-printer me-1"></i> Print / PDF
        </button>
    </div>
</div>

<div class="letter-container p-5">
    <div class="letter-header d-flex justify-content-between align-items-start">
        <div>
            <h2 class="letter-title">Promotional Letter</h2>
            <p class="letter-subtitle">Official Confirmation of Position Advancement</p>
        </div>
        <div class="letter-ref">
            Ref: <strong>{{ $promotion->reference_number }}</strong><br>
            Date: <strong>{{ $promotion->effective_date?->format('d F Y') }}</strong>
        </div>
    </div>

    <div class="letter-body mt-4">
        <p><strong>To,</strong></p>
        <p style="font-size: 1.05rem; font-weight: 600; color: #0f172a;">{{ $promotion->employee?->user?->name ?? 'N/A' }}</p>
        <p class="text-muted" style="margin-top: -0.5rem;">Employee ID: {{ $promotion->employee?->employee_id }}</p>

        <p>Dear <strong>{{ explode(' ', $promotion->employee?->user?->name ?? 'Employee')[0] }}</strong>,</p>

        <p>We are pleased to inform you that based on your performance, dedication, and contributions to the organization, you have been promoted. Your new role and responsibilities will be effective from <strong>{{ $promotion->effective_date?->format('d F Y') }}</strong>.</p>

        <p>The details of your promotion are as follows:</p>

        <table class="letter-table">
            <tr>
                <th>Previous Department</th>
                <td>{{ $promotion->oldDepartment?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>New Department</th>
                <td><strong class="text-success">{{ $promotion->newDepartment?->name ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <th>Previous Designation</th>
                <td>{{ $promotion->oldDesignation?->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>New Designation</th>
                <td><strong class="text-primary">{{ $promotion->newDesignation?->title ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <th>Promotion Type</th>
                <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11">{{ $types[$promotion->promotion_type] ?? $promotion->promotion_type }}</span></td>
            </tr>
            <tr>
                <th>Effective Date</th>
                <td>{{ $promotion->effective_date?->format('d F Y') }}</td>
            </tr>
            @if($promotion->notes)
            <tr>
                <th>Remarks</th>
                <td>{{ $promotion->notes }}</td>
            </tr>
            @endif
        </table>

        <p>We extend our heartfelt congratulations and look forward to your continued success in your new capacity. We are confident that you will excel in your new responsibilities and contribute significantly to our shared goals.</p>

        <div class="mt-5 pt-3" style="border-top: 1px solid #e2e8f0;">
            <div class="row">
                <div class="col-6">
                    <p style="margin-top: 2.5rem;">
                        <span class="text-muted font-size-12">Authorized Signature</span><br>
                        <span style="border-top: 1px solid #0f172a; display: inline-block; padding-top: 4px; min-width: 180px; font-weight: 600;">HR Manager</span>
                    </p>
                </div>
                <div class="col-6 text-end">
                    <div class="letter-stamp">
                        <span>Approved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
