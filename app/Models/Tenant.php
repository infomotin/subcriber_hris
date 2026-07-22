<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'tenant_token',
        'subscription_plan_id',
        'status',
        'expires_at',
        'max_devices',
        'is_demo',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_demo' => 'boolean',
        'max_devices' => 'integer',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->tenant_token)) {
                $tenant->tenant_token = Str::random(16);
            }
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name) . '-' . Str::random(4);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function zktecoUsers(): HasMany
    {
        return $this->hasMany(ZktecoUser::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canAddDevice(): bool
    {
        return $this->devices()->count() < $this->max_devices;
    }
}
