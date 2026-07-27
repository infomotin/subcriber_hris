<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\MovementPass;
use App\Models\MovementType;
use App\Models\MovementMonthlyLimit;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MovementPassController extends Controller
{
    public function index()
    {
        $passes = MovementPass::with(['employee.user', 'movementType', 'actionedBy'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('subscriber.hris.movement-passes.index', compact('passes'));
    }

    public function apply()
    {
        $employees = EmployeeProfile::with(['user', 'department', 'images'])->get();
        $types = MovementType::where('is_active', true)->orderBy('name')->get();
        return view('subscriber.hris.movement-passes.apply', compact('employees', 'types'));
    }

    public function getEmployeeInfo(Request $request)
    {
        $emp = EmployeeProfile::with(['user', 'department', 'designation', 'images'])
            ->find($request->get('employee_profile_id'));
        if (!$emp) return response()->json(null);

        $photo = $emp->images()->where('type', 'profile_photo')->first();
        return response()->json([
            'id' => $emp->id,
            'employee_id' => $emp->employee_id,
            'name' => $emp->user?->name,
            'department' => $emp->department?->name,
            'designation' => $emp->designation?->name,
            'photo_url' => $photo ? asset('storage/' . $photo->file_path) : null,
        ]);
    }

    public function getMonthlyUsage(Request $request)
    {
        $empId = $request->get('employee_profile_id');
        if (!$empId) return response()->json([]);

        $month = (int) now()->month;
        $year = (int) now()->year;

        $types = MovementType::where('is_active', true)->orderBy('name')->get();
        $result = [];

        foreach ($types as $type) {
            $limit = MovementMonthlyLimit::where('movement_type_id', $type->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            $used = MovementPass::where('employee_profile_id', $empId)
                ->where('movement_type_id', $type->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->whereIn('status', ['approved', 'pending'])
                ->count();

            $maxAllowed = $limit?->max_allowed ?? 3;

            $result[] = [
                'id' => $type->id,
                'name' => $type->name,
                'code' => $type->code,
                'duration_type' => $type->duration_type,
                'max_hours' => $type->max_hours,
                'requires_return' => $type->requires_return,
                'max_allowed' => $maxAllowed,
                'used' => $used,
                'remaining' => max(0, $maxAllowed - $used),
            ];
        }

        return response()->json($result);
    }

    public function getPassHistory(Request $request)
    {
        $empId = $request->get('employee_profile_id');
        if (!$empId) return response()->json([]);

        $passes = MovementPass::with('movementType')
            ->where('employee_profile_id', $empId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($passes);
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'date' => 'required|date|after_or_equal:today',
            'out_time' => 'required',
            'return_time' => 'nullable',
            'reason' => 'nullable|string|max:500',
        ]);

        $type = MovementType::find($validated['movement_type_id']);
        $emp = EmployeeProfile::find($validated['employee_profile_id']);

        $month = (int) now()->month;
        $year = (int) now()->year;
        $limit = MovementMonthlyLimit::where('movement_type_id', $type->id)
            ->where('month', $month)->where('year', $year)->first();
        $maxAllowed = $limit?->max_allowed ?? 3;

        $used = MovementPass::where('employee_profile_id', $emp->id)
            ->where('movement_type_id', $type->id)
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->whereIn('status', ['approved', 'pending'])->count();

        if ($used >= $maxAllowed) {
            return redirect()->back()
                ->withErrors(['error' => "Monthly limit reached for {$type->name}. Used: {$used}/{$maxAllowed}."])
                ->withInput();
        }

        $duration = null;
        if ($type->requires_return && !empty($validated['return_time'])) {
            $out = \Carbon\Carbon::parse($validated['date'] . ' ' . $validated['out_time']);
            $ret = \Carbon\Carbon::parse($validated['date'] . ' ' . $validated['return_time']);
            $duration = round($out->diffInMinutes($ret) / 60, 1);
            if ($duration > $type->max_hours) {
                return redirect()->back()
                    ->withErrors(['error' => "Duration exceeds max allowed ({$type->max_hours} hours)."])
                    ->withInput();
            }
        }

        MovementPass::create([
            'tenant_id' => $tenant->id,
            'employee_profile_id' => $emp->id,
            'movement_type_id' => $type->id,
            'date' => $validated['date'],
            'out_time' => $validated['out_time'],
            'return_time' => $validated['return_time'] ?? null,
            'duration_hours' => $duration,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('subscriber.hris.movement-passes.index')
            ->with('success', 'Movement pass submitted for ' . ($emp->user->name ?? 'employee') . '.');
    }

    public function approve(MovementPass $pass)
    {
        if ($pass->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending passes can be approved.']);
        }

        $pass->update([
            'status' => 'approved',
            'actioned_by' => auth()->id(),
            'action_remarks' => request('action_remarks'),
        ]);

        return redirect()->route('subscriber.hris.movement-passes.index')
            ->with('success', 'Movement pass approved.');
    }

    public function reject(MovementPass $pass)
    {
        if ($pass->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending passes can be rejected.']);
        }

        $pass->update([
            'status' => 'rejected',
            'actioned_by' => auth()->id(),
            'action_remarks' => request('action_remarks', 'Application rejected.'),
        ]);

        return redirect()->route('subscriber.hris.movement-passes.index')
            ->with('success', 'Movement pass rejected.');
    }

    public function destroy(MovementPass $pass)
    {
        $pass->delete();
        return redirect()->route('subscriber.hris.movement-passes.index')
            ->with('success', 'Movement pass deleted.');
    }
}
