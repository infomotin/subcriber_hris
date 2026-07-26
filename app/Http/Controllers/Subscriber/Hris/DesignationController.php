<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $designations = Designation::orderBy('title', 'asc')->paginate(15);
        return view('subscriber.hris.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('subscriber.hris.designations.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50'
        ]);

        Designation::create([
            'tenant_id' => $tenant->id,
            'title' => $validated['title'],
            'grade' => $validated['grade']
        ]);

        return redirect()->route('subscriber.hris.designations.index')->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        return view('subscriber.hris.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50'
        ]);

        $designation->update($validated);

        return redirect()->route('subscriber.hris.designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('subscriber.hris.designations.index')->with('success', 'Designation deleted successfully.');
    }
}
