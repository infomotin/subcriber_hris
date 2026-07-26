<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\WorkShift;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'status',
        'nid',
        'birth_certificate',
        'religion',
        'marital_status',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'shift_id',
        'employee_type',
        'overtime_eligible',
        'overtime_rate',
    ];

    protected $casts = [
        'overtime_eligible' => 'boolean',
        'overtime_rate' => 'decimal:2',
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

    public function shift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class, 'shift_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(EmployeePromotion::class, 'employee_profile_id');
    }

    public function increments(): HasMany
    {
        return $this->hasMany(Increment::class, 'employee_profile_id');
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

    public function documents(): MorphMany
    {
        return $this->morphMany(EmployeeDocument::class, 'documentable');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(EmployeeVerification::class, 'employee_profile_id');
    }

    public function verificationProgress(): int
    {
        $total = count(EmployeeVerification::SECTIONS);
        $done = $this->verifications()
            ->where('status', 'verified')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();
        return $total > 0 ? (int) round(($done / $total) * 100) : 0;
    }

    public function isFullyVerified(): bool
    {
        return $this->verificationProgress() === 100;
    }

    public function verificationBadge(): string
    {
        $pct = $this->verificationProgress();
        if ($pct === 100) return 'bg-soft-success text-success';
        if ($pct >= 50) return 'bg-soft-warning text-warning';
        return 'bg-soft-danger text-danger';
    }
}
