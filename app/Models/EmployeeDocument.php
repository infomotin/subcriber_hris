<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\Multitenantable;

class EmployeeDocument extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'documentable_id',
        'documentable_type',
        'label',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
