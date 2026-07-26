<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\EmployeePromotion;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $query = EmployeePromotion::with(['employee.user', 'oldDepartment', 'newDepartment', 'oldDesignation', 'newDesignation']);

        if ($search = $request->get('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $types = EmployeePromotion::TYPES;

        return view('subscriber.hris.promotions.index', compact('promotions', 'types'));
    }

    public function create()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('title')->get();
        $types = EmployeePromotion::TYPES;

        return view('subscriber.hris.promotions.create', compact('departments', 'designations', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'new_department_id' => 'required|exists:departments,id',
            'new_designation_id' => 'required|exists:designations,id',
            'promotion_type' => 'required|string|in:' . implode(',', array_keys(EmployeePromotion::TYPES)),
            'notes' => 'nullable|string|max:1000',
            'effective_date' => 'required|date',
        ]);

        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $employee = EmployeeProfile::with(['department', 'designation'])->findOrFail($validated['employee_profile_id']);

        $promotion = DB::transaction(function () use ($validated, $employee, $tenant) {
            $promotion = EmployeePromotion::create([
                'tenant_id' => $tenant->id,
                'employee_profile_id' => $employee->id,
                'old_department_id' => $employee->department_id,
                'new_department_id' => $validated['new_department_id'],
                'old_designation_id' => $employee->designation_id,
                'new_designation_id' => $validated['new_designation_id'],
                'promotion_type' => $validated['promotion_type'],
                'notes' => $validated['notes'] ?? null,
                'effective_date' => $validated['effective_date'],
                'status' => 'active',
                'reference_number' => 'PRO-' . strtoupper($tenant->code ?? 'XX') . '-' . date('Ymd') . '-' . str_pad(EmployeePromotion::max('id') + 1, 4, '0', STR_PAD_LEFT),
            ]);

            $employee->update([
                'department_id' => $validated['new_department_id'],
                'designation_id' => $validated['new_designation_id'],
            ]);

            return $promotion;
        });

        return redirect()->route('subscriber.hris.promotions.show', $promotion->id)
            ->with('success', 'Promotion recorded successfully. Promotional letter generated below.');
    }

    public function show(EmployeePromotion $promotion)
    {
        $promotion->load(['employee.user', 'oldDepartment', 'newDepartment', 'oldDesignation', 'newDesignation']);
        $types = EmployeePromotion::TYPES;

        return view('subscriber.hris.promotions.letter', compact('promotion', 'types'));
    }

    public function getEmployee(Request $request)
    {
        $employee = EmployeeProfile::with(['department', 'designation', 'user'])
            ->where('employee_id', $request->get('employee_id'))
            ->first();

        if (!$employee) {
            return response()->json(['found' => false, 'message' => 'Employee not found']);
        }

        return response()->json([
            'found' => true,
            'id' => $employee->id,
            'employee_id' => $employee->employee_id,
            'name' => $employee->user?->name ?? 'N/A',
            'department_id' => $employee->department_id,
            'department' => $employee->department?->name ?? 'N/A',
            'designation_id' => $employee->designation_id,
            'designation' => $employee->designation?->title ?? 'N/A',
        ]);
    }
}
