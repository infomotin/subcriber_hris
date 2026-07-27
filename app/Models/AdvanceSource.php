<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class AdvanceSource extends Model
{
    use Multitenantable;

    protected $fillable = ['tenant_id', 'name', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function advances(): HasMany { return $this->hasMany(Advance::class); }
}
