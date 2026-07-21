<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantWebhookSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'endpoint_url',
        'push_schedule',
        'scheduled_time',
        'data_format',
        'date_format',
        'custom_mapping',
        'auth_type',
        'auth_header_name',
        'auth_token',
        'auth_username',
        'auth_password',
        'is_enabled',
        'last_pushed_at',
        'last_status_code',
        'last_response_body',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'last_pushed_at' => 'datetime',
        'last_status_code' => 'integer',
        'custom_mapping' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
