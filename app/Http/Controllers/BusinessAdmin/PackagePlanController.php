<?php

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PackagePlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('tenants')->get();
        return view('business_admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_devices' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.business.plans.index')
            ->with('success', 'Subscription package plan created successfully.');
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_devices' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.business.plans.index')
            ->with('success', 'Package plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->tenants()->count() > 0) {
            return redirect()->route('admin.business.plans.index')
                ->with('error', 'Cannot delete plan: ' . $plan->tenants()->count() . ' subscriber(s) are assigned to this plan.');
        }

        $plan->delete();

        return redirect()->route('admin.business.plans.index')
            ->with('success', 'Package plan deleted successfully.');
    }
}
