@extends('layouts.subscriber')

@section('title', 'Increment Letter')

@section('content')
<style>
    .letter-container { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .letter-header { border-bottom: 3px double #5f5af6; padding-bottom: 1.5rem; }
    .letter-title { font-family: 'Georgia', serif; font-size: 1.4rem; font-weight: 700; color: #0f172a; text-align: center; letter-spacing: 0.02em; }
    .letter-subtitle { text-align: center; font-size: 0.8rem; color: #64748b; }
    .letter-table { width: 100%; border-collapse: collapse; margin: 1.25rem 0; }
    .letter-table th, .letter-table td { padding: 10px 14px; border: 1px solid #e2e8f0; font-size: 0.88rem; }
    .letter-table th { background: #f8fafc; font-weight: 600; color: #475569; text-align: left; width: 35%; }
    .letter-table td { color: #0f172a; font-weight: 500; }
    .letter-table td.old-val { color: #dc2626; }
    .letter-table td.new-val { color: #059669; font-weight: 700; }
    .letter-stamp { width: 120px; height: 120px; border: 3px solid #5f5af6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #5f5af6; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.3; transform: rotate(-15deg); margin-left: auto; }
    @media print { .no-print, .page-title-box { display: none !important; } .letter-container { box-shadow: none; border: none; } body { background: #fff !important; } }
</style>

<div class="page-title-box d-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Operations</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-file text-primary me-1.5 align-middle font-size-26"></i>Increment Letter
        </h4>
    </div>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 no-print" style="height: 40px; font-size: 0.85rem;">
        <i class="bx bx-printer me-1"></i> Print / PDF
    </button>
</div>

<div class="letter-container p-5">
    <div class="letter-header d-flex justify-content-between align-items-start">
        <div>
            <h2 class="letter-title">Salary Increment Letter</h2>
            <p class="letter-subtitle">Official Notification of Salary Adjustment</p>
        </div>
        <div class="text-end font-size-12 text-muted">
            Ref: <strong>{{ $increment->reference_number }}</strong><br>
            Date: <strong>{{ $increment->enforced_at?->format('d F Y') }}</strong>
        </div>
    </div>

    <div class="mt-4">
        <p><strong>To,</strong></p>
        <p style="font-size: 1.05rem; font-weight: 600;">{{ $increment->employee?->user?->name ?? 'N/A' }}</p>
        <p class="text-muted" style="margin-top: -0.5rem;">Employee ID: {{ $increment->employee?->employee_id }} | {{ $increment->employee?->department?->name ?? '' }} / {{ $increment->employee?->designation?->title ?? '' }}</p>

        <p>Dear <strong>{{ explode(' ', $increment->employee?->user?->name ?? 'Employee')[0] }}</strong>,</p>

        <p>We are pleased to inform you that your salary has been revised effective from <strong>{{ $increment->enforced_at?->format('d F Y') }}</strong>. This increment is based on your performance and contributions to the organization.</p>

        <p>The details of your salary revision are as follows:</p>

        <table class="letter-table">
            <tr>
                <th>Increment Type</th>
                <td><span class="badge bg-soft-primary text-primary px-3 py-1.5 font-size-11 text-uppercase">{{ \App\Models\Increment::TYPES[$increment->increment_type] ?? $increment->increment_type }}</span></td>
            </tr>
            <tr>
                <th>Previous Basic Salary</th>
                <td class="old-val">{{ number_format($increment->old_basic, 2) }} BDT</td>
            </tr>
            <tr>
                <th>New Basic Salary</th>
                <td class="new-val">{{ number_format($increment->new_basic, 2) }} BDT</td>
            </tr>
            <tr>
                <th>Previous Gross Salary</th>
                <td class="old-val">{{ number_format($increment->old_gross, 2) }} BDT</td>
            </tr>
            <tr>
                <th>New Gross Salary</th>
                <td class="new-val">{{ number_format($increment->new_gross, 2) }} BDT</td>
            </tr>
            <tr>
                <th>Increment Amount</th>
                <td class="new-val">{{ number_format($increment->increment_amount, 2) }} BDT</td>
            </tr>
            <tr>
                <th>Increment Percentage</th>
                <td>{{ $increment->increment_percentage }}%</td>
            </tr>
            @if($increment->notes)
            <tr>
                <th>Remarks</th>
                <td>{{ $increment->notes }}</td>
            </tr>
            @endif
        </table>

        <p>We appreciate your dedication and look forward to your continued contributions to the organization's success.</p>

        <div class="mt-5 pt-3" style="border-top: 1px solid #e2e8f0;">
            <div class="row">
                <div class="col-6">
                    <p style="margin-top: 2.5rem;">
                        <span class="text-muted font-size-12">Authorized Signature</span><br>
                        <span style="border-top: 1px solid #0f172a; display: inline-block; padding-top: 4px; min-width: 180px; font-weight: 600;">HR Manager</span>
                    </p>
                </div>
                <div class="col-6 text-end">
                    <div class="letter-stamp"><span>Enforced</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
