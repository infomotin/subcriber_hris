<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\AdvanceType;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AdvanceTypeController extends Controller
{
    public function index()
    {
        $types = AdvanceType::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.advance-types.index', compact('types'));
    }

    public function create()
    {
        return view('subscriber.hris.advance-types.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'payment_mode' => 'required|in:one_time,monthly_installment',
            'is_active' => 'boolean',
        ]);
        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        AdvanceType::create($validated);
        return redirect()->route('subscriber.hris.advance-types.index')->with('success', 'Advance type created.');
    }

    public function edit(AdvanceType $advanceType)
    {
        return view('subscriber.hris.advance-types.edit', ['type' => $advanceType]);
    }

    public function update(Request $request, AdvanceType $advanceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'payment_mode' => 'required|in:one_time,monthly_installment',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $advanceType->update($validated);
        return redirect()->route('subscriber.hris.advance-types.index')->with('success', 'Advance type updated.');
    }

    public function destroy(AdvanceType $advanceType)
    {
        $advanceType->delete();
        return redirect()->route('subscriber.hris.advance-types.index')->with('success', 'Advance type deleted.');
    }
}
