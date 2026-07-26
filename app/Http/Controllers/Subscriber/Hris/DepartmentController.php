<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $departments = Department::with('parent')->orderBy('name', 'asc')->paginate(15);
        return view('subscriber.hris.departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name', 'asc')->get();
        return view('subscriber.hris.departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:departments,id'
        ]);

        Department::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'code' => $validated['code'],
            'parent_id' => $validated['parent_id'],
        ]);

        return redirect()->route('subscriber.hris.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $departments = Department::where('id', '!=', $department->id)->orderBy('name', 'asc')->get();
        return view('subscriber.hris.departments.edit', compact('department', 'departments'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:departments,id'
        ]);

        $department->update($validated);

        return redirect()->route('subscriber.hris.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('subscriber.hris.departments.index')->with('success', 'Department deleted successfully.');
    }
}
