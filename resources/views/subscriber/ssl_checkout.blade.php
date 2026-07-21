<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SSLCommerz Payment Gateway Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body { background: #eef2f5; font-family: 'Inter', sans-serif; }
        .checkout-box { max-width: 500px; margin: 4rem auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .ssl-header { background: #007bff; color: #fff; padding: 1.5rem; text-align: center; }
    </style>
</head>
<body>

<div class="checkout-box">
    <div class="ssl-header">
        <h4 class="fw-bold mb-1"><i class="bx bx-lock-alt me-1"></i> SSLCommerz Payment Gateway</h4>
        <small class="opacity-75">Secure Merchant Payment Processing</small>
    </div>
    <div class="p-4">
        <div class="bg-light p-3 rounded mb-3">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Transaction ID:</span>
                <code>{{ $paymentLog->tran_id }}</code>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subscriber:</span>
                <strong>{{ $paymentLog->tenant->name ?? 'Subscriber' }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Selected Plan:</span>
                <span class="badge bg-primary">{{ $paymentLog->plan->name ?? 'Plan' }}</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                <span class="fw-bold">Total Amount:</span>
                <h4 class="fw-bold text-success mb-0">{{ number_format($paymentLog->amount, 2) }} BDT</h4>
            </div>
        </div>

        <form action="{{ route('subscription.ssl.success') }}" method="POST">
            @csrf
            <input type="hidden" name="tran_id" value="{{ $paymentLog->tran_id }}">
            <input type="hidden" name="val_id" value="VAL_{{ Str::random(10) }}">
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg"><i class="bx bx-check-circle me-1"></i> Confirm & Pay via bKash / Card</button>
                <a href="{{ route('subscription.ssl.cancel') }}" class="btn btn-outline-danger">Cancel Payment</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
