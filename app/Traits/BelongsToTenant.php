<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (! $model->tenant_id && ($tenantId = static::currentTenantId())) {
                $model->tenant_id = $tenantId;
            }
        });

        static::addGlobalScope('tenant_scope', function (Builder $builder) {
            if ($tenantId = static::currentTenantId()) {
                $builder->where($builder->getQuery()->from . '.tenant_id', $tenantId);
            }
        });
    }

    public static function currentTenantId(): ?int
    {
        if (app()->bound('current_tenant_id')) {
            return app('current_tenant_id');
        }

        if (session()->has('tenant_id')) {
            return session('tenant_id');
        }

        if (auth()->check() && auth()->user()->tenant_id) {
            return auth()->user()->tenant_id;
        }

        return null;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
