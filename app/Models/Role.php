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

    /**
     * Get roles for a specific tenant ONLY (excludes system roles).
     * Used by subscriber panel to show only tenant-owned roles.
     */
    public function scopeForTenantOnly($query, $tenantId)
    {
        if (!self::hasTenantColumn()) {
            return $query;
        }
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get tenant roles + system roles (for dropdowns, permissions page, etc.)
     */
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

    public function isSystemRole(): bool
    {
        if (!self::hasTenantColumn()) {
            return false;
        }
        return $this->tenant_id == 0;
    }

    public function isTenantRole(): bool
    {
        if (!self::hasTenantColumn()) {
            return true;
        }
        return $this->tenant_id != 0 && $this->tenant_id !== null;
    }

    /**
     * Check if this role belongs to a specific tenant.
     */
    public function belongsToTenant($tenantId): bool
    {
        if (!self::hasTenantColumn()) {
            return true;
        }
        return $this->tenant_id == $tenantId;
    }
}
