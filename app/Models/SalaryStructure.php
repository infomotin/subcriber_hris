<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class SalaryStructure extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'conveyance_allowance',
        'other_allowances',
        'provident_fund_deduction',
        'tax_deduction'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'provident_fund_deduction' => 'decimal:2',
        'tax_deduction' => 'decimal:2'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
