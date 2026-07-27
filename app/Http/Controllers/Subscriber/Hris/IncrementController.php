<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Increment;
use App\Models\IncrementRule;
use App\Models\EmployeeProfile;
use App\Models\SalaryStructure;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncrementController extends Controller
{
    public function index(Request $request)
    {
        $query = Increment::with(['employee.user', 'employee.department', 'employee.designation', 'rule']);

        if ($search = $request->get('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        if ($type = $request->get('type')) {
            $query->where('increment_type', $type);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $increments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $types = Increment::TYPES;

        return view('subscriber.hris.increments.index', compact('increments', 'types'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('title')->get();
        $rules = IncrementRule::where('is_active', true)->orderBy('name')->get();
        $types = Increment::TYPES;

        $employees = EmployeeProfile::with(['user', 'department', 'designation', 'salaryStructure'])
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->user?->name ?? 'N/A',
                'emp_id' => $e->employee_id,
                'department' => $e->department?->name ?? 'N/A',
                'designation' => $e->designation?->title ?? 'N/A',
                'joining_date' => $e->joining_date,
                'status' => $e->status,
                'basic' => $e->salaryStructure?->basic_salary ?? 0,
                'gross' => $e->salaryStructure
                    ? ($e->salaryStructure->basic_salary + $e->salaryStructure->house_rent + $e->salaryStructure->medical_allowance + $e->salaryStructure->conveyance_allowance + ($e->salaryStructure->other_allowances ?? 0))
                    : 0,
            ]);

        return view('subscriber.hris.increments.create', compact('departments', 'designations', 'rules', 'types', 'employees'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'increment_type' => 'required|in:annual,special,manual,bulk',
            'increment_rule_id' => 'nullable|exists:increment_rules,id',
            'based_on' => 'required|in:basic,gross',
            'increment_percentage' => 'nullable|numeric|min:0|max:100',
            'increment_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',

            'employee_profile_id' => 'required_if:increment_type,manual|exists:employee_profiles,id',
            'department_id' => 'required_if:increment_type,bulk|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
        ]);

        DB::beginTransaction();
        try {
            $rule = $validated['increment_rule_id'] ? IncrementRule::find($validated['increment_rule_id']) : null;
            $basedOn = $validated['based_on'] ?? $rule?->increment_based_on ?? 'basic';
            $pct = $validated['increment_percentage'] ?? 0;
            $employees = [];

            if ($validated['increment_type'] === 'manual') {
                $emp = EmployeeProfile::with('salaryStructure')->findOrFail($validated['employee_profile_id']);
                $employees[] = $this->buildIncrement($emp, $validated['increment_type'], $basedOn, $pct, $validated['increment_amount'] ?? 0, $rule?->id, $validated['notes'] ?? null, $tenant->id);
            } elseif ($validated['increment_type'] === 'bulk') {
                $empQuery = EmployeeProfile::with('salaryStructure')
                    ->where('department_id', $validated['department_id']);
                if (!empty($validated['designation_id'])) {
                    $empQuery->where('designation_id', $validated['designation_id']);
                }
                $emps = $empQuery->get();
                foreach ($emps as $emp) {
                    $employees[] = $this->buildIncrement($emp, 'bulk', $basedOn, $pct, 0, $rule?->id, $validated['notes'] ?? null, $tenant->id);
                }
            } else {
                $emp = EmployeeProfile::with('salaryStructure')->findOrFail($validated['employee_profile_id']);
                if ($validated['increment_type'] === 'annual') {
                    $lastIncrement = Increment::where('employee_profile_id', $emp->id)
                        ->where('status', 'enforced')
                        ->latest('enforced_at')
                        ->first();
                    if ($lastIncrement && $lastIncrement->enforced_at->addYear()->isFuture()) {
                        return redirect()->back()->withErrors(['error' => 'Last increment was less than 1 year ago. Annual increment not eligible yet.'])->withInput();
                    }
                }
                $employees[] = $this->buildIncrement($emp, $validated['increment_type'], $basedOn, $pct, $validated['increment_amount'] ?? 0, $rule?->id, $validated['notes'] ?? null, $tenant->id);
            }

            DB::commit();

            $count = count($employees);
            $msg = $count === 1
                ? 'Increment created for ' . ($employees[0]->employee?->user?->name ?? 'employee') . '.'
                : "{$count} increments created successfully.";

            return redirect()->route('subscriber.hris.increments.enforce')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create increment: ' . $e->getMessage()])->withInput();
        }
    }

    private function buildIncrement($emp, $type, $basedOn, $pct, $fixedAmount, $ruleId, $notes, $tenantId): Increment
    {
        $salary = $emp->salaryStructure;
        $oldBasic = $salary?->basic_salary ?? 0;
        $oldGross = $salary ? ($salary->basic_salary + $salary->house_rent + $salary->medical_allowance + $salary->conveyance_allowance + ($salary->other_allowances ?? 0)) : 0;

        if ($fixedAmount > 0) {
            $incAmount = $fixedAmount;
            $incPct = $oldBasic > 0 ? round(($fixedAmount / $oldBasic) * 100, 2) : 0;
        } else {
            $base = $basedOn === 'gross' ? $oldGross : $oldBasic;
            $incAmount = round($base * ($pct / 100), 2);
            $incPct = $pct;
        }

        $newBasic = $oldBasic + ($basedOn === 'basic' ? $incAmount : 0);
        $newGross = $oldGross + $incAmount;

        $ref = 'INC-' . strtoupper(substr(md5(uniqid()), 0, 8));

        return Increment::create([
            'tenant_id' => $tenantId,
            'employee_profile_id' => $emp->id,
            'increment_rule_id' => $ruleId,
            'increment_type' => $type,
            'old_basic' => $oldBasic,
            'old_gross' => $oldGross,
            'new_basic' => $newBasic,
            'new_gross' => $newGross,
            'increment_amount' => $incAmount,
            'increment_percentage' => $incPct,
            'based_on' => $basedOn,
            'status' => 'pending',
            'reference_number' => $ref,
            'notes' => $notes,
        ]);
    }

    public function enforce()
    {
        $increments = Increment::with(['employee.user', 'employee.department', 'employee.designation', 'employee.salaryStructure', 'rule'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('subscriber.hris.increments.enforce', compact('increments'));
    }

    public function doEnforce(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:increments,id',
        ]);

        $enforced = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($validated['ids'] as $id) {
                $increment = Increment::with('employee.salaryStructure')->find($id);
                if (!$increment || $increment->status !== 'pending') {
                    $errors[] = "Increment #{$id} is not in pending status.";
                    continue;
                }

                $salary = $increment->employee->salaryStructure;
                if ($salary) {
                    $basicDiff = $increment->new_basic - $increment->old_basic;
                    $grossDiff = $increment->new_gross - $increment->old_gross;

                    $salary->update([
                        'basic_salary' => $increment->new_basic,
                        'house_rent' => $salary->house_rent + round($grossDiff * 0.25, 2),
                        'medical_allowance' => $salary->medical_allowance + round($grossDiff * 0.10, 2),
                        'conveyance_allowance' => $salary->conveyance_allowance + round($grossDiff * 0.15, 2),
                    ]);
                }

                $increment->update([
                    'status' => 'enforced',
                    'enforced_at' => now(),
                    'enforced_by' => auth()->user()?->name ?? 'System',
                ]);

                $enforced++;
            }

            DB::commit();

            $msg = "{$enforced} increment(s) enforced successfully.";
            if (!empty($errors)) {
                $msg .= ' Errors: ' . implode(', ', $errors);
            }

            return redirect()->route('subscriber.hris.increments.enforce')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to enforce: ' . $e->getMessage()]);
        }
    }

    public function letter(Increment $increment)
    {
        $increment->load(['employee.user', 'employee.department', 'employee.designation', 'rule']);
        return view('subscriber.hris.increments.letter', compact('increment'));
    }

    public function getEmployee(Request $request)
    {
        $search = trim($request->get('q'));
        if (empty($search)) {
            return response()->json([], 200);
        }

        $employees = EmployeeProfile::with(['user', 'department', 'designation', 'salaryStructure'])
            ->where('employee_id', 'like', "%{$search}%")
            ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'employee_id' => $e->employee_id,
                'name' => $e->user?->name ?? 'N/A',
                'department' => $e->department?->name ?? 'N/A',
                'designation' => $e->designation?->title ?? 'N/A',
                'basic' => $e->salaryStructure?->basic_salary ?? 0,
                'gross' => $e->salaryStructure
                    ? ($e->salaryStructure->basic_salary + $e->salaryStructure->house_rent + $e->salaryStructure->medical_allowance + $e->salaryStructure->conveyance_allowance + ($e->salaryStructure->other_allowances ?? 0))
                    : 0,
            ]);

        return response()->json($employees);
    }

    public function checkEligibility(Request $request)
    {
        $empId = $request->get('employee_profile_id');
        $lastIncrement = Increment::where('employee_profile_id', $empId)
            ->where('status', 'enforced')
            ->latest('enforced_at')
            ->first();

        if (!$lastIncrement) {
            return response()->json(['eligible' => true, 'message' => 'No previous increment found. Eligible for annual increment.']);
        }

        $oneYearAfter = $lastIncrement->enforced_at->addYear();
        if ($oneYearAfter->isPast()) {
            return response()->json(['eligible' => true, 'message' => 'Last increment was on ' . $lastIncrement->enforced_at->format('d M Y') . '. Eligible for annual increment.']);
        }

        $daysLeft = now()->diffInDays($oneYearAfter, false);
        return response()->json(['eligible' => false, 'message' => 'Not eligible. Last increment was ' . $lastIncrement->enforced_at->format('d M Y') . '. Wait ' . ceil(abs($daysLeft)) . ' more days.']);
    }
}
