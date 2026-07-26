<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'is_active'
    ];

    protected $casts = [
        'basic_percent' => 'decimal:2',
        'house_rent_percent' => 'decimal:2',
        'medical_percent' => 'decimal:2',
        'tada_percent' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
