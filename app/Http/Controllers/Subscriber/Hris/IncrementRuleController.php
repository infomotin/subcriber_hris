<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\IncrementRule;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IncrementRuleController extends Controller
{
    public function index()
    {
        $search = request('search');
        $query = IncrementRule::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        $rules = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('subscriber.hris.increment-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('subscriber.hris.increment-rules.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'joining_date_from' => 'nullable|date',
            'joining_date_to' => 'nullable|date|after_or_equal:joining_date_from',
            'increment_based_on' => 'required|in:basic,gross',
            'year_start_date' => 'nullable|date',
            'special_max_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        IncrementRule::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('subscriber.hris.increment-rules.index')
            ->with('success', 'Increment rule created successfully.');
    }

    public function edit(IncrementRule $incrementRule)
    {
        return view('subscriber.hris.increment-rules.edit', compact('incrementRule'));
    }

    public function update(Request $request, IncrementRule $incrementRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'joining_date_from' => 'nullable|date',
            'joining_date_to' => 'nullable|date|after_or_equal:joining_date_from',
            'increment_based_on' => 'required|in:basic,gross',
            'year_start_date' => 'nullable|date',
            'special_max_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $incrementRule->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('subscriber.hris.increment-rules.index')
            ->with('success', 'Increment rule updated successfully.');
    }

    public function destroy(IncrementRule $incrementRule)
    {
        $incrementRule->delete();
        return redirect()->route('subscriber.hris.increment-rules.index')
            ->with('success', 'Increment rule deleted.');
    }
}
