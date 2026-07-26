<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class Kpi extends Model
{
    use Multitenantable;

    protected $table = 'kpis';

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'goal_title',
        'description',
        'target_date',
        'weightage',
        'status',
        'score_rating'
    ];

    protected $casts = [
        'target_date' => 'date',
        'weightage' => 'integer',
        'score_rating' => 'integer'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
