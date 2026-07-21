<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;

class SubscriptionCheckoutController extends Controller
{
    public function __construct(
        protected SslCommerzService $sslCommerzService
    ) {}

    public function plans()
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        return view('subscriber.plans', compact('plans', 'tenant'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $initResult = $this->sslCommerzService->initiatePayment($tenant, $plan, $request->billing_cycle);

        return redirect()->away($initResult['redirect_url']);
    }

    public function mockCheckout(Request $request)
    {
        $tranId = $request->query('tran_id');
        $paymentLog = PaymentLog::where('tran_id', $tranId)->firstOrFail();

        return view('subscriber.ssl_checkout', compact('paymentLog'));
    }

    public function success(Request $request)
    {
        $tranId = $request->input('tran_id') ?? $request->query('tran_id');
        $valId = $request->input('val_id') ?? 'VAL_' . uniqid();

        $this->sslCommerzService->validatePayment($tranId, [
            'val_id' => $valId,
            'card_type' => 'SSLCOMMERZ/BKASH',
        ]);

        return redirect()->route('subscriber.dashboard')
            ->with('success', 'Subscription payment successful! Your quota has been extended.');
    }

    public function fail(Request $request)
    {
        return redirect()->route('subscriber.plans')
            ->with('error', 'Payment transaction failed. Please try again.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('subscriber.plans')
            ->with('error', 'Payment transaction was canceled.');
    }
}
