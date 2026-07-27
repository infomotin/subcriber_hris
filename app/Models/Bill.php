<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class Bill extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id', 'employee_profile_id', 'bill_type_id', 'bill_purpose_id',
        'amount', 'approved_amount', 'bill_no', 'voucher_path', 'description',
        'status', 'actioned_by', 'action_remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function billType(): BelongsTo
    {
        return $this->belongsTo(BillType::class);
    }

    public function billPurpose(): BelongsTo
    {
        return $this->belongsTo(BillPurpose::class);
    }

    public function actionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    public function modifications(): HasMany
    {
        return $this->hasMany(BillModification::class);
    }
}
