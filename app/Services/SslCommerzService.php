<?php

namespace App\Services;

use App\Models\GatewayConfig;
use App\Models\PaymentLog;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Str;

class SslCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $isSandbox;

    public function __construct()
    {
        $gatewayConfig = GatewayConfig::find(1);

        $this->storeId = $gatewayConfig->sslcommerz_store_id
            ?? config('sslcommerz.apiCredentials.store_id', 'testbox');
        $this->storePassword = $gatewayConfig->sslcommerz_store_passwd
            ?? config('sslcommerz.apiCredentials.store_password', 'qwerty');
        $this->isSandbox = $gatewayConfig->sslcommerz_is_sandbox
            ?? config('sslcommerz.sandbox', true);
    }

    public function initiatePayment(Tenant $tenant, SubscriptionPlan $plan, string $billingCycle = 'monthly'): array
    {
        $tranId = 'TXN_' . strtoupper(Str::random(12));
        $amount = $billingCycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        $paymentLog = PaymentLog::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'tran_id' => $tranId,
            'amount' => $amount,
            'currency' => 'BDT',
            'billing_cycle' => $billingCycle,
            'status' => 'pending',
        ]);

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => $tranId,
            'success_url' => route('subscription.ssl.success'),
            'fail_url' => route('subscription.ssl.fail'),
            'cancel_url' => route('subscription.ssl.cancel'),
            'ipn_url' => route('subscription.ssl.ipn'),
            'cus_name' => $tenant->user->name ?? $tenant->name,
            'cus_email' => $tenant->user->email ?? 'subscriber@amds.test',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01700000000',
            'shipping_method' => 'NO',
            'product_name' => "Subscription Plan: {$plan->name}",
            'product_category' => 'Software',
            'product_profile' => 'non-physical-goods',
        ];

        return [
            'payment_log' => $paymentLog,
            'post_data' => $postData,
            'redirect_url' => route('subscription.ssl.mock_checkout', ['tran_id' => $tranId]),
        ];
    }

    public function validatePayment(string $tranId, array $data): bool
    {
        $paymentLog = PaymentLog::where('tran_id', $tranId)->first();

        if (! $paymentLog) {
            return false;
        }

        $paymentLog->update([
            'status' => 'success',
            'val_id' => $data['val_id'] ?? 'VAL_' . Str::random(8),
            'card_type' => $data['card_type'] ?? 'VISA/BKASH',
            'raw_response' => $data,
        ]);

        // Extend Tenant Subscription
        $tenant = $paymentLog->tenant;
        $plan = $paymentLog->plan;

        if ($tenant && $plan) {
            $months = $paymentLog->billing_cycle === 'yearly' ? 12 : 1;
            $newExpiration = ($tenant->expires_at && $tenant->expires_at->isFuture())
                ? $tenant->expires_at->addMonths($months)
                : now()->addMonths($months);

            $tenant->update([
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'expires_at' => $newExpiration,
                'max_devices' => $plan->max_devices,
            ]);
        }

        return true;
    }
}
