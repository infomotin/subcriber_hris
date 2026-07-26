<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeeDependent extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'name',
        'relationship',
        'dob',
        'contact_number',
        'is_emergency_contact'
    ];

    protected $casts = [
        'dob' => 'date',
        'is_emergency_contact' => 'boolean'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
