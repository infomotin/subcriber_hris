<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class WorkShift extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'name',
        'start_time',
        'end_time',
        'late_buffer_time'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
