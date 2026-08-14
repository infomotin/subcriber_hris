<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class SalaryRelation extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'name',
        'basic_percent',
        'house_rent_percent',
        'medical_percent',
        'tada_percent',
        'is_active',
        'is_ot_payable',
        'is_late_deduction',
        'single_punch_full_day',
    ];

    protected $casts = [
        'basic_percent' => 'decimal:2',
        'house_rent_percent' => 'decimal:2',
        'medical_percent' => 'decimal:2',
        'tada_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'is_ot_payable' => 'boolean',
        'is_late_deduction' => 'boolean',
        'single_punch_full_day' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bonusConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BonusConfig::class, 'salary_role_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(SalaryRoleAssignment::class, 'salary_role_id');
    }

    public function assignedDepartments(): HasMany
    {
        return $this->assignments()->whereNotNull('department_id');
    }

    public function globalAssignments(): HasMany
    {
        return $this->assignments()->whereNull('department_id');
    }
}
