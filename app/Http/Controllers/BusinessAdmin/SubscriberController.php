<?php

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\PaymentConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['user', 'plan'])
            ->withCount('devices', 'employees');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        }

        $subscribers = $query->orderBy('id', 'desc')->paginate(15);
        $plans = SubscriptionPlan::all();

        return view('business_admin.subscribers.index', compact('subscribers', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::find($request->subscription_plan_id);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole('Subscriber');

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'max_devices' => $plan->max_devices,
            'max_employees' => $plan->max_employees,
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);

        $user->update(['tenant_id' => $tenant->id]);

        return redirect()->route('admin.business.subscribers.index')
            ->with('success', 'Subscriber account created successfully.');
    }

    public function resetPassword(Request $request, Tenant $tenant)
    {
        $request->validate(['password' => 'required|string|min:6']);

        if ($tenant->user) {
            $tenant->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', "Password reset for subscriber {$tenant->name}");
    }

    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        return back()->with('success', "Subscriber status set to {$newStatus}");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'expires_at' => 'nullable|date|after_or_equal:today',
            'max_devices' => 'nullable|integer|min:1',
            'max_employees' => 'nullable|integer|min:1',
        ]);

        $plan = SubscriptionPlan::find($validated['subscription_plan_id']);

        $updateData = [
            'subscription_plan_id' => $plan->id,
            'max_devices' => $validated['max_devices'] ?? $plan->max_devices,
            'max_employees' => $validated['max_employees'] ?? $plan->max_employees,
            'expires_at' => $validated['expires_at'] ?: $tenant->expires_at,
        ];

        $tenant->update($updateData);

        return redirect()->route('admin.business.subscribers.index')
            ->with('success', "Subscriber '{$tenant->name}' updated successfully.");
    }

    public function recordPayment(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'send_email' => 'nullable|in:0,1',
        ]);

        $payment = PaymentLog::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $tenant->subscription_plan_id,
            'tran_id' => 'MANUAL-' . strtoupper(uniqid()),
            'amount' => $validated['amount'],
            'currency' => 'BDT',
            'status' => 'success',
            'billing_cycle' => 'manual',
            'raw_response' => [
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? '',
                'recorded_by' => auth()->user()->name ?? 'Admin',
            ],
        ]);

        // Extend subscription by 1 month from now or current expiry (whichever is later)
        $baseDate = $tenant->expires_at && $tenant->expires_at->isFuture() ? $tenant->expires_at : now();
        $tenant->update([
            'expires_at' => $baseDate->addMonth(),
            'status' => 'active',
        ]);

        // Send email notification if requested
        $emailSent = false;
        if ($request->boolean('send_email') && $tenant->user && $tenant->user->email) {
            try {
                $tenant->user->notify(new PaymentConfirmation(
                    $tenant,
                    $validated['amount'],
                    $tenant->plan?->name ?? 'Current',
                    $validated['payment_method']
                ));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Payment email failed: ' . $e->getMessage());
            }
        }

        $msg = 'Manual payment of ' . number_format($validated['amount'], 0) . ' BDT recorded for ' . $tenant->name . '.';
        if ($emailSent) {
            $msg .= ' Email notification sent.';
        }

        return back()->with('success', $msg);
    }

    public function sendEmail(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message_body' => 'required|string|max:5000',
        ]);

        if (!$tenant->user || !$tenant->user->email) {
            return back()->with('error', 'No email address found for this subscriber.');
        }

        try {
            Mail::html(nl2br(e($validated['message_body'])), function ($msg) use ($tenant, $validated) {
                $msg->to($tenant->user->email, $tenant->name)
                    ->subject($validated['subject'])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return back()->with('success', 'Email sent to ' . $tenant->name . ' successfully.');
        } catch (\Throwable $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendBulkEmail(Request $request)
    {
        $validated = $request->validate([
            'tenant_ids' => 'required|string',
            'subject' => 'required|string|max:255',
            'message_body' => 'required|string|max:5000',
        ]);

        $ids = explode(',', $validated['tenant_ids']);
        $tenants = Tenant::whereIn('id', $ids)->with('user')->get();

        $sent = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            if (!$tenant->user || !$tenant->user->email) {
                $failed++;
                continue;
            }

            try {
                Mail::html(nl2br(e($validated['message_body'])), function ($msg) use ($tenant, $validated) {
                    $msg->to($tenant->user->email, $tenant->name)
                        ->subject($validated['subject'])
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Bulk email failed for ' . $tenant->name . ': ' . $e->getMessage());
                $failed++;
            }
        }

        return back()->with('success', "Bulk email sent to {$sent} subscriber(s)." . ($failed > 0 ? " {$failed} failed." : ''));
    }

    public function paymentHistory(Tenant $tenant)
    {
        $payments = PaymentLog::where('tenant_id', $tenant->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'tenant' => $tenant->name,
            'payments' => $payments->map(fn ($p) => [
                'id' => $p->id,
                'tran_id' => $p->tran_id,
                'amount' => number_format($p->amount, 0),
                'currency' => $p->currency,
                'status' => $p->status,
                'plan' => $p->plan?->name ?? 'N/A',
                'method' => $p->raw_response['payment_method'] ?? 'SSLCommerz',
                'date' => $p->created_at->format('M d, Y h:i A'),
            ]),
        ]);
    }
}
