<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class MovementMonthlyLimit extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id', 'movement_type_id', 'month', 'year', 'max_allowed'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'max_allowed' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class);
    }
}
