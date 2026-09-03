<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $search = request('search');
        $query = Kpi::with('employee.user');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('goal_title', 'like', "%{$search}%")
                  ->orWhereHas('employee.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $kpis = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('subscriber.hris.kpis.index', compact('kpis'));
    }

    public function create()
    {
        $employees = EmployeeProfile::with('user')->get();
        return view('subscriber.hris.kpis.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'weightage' => 'required|integer|min:1|max:100',
            'status' => 'required|string',
            'score_rating' => 'nullable|integer|min:1|max:10'
        ]);

        Kpi::create([
            'tenant_id' => $tenant->id,
            'employee_profile_id' => $validated['employee_profile_id'],
            'goal_title' => $validated['goal_title'],
            'description' => $validated['description'],
            'target_date' => $validated['target_date'],
            'weightage' => $validated['weightage'],
            'status' => $validated['status'],
            'score_rating' => $validated['score_rating']
        ]);

        return redirect()->route('subscriber.hris.kpis.index')->with('success', 'KPI goal added successfully.');
    }

    public function edit(Kpi $kpi)
    {
        $employees = EmployeeProfile::with('user')->get();
        return view('subscriber.hris.kpis.edit', compact('kpi', 'employees'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'weightage' => 'required|integer|min:1|max:100',
            'status' => 'required|string',
            'score_rating' => 'nullable|integer|min:1|max:10'
        ]);

        $kpi->update($validated);

        return redirect()->route('subscriber.hris.kpis.index')->with('success', 'KPI goal updated successfully.');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return redirect()->route('subscriber.hris.kpis.index')->with('success', 'KPI goal deleted successfully.');
    }
}
