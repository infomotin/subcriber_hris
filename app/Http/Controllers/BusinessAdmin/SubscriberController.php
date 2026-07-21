<?php

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['user', 'plan']);

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
}
