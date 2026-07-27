<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\BillPurpose;
use App\Models\Tenant;
use Illuminate\Http\Request;

class BillPurposeController extends Controller
{
    public function index()
    {
        $purposes = BillPurpose::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.bill-purposes.index', compact('purposes'));
    }

    public function create()
    {
        return view('subscriber.hris.bill-purposes.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        BillPurpose::create($validated);

        return redirect()->route('subscriber.hris.bill-purposes.index')
            ->with('success', 'Bill purpose created successfully.');
    }

    public function edit(BillPurpose $billPurpose)
    {
        return view('subscriber.hris.bill-purposes.edit', ['purpose' => $billPurpose]);
    }

    public function update(Request $request, BillPurpose $billPurpose)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $billPurpose->update($validated);

        return redirect()->route('subscriber.hris.bill-purposes.index')
            ->with('success', 'Bill purpose updated successfully.');
    }

    public function destroy(BillPurpose $billPurpose)
    {
        $billPurpose->delete();
        return redirect()->route('subscriber.hris.bill-purposes.index')
            ->with('success', 'Bill purpose deleted.');
    }
}
