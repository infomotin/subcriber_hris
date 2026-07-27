<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class Advance extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id', 'employee_profile_id', 'advance_type_id', 'advance_source_id',
        'reference_employee_id', 'amount', 'approved_amount', 'installments',
        'monthly_deduction', 'reason', 'status', 'actioned_by', 'action_remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'installments' => 'integer',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function employee(): BelongsTo { return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id'); }
    public function advanceType(): BelongsTo { return $this->belongsTo(AdvanceType::class); }
    public function advanceSource(): BelongsTo { return $this->belongsTo(AdvanceSource::class); }
    public function referenceEmployee(): BelongsTo { return $this->belongsTo(EmployeeProfile::class, 'reference_employee_id'); }
    public function actionedBy(): BelongsTo { return $this->belongsTo(User::class, 'actioned_by'); }
}
