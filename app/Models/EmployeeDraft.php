<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDraft extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'form_token',
        'step',
        'step_data',
    ];

    protected $casts = [
        'step_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
