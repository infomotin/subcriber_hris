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
        'max_employees',
        'is_demo',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_demo' => 'boolean',
        'max_devices' => 'integer',
        'max_employees' => 'integer',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->tenant_token)) {
                $tenant->tenant_token = static::generateUniqueToken();
            }
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name) . '-' . Str::random(4);
            }
        });
    }

    /**
     * Generate a unique, device-friendly (uppercase) tenant token.
     */
    public static function generateUniqueToken(int $length = 16): string
    {
        do {
            $token = strtoupper(Str::random($length));
        } while (static::where('tenant_token', $token)->exists());

        return $token;
    }

    public function regenerateToken(): string
    {
        $this->tenant_token = static::generateUniqueToken();
        $this->save();

        return $this->tenant_token;
    }

    public function admsEndpointUrl(): string
    {
        $host = request()?->getHttpHost() ?? 'hr.nexogiant.com';

        return 'https://' . $host . '/iclock/' . $this->tenant_token . '/cdata';
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

    public function canAddEmployee(): bool
    {
        return $this->employees()->count() < $this->max_employees;
    }

    public function employees()
    {
        return $this->hasMany(EmployeeProfile::class);
    }
}
