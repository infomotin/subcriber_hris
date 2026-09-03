<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Support\Facades\Schema;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'tenant_id'];

    public static function hasTenantColumn(): bool
    {
        static $hasColumn;
        if ($hasColumn === null) {
            $hasColumn = Schema::hasColumn('roles', 'tenant_id');
        }
        return $hasColumn;
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (self::hasTenantColumn() && auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id ?? 0;
            }
        });
    }

    public function scopeForTenant($query, $tenantId = null)
    {
        if (!self::hasTenantColumn()) {
            return $query;
        }

        if ($tenantId === null && auth()->check()) {
            $tenantId = auth()->user()->tenant_id;
        }

        if ($tenantId) {
            return $query->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)
                  ->orWhere('tenant_id', 0);
            });
        }

        return $query->where('tenant_id', 0);
    }

    public function scopeSystemRoles($query)
    {
        if (!self::hasTenantColumn()) {
            return $query;
        }
        return $query->where('tenant_id', 0);
    }

    public function scopeTenantRoles($query, $tenantId = null)
    {
        if (!self::hasTenantColumn()) {
            return $query;
        }

        if ($tenantId === null && auth()->check()) {
            $tenantId = auth()->user()->tenant_id;
        }

        return $query->where('tenant_id', $tenantId);
    }

    public function isSystemRole(): bool
    {
        if (!self::hasTenantColumn()) {
            return in_array($this->name, ['admin', 'hr-manager', 'employee']);
        }
        return $this->tenant_id == 0;
    }

    public function isTenantRole(): bool
    {
        if (!self::hasTenantColumn()) {
            return !in_array($this->name, ['admin', 'hr-manager', 'employee']);
        }
        return $this->tenant_id != 0 && $this->tenant_id !== null;
    }
}
