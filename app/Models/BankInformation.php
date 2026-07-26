<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class BankInformation extends Model
{
    use Multitenantable;

    protected $table = 'bank_informations';

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'routing_number',
        'payment_mode'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
