<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPushLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'endpoint_url',
        'data_format',
        'records_count',
        'status_code',
        'response_body',
        'is_success',
    ];

    protected $casts = [
        'records_count' => 'integer',
        'status_code' => 'integer',
        'is_success' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
