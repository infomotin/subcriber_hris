<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeeSocialLink extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'linkedin_url',
        'github_url',
        'facebook_url',
        'twitter_url'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
