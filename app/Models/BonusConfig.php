<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class BonusConfig extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'salary_role_id',
        'calculation_type',
        'calculation_value',
    ];

    public function salaryRole(): BelongsTo
    {
        return $this->belongsTo(SalaryRelation::class, 'salary_role_id');
    }

    public function slabs(): HasMany
    {
        return $this->hasMany(BonusEligibilitySlab::class, 'bonus_config_id')->orderBy('min_months');
    }

    public function calculateBonusAmount($basicSalary, $grossSalary): float
    {
        return match ($this->calculation_type) {
            'basic_half' => round($basicSalary * 0.5, 2),
            'gross_1_5x' => round($grossSalary * 1.5, 2),
            'basic_percent' => round($basicSalary * ($this->calculation_value / 100), 2),
            'gross_percent' => round($grossSalary * ($this->calculation_value / 100), 2),
            'fixed_amount' => round($this->calculation_value, 2),
            default => 0,
        };
    }

    public function getEligibilityPercent(int $tenureMonths): float
    {
        $slab = $this->slabs()
            ->where('min_months', '<=', $tenureMonths)
            ->where(function ($q) use ($tenureMonths) {
                $q->where('max_months', '>=', $tenureMonths)
                  ->orWhereNull('max_months');
            })
            ->first();

        return $slab ? (float) $slab->percent_of_bonus : 0;
    }
}
