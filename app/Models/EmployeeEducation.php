<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Multitenantable;

class EmployeeEducation extends Model
{
    use Multitenantable;

    protected $table = 'employee_education';

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'degree_name',
        'institution',
        'passing_year',
        'result',
        'certification_type'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(EmployeeDocument::class, 'documentable');
    }
}
