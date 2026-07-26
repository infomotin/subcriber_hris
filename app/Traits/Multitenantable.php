<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable
{
    public static function bootMultitenantable(): void
    {
        // Automatically set tenant_id when creating new records
        static::creating(function ($model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Apply global tenant_id scoping
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }
}
