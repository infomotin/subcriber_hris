<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'tenant_id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id ?? 0;
            }
        });
    }

    public function scopeForTenant($query, $tenantId = null)
    {
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
        return $query->where('tenant_id', 0);
    }

    public function scopeTenantRoles($query, $tenantId = null)
    {
        if ($tenantId === null && auth()->check()) {
            $tenantId = auth()->user()->tenant_id;
        }

        return $query->where('tenant_id', $tenantId);
    }

    public function isSystemRole(): bool
    {
        return $this->tenant_id == 0;
    }

    public function isTenantRole(): bool
    {
        return $this->tenant_id != 0 && $this->tenant_id !== null;
    }
}
