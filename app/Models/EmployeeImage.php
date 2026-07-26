<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeeImage extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'type',
        'file_path'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
