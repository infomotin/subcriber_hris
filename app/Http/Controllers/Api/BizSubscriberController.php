<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BizSubscriberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tenant::with(['user', 'plan']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->search}%"));
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tenants = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 15);

        $tenants->getCollection()->transform(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'slug' => $t->slug,
            'owner' => $t->user?->name ?? 'N/A',
            'email' => $t->user?->email ?? 'N/A',
            'plan' => $t->plan?->name ?? 'N/A',
            'status' => $t->status,
            'devices' => $t->devices()->count(),
            'employees' => $t->employees()->count(),
            'expires_at' => $t->expires_at?->format('Y-m-d'),
            'is_demo' => $t->is_demo,
            'created_at' => $t->created_at->format('Y-m-d'),
        ]);

        return response()->json($tenants);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

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

        return response()->json(['message' => 'Subscriber created', 'tenant' => $tenant], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $tenant = Tenant::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,suspended,expired',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'max_devices' => 'nullable|integer|min:1',
            'max_employees' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        $tenant->update(array_merge($validated, [
            'max_devices' => $validated['max_devices'] ?? $plan->max_devices,
            'max_employees' => $validated['max_employees'] ?? $plan->max_employees,
        ]));

        return response()->json(['message' => 'Subscriber updated', 'tenant' => $tenant]);
    }

    public function toggle($id): JsonResponse
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => $tenant->status === 'active' ? 'suspended' : 'active']);
        return response()->json(['message' => "Tenant {$tenant->status}", 'tenant' => $tenant]);
    }

    public function payments(Request $request, $id): JsonResponse
    {
        $tenant = Tenant::findOrFail($id);
        $payments = $tenant->paymentLogs()->orderBy('id', 'desc')->paginate($request->per_page ?? 15);
        return response()->json($payments);
    }
}
