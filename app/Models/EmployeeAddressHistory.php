<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeeAddressHistory extends Model
{
    use Multitenantable;

    protected $table = 'employee_addresses';

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'country',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
