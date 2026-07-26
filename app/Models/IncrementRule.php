<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class IncrementRule extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'name',
        'joining_date_from',
        'joining_date_to',
        'increment_based_on',
        'year_start_date',
        'special_max_percentage',
        'is_active',
    ];

    protected $casts = [
        'joining_date_from' => 'date',
        'joining_date_to' => 'date',
        'year_start_date' => 'date',
        'is_active' => 'boolean',
    ];
}
