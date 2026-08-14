<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment {{ ucfirst($status) }} | ZKTeco ADMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background: #f0f2f5; font-family: 'Inter', sans-serif; display: flex; align-items: center; min-height: 100vh; }
        .result-card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
        .result-header { padding: 2rem; text-align: center; }
        .result-body { padding: 1.5rem 2rem 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <div class="result-header {{ $status === 'success' ? 'bg-success' : ($status === 'fail' ? 'bg-danger' : 'bg-warning') }}">
                @if($status === 'success')
                    <i class="bx bx-check-circle" style="font-size: 3.5rem; color: #fff;"></i>
                    <h3 class="text-white fw-bold mt-2 mb-0">Payment Successful</h3>
                @elseif($status === 'fail')
                    <i class="bx bx-x-circle" style="font-size: 3.5rem; color: #fff;"></i>
                    <h3 class="text-white fw-bold mt-2 mb-0">Payment Failed</h3>
                @else
                    <i class="bx bx-x-circle" style="font-size: 3.5rem; color: #fff;"></i>
                    <h3 class="text-white fw-bold mt-2 mb-0">Payment Cancelled</h3>
                @endif
            </div>
            <div class="result-body text-center">
                <p class="font-size-14 text-slate-600 mb-3">{{ $message }}</p>

                @if($paymentLog)
                    <div class="bg-light p-3 rounded-3 mb-4 text-start font-size-13">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Transaction ID:</span>
                            <code>{{ $paymentLog->tran_id }}</code>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Plan:</span>
                            <strong>{{ $paymentLog->plan->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Amount:</span>
                            <strong class="text-success">{{ number_format($paymentLog->amount, 2) }} BDT</strong>
                        </div>
                    </div>
                @endif

                <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg">
                    <i class="bx bx-log-in me-1"></i> Login to Subscriber Dashboard
                </a>
                @if($status === 'success')
                    <p class="font-size-12 text-muted mt-2 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        After login, your subscription plan and device quota will be updated automatically.
                    </p>
                @else
                    <a href="{{ route('subscriber.plans') }}" class="btn btn-link mt-2">Back to Plans</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>