<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class Increment extends Model
{
    use Multitenantable;

    protected $table = 'increments';

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'increment_rule_id',
        'increment_type',
        'old_basic',
        'old_gross',
        'new_basic',
        'new_gross',
        'increment_amount',
        'increment_percentage',
        'based_on',
        'status',
        'enforced_at',
        'enforced_by',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'enforced_at' => 'datetime',
    ];

    const TYPES = [
        'annual' => 'Annual Increment',
        'special' => 'Special Increment',
        'manual' => 'Manual Increment',
        'bulk' => 'Bulk Increment',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(IncrementRule::class, 'increment_rule_id');
    }
}
