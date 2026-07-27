<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\MovementType;
use App\Models\MovementMonthlyLimit;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MovementTypeController extends Controller
{
    public function index()
    {
        $types = MovementType::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.movement-types.index', compact('types'));
    }

    public function create()
    {
        return view('subscriber.hris.movement-types.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20',
            'duration_type' => 'required|in:short_leave,day_out',
            'max_hours' => 'required|numeric|min:0.5|max:24',
            'requires_return' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['requires_return'] = $request->boolean('requires_return');
        $validated['is_active'] = $request->boolean('is_active', true);

        MovementType::create($validated);

        return redirect()->route('subscriber.hris.movement-types.index')
            ->with('success', 'Movement type created successfully.');
    }

    public function edit(MovementType $movementType)
    {
        return view('subscriber.hris.movement-types.edit', ['type' => $movementType]);
    }

    public function update(Request $request, MovementType $movementType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20',
            'duration_type' => 'required|in:short_leave,day_out',
            'max_hours' => 'required|numeric|min:0.5|max:24',
            'requires_return' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['requires_return'] = $request->boolean('requires_return');
        $validated['is_active'] = $request->boolean('is_active', true);

        $movementType->update($validated);

        return redirect()->route('subscriber.hris.movement-types.index')
            ->with('success', 'Movement type updated successfully.');
    }

    public function destroy(MovementType $movementType)
    {
        $movementType->delete();
        return redirect()->route('subscriber.hris.movement-types.index')
            ->with('success', 'Movement type deleted.');
    }

    public function limits()
    {
        $types = MovementType::where('is_active', true)->orderBy('name')->get();
        $month = (int) now()->month;
        $year = (int) now()->year;

        $limits = MovementMonthlyLimit::with('movementType')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('movement_type_id');

        return view('subscriber.hris.movement-types.limits', compact('types', 'limits', 'month', 'year'));
    }

    public function storeLimit(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'movement_type_id' => 'required|exists:movement_types,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2024',
            'max_allowed' => 'required|integer|min:1|max:100',
        ]);

        $validated['tenant_id'] = $tenant->id;

        MovementMonthlyLimit::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'movement_type_id' => $validated['movement_type_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            ['max_allowed' => $validated['max_allowed']]
        );

        return redirect()->route('subscriber.hris.movement-types.limits')
            ->with('success', 'Monthly limit updated.');
    }

    public function destroyLimit(MovementMonthlyLimit $limit)
    {
        $limit->delete();
        return redirect()->route('subscriber.hris.movement-types.limits')
            ->with('success', 'Monthly limit removed.');
    }
}
