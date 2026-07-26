<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class LeaveType extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'days_per_year',
        'accrual_enabled'
    ];

    protected $casts = [
        'accrual_enabled' => 'boolean'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
