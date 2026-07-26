<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class EmployeeVerification extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tenant_id',
        'employee_profile_id',
        'section',
        'status',
        'verified_by',
        'verification_method',
        'remarks',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public const SECTIONS = [
        'identity'   => 'Identity (NID/Birth Certificate)',
        'education'  => 'Education (Certificates)',
        'experience' => 'Experience (Employment)',
        'bank'       => 'Bank Account Details',
        'address'    => 'Address Information',
        'documents'  => 'Documents (NOC/Police Clearance)',
    ];

    public const METHODS = [
        'document_check' => 'Document Check',
        'in_person' => 'In-Person Verification',
        'online_verification' => 'Online Verification',
        'third_party' => 'Third-Party Verification',
        'self_declaration' => 'Self Declaration',
        'supervisor_confirmation' => 'Supervisor Confirmation',
        'bank_confirmation' => 'Bank Confirmation',
        'physical_id_check' => 'Physical ID Check',
    ];

    public const VERIFIED_BY = [
        'identity'   => 'HR Admin',
        'education'  => 'Education Board',
        'experience' => 'Previous Employer',
        'bank'       => 'Bank Authority',
        'address'    => 'HR Admin',
        'documents'  => 'HR Admin',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
