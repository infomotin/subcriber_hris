<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $shifts = WorkShift::orderBy('name', 'asc')->paginate(15);
        return view('subscriber.hris.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('subscriber.hris.shifts.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_buffer_time' => 'nullable|date_format:H:i'
        ]);

        WorkShift::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'late_buffer_time' => $validated['late_buffer_time']
        ]);

        return redirect()->route('subscriber.hris.shifts.index')->with('success', 'Work Shift created successfully.');
    }

    public function edit(WorkShift $shift)
    {
        return view('subscriber.hris.shifts.edit', compact('shift'));
    }

    public function update(Request $request, WorkShift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_buffer_time' => 'nullable'
        ]);

        $shift->update($validated);

        return redirect()->route('subscriber.hris.shifts.index')->with('success', 'Work Shift updated successfully.');
    }

    public function destroy(WorkShift $shift)
    {
        $shift->delete();
        return redirect()->route('subscriber.hris.shifts.index')->with('success', 'Work Shift deleted successfully.');
    }
}
