<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeePromotion extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'old_department_id',
        'new_department_id',
        'old_designation_id',
        'new_designation_id',
        'promotion_type',
        'notes',
        'effective_date',
        'status',
        'reference_number',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    const TYPES = [
        'merit' => 'Merit Based',
        'seniority' => 'Seniority Based',
        'departmental' => 'Departmental Transfer',
        'positional' => 'Positional Promotion',
        'special' => 'Special Achievement',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function oldDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'old_department_id');
    }

    public function newDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }

    public function oldDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'old_designation_id');
    }

    public function newDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'new_designation_id');
    }
}
