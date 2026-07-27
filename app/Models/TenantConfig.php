<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class TenantConfig extends Model
{
    protected $fillable = ['tenant_id', 'group', 'key', 'value'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $tenantId = auth()->user()->tenant_id ?? session('tenant_id');
        if (!$tenantId) return $default;

        $cacheKey = "tenant_config_{$tenantId}_{$group}_{$key}";
        return Cache::remember($cacheKey, 3600, function () use ($tenantId, $group, $key, $default) {
            $config = static::where('tenant_id', $tenantId)
                ->where('group', $group)
                ->where('key', $key)
                ->first();
            return $config ? $config->value : $default;
        });
    }

    public static function set(string $group, string $key, mixed $value): void
    {
        $tenantId = auth()->user()->tenant_id ?? session('tenant_id');
        if (!$tenantId) return;

        static::updateOrCreate(
            ['tenant_id' => $tenantId, 'group' => $group, 'key' => $key],
            ['value' => $value]
        );

        $cacheKey = "tenant_config_{$tenantId}_{$group}_{$key}";
        Cache::forget($cacheKey);
    }

    public static function getGroup(string $group): array
    {
        $tenantId = auth()->user()->tenant_id ?? session('tenant_id');
        if (!$tenantId) return [];

        return static::where('tenant_id', $tenantId)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function setGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($group, $key, $value);
        }
    }
}
