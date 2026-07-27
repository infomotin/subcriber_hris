<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\BillType;
use App\Models\Tenant;
use Illuminate\Http\Request;

class BillTypeController extends Controller
{
    public function index()
    {
        $types = BillType::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.bill-types.index', compact('types'));
    }

    public function create()
    {
        return view('subscriber.hris.bill-types.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        BillType::create($validated);

        return redirect()->route('subscriber.hris.bill-types.index')
            ->with('success', 'Bill type created successfully.');
    }

    public function edit(BillType $billType)
    {
        return view('subscriber.hris.bill-types.edit', ['type' => $billType]);
    }

    public function update(Request $request, BillType $billType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $billType->update($validated);

        return redirect()->route('subscriber.hris.bill-types.index')
            ->with('success', 'Bill type updated successfully.');
    }

    public function destroy(BillType $billType)
    {
        $billType->delete();
        return redirect()->route('subscriber.hris.bill-types.index')
            ->with('success', 'Bill type deleted.');
    }
}
