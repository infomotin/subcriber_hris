<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class SalaryRoleAssignment extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'salary_role_id',
        'department_id',
        'applicable_month',
    ];

    public function salaryRole(): BelongsTo
    {
        return $this->belongsTo(SalaryRelation::class, 'salary_role_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
