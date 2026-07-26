<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class EmployeeProfile extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'department_id',
        'designation_id',
        'employee_id',
        'joining_date',
        'gender',
        'dob',
        'phone_number',
        'blood_group',
        'status'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function bankInfo(): HasOne
    {
        return $this->hasOne(BankInformation::class, 'employee_profile_id');
    }

    public function socialLinks(): HasOne
    {
        return $this->hasOne(EmployeeSocialLink::class, 'employee_profile_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(EmployeeAddressHistory::class, 'employee_profile_id');
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(EmployeeDependent::class, 'employee_profile_id');
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(EmployeeNominee::class, 'employee_profile_id');
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(JobResponsibility::class, 'employee_profile_id');
    }

    public function education(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class, 'employee_profile_id');
    }

    public function salaryStructure(): HasOne
    {
        return $this->hasOne(SalaryStructure::class, 'employee_profile_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(EmployeeExperience::class, 'employee_profile_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(EmployeeImage::class, 'employee_profile_id');
    }
}
