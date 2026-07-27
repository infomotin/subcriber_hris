<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class MovementType extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'duration_type', 'max_hours', 'requires_return', 'is_active'
    ];

    protected $casts = [
        'max_hours' => 'decimal:1',
        'requires_return' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function monthlyLimits(): HasMany
    {
        return $this->hasMany(MovementMonthlyLimit::class, 'movement_type_id');
    }

    public function passes(): HasMany
    {
        return $this->hasMany(MovementPass::class, 'movement_type_id');
    }
}
