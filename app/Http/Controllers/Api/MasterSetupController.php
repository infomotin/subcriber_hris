<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterSetupController extends Controller
{
    protected array $allowedTypes = [
        'departments', 'designations', 'shifts', 'genders',
        'divisions', 'districts', 'thanas', 'institutions',
        'education-boards', 'bill-types', 'bill-purposes',
        'advance-types', 'advance-sources', 'movement-types',
        'leave-types', 'leave-reasons', 'work-shifts',
    ];

    protected array $modelMap = [
        'departments' => \App\Models\Department::class,
        'designations' => \App\Models\Designation::class,
        'shifts' => \App\Models\WorkShift::class,
        'genders' => \App\Models\Gender::class,
        'divisions' => \App\Models\Division::class,
        'districts' => \App\Models\District::class,
        'thanas' => \App\Models\Thana::class,
        'institutions' => \App\Models\Institution::class,
        'education-boards' => \App\Models\EducationBoard::class,
        'bill-types' => \App\Models\BillType::class,
        'bill-purposes' => \App\Models\BillPurpose::class,
        'advance-types' => \App\Models\AdvanceType::class,
        'advance-sources' => \App\Models\AdvanceSource::class,
        'movement-types' => \App\Models\MovementType::class,
        'leave-types' => \App\Models\LeaveType::class,
        'leave-reasons' => \App\Models\LeaveReason::class,
        'work-shifts' => \App\Models\WorkShift::class,
    ];

    public function index(Request $request): JsonResponse
    {
        $data = [];
        foreach ($this->allowedTypes as $type) {
            $model = $this->modelMap[$type];
            $query = $model::query();

            if (in_array(\App\Traits\Multitenantable::class, class_uses($model))) {
                $query->where('tenant_id', $request->user()->tenant_id);
            }

            $data[$type] = $query->orderBy('id')->get();
        }
        return response()->json($data);
    }

    public function store(Request $request, $type): JsonResponse
    {
        if (!isset($this->modelMap[$type])) {
            return response()->json(['message' => "Invalid type: {$type}"], 422);
        }

        $model = $this->modelMap[$type];
        $fillable = (new $model)->getFillable();
        $rules = [];
        foreach ($fillable as $field) {
            if ($field === 'tenant_id') continue;
            $rules[$field] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        if (in_array(\App\Traits\Multitenantable::class, class_uses($model))) {
            $validated['tenant_id'] = $request->user()->tenant_id;
        }

        $record = $model::create($validated);
        return response()->json(['message' => "{$type} created", 'record' => $record], 201);
    }

    public function destroy(Request $request, $type, $id): JsonResponse
    {
        if (!isset($this->modelMap[$type])) {
            return response()->json(['message' => "Invalid type: {$type}"], 422);
        }

        $model = $this->modelMap[$type];
        $query = $model::where('id', $id);

        if (in_array(\App\Traits\Multitenantable::class, class_uses($model))) {
            $query->where('tenant_id', $request->user()->tenant_id);
        }

        $record = $query->first();
        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $record->delete();
        return response()->json(['message' => "{$type} deleted"]);
    }
}
