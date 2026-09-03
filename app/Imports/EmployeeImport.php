<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class EmployeeImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    private $tenantId;
    private $errors = [];
    private $validRows = [];
    private $rowNumber = 0;
    private $divisionCache = [];
    private $districtCache = [];
    private $thanaCache = [];

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function model(array $row)
    {
        $this->rowNumber++;

        $data = [
            'name'             => trim($row['full_name'] ?? ''),
            'email'            => trim($row['email_login'] ?? ''),
            'password'         => trim($row['password'] ?? ''),
            'employee_id'      => trim($row['employee_id'] ?? ''),
            'department'       => trim($row['department'] ?? ''),
            'designation'      => trim($row['designation'] ?? ''),
            'joining_date'     => trim($row['joining_date'] ?? ''),
            'gender'           => trim($row['gender'] ?? ''),
            'phone_number'     => trim($row['phone_number'] ?? ''),
            'dob'              => trim($row['date_of_birth'] ?? ''),
            'blood_group'      => trim($row['blood_group'] ?? ''),
            'religion'         => trim($row['religion'] ?? ''),
            'marital_status'   => trim($row['marital_status'] ?? ''),
            'address_line_1'   => trim($row['address_line'] ?? ''),
            'state'            = trim($row['state_division'] ?? ''),
            'district'         => trim($row['district'] ?? ''),
            'thana'            => trim($row['thana_upazila'] ?? ''),
            'zip_code'         => trim($row['zip_code'] ?? ''),
            'country'          => trim($row['country'] ?? 'Bangladesh'),
            'perm_address'     => trim($row['permanent_address'] ?? ''),
            'perm_state'       => trim($row['permanent_state'] ?? ''),
            'perm_district'    => trim($row['permanent_district'] ?? ''),
            'perm_zip'         => trim($row['permanent_zip'] ?? ''),
            'nid'              => trim($row['nid_number'] ?? ''),
            'employee_type'    => trim($row['employee_type'] ?? ''),
            'status'           => trim($row['status'] ?? 'active'),
            'overtime_eligible' => trim($row['overtime_eligible_1_0'] ?? ''),
            'overtime_rate'    => trim($row['overtime_rate_bdt_hr'] ?? ''),
            'bank_name'        => trim($row['bank_name'] ?? ''),
            'branch_name'      => trim($row['branch_name'] ?? ''),
            'account_name'     => trim($row['account_holder_name'] ?? ''),
            'account_number'   => trim($row['account_number'] ?? ''),
            'routing_number'   => trim($row['routing_number'] ?? ''),
            'payment_mode'     => trim($row['payment_mode'] ?? 'bank_transfer'),
            'father_name'      => trim($row['father_name'] ?? ''),
            'father_occupation' => trim($row['father_occupation'] ?? ''),
            'mother_name'      => trim($row['mother_name'] ?? ''),
            'mother_occupation' => trim($row['mother_occupation'] ?? ''),
            'guardian_name'    => trim($row['guardian_name'] ?? ''),
            'guardian_relation' => trim($row['guardian_relation'] ?? ''),
            'guardian_phone'   => trim($row['guardian_phone'] ?? ''),
        ];

        $errors = $this->validateRow($data);

        if (!empty($errors)) {
            $this->errors[$this->rowNumber] = $errors;
            return null;
        }

        $this->validRows[$this->rowNumber] = $data;
        return null;
    }

    private function validateRow(array $d): array
    {
        $e = [];

        if (empty($d['name']))           $e[] = 'Name is required';
        if (empty($d['email']))          $e[] = 'Email is required';
        elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e[] = 'Invalid email format';
        if (empty($d['password']))       $e[] = 'Password is required';
        elseif (strlen($d['password']) < 8) $e[] = 'Password must be min 8 chars';
        if (empty($d['employee_id']))    $e[] = 'Employee ID is required';
        if (empty($d['joining_date']))   $e[] = 'Joining date is required';
        elseif (!$this->isValidDate($d['joining_date'])) $e[] = 'Joining date must be YYYY-MM-DD';
        if (empty($d['gender']))         $e[] = 'Gender is required';
        if (empty($d['phone_number']))   $e[] = 'Phone number is required';
        if (empty($d['dob']))            $e[] = 'Date of birth is required';
        elseif (!$this->isValidDate($d['dob'])) $e[] = 'DOB must be YYYY-MM-DD';
        if (empty($d['address_line_1'])) $e[] = 'Address is required';
        if (empty($d['state']))          $e[] = 'State / Division is required';
        if (empty($d['district']))       $e[] = 'District is required';
        if (empty($d['thana']))          $e[] = 'Thana is required';
        if (empty($d['zip_code']))       $e[] = 'Zip code is required';
        if (empty($d['status']))         $e[] = 'Status is required';
        elseif (!in_array($d['status'], ['active','probation','terminated','resigned'])) $e[] = 'Invalid status';

        if (!empty($d['employee_type']) && !in_array($d['employee_type'], ['worker','staff','manager'])) {
            $e[] = 'Invalid employee type';
        }
        if (!empty($d['payment_mode']) && !in_array($d['payment_mode'], ['bank_transfer','cash','mobile_banking'])) {
            $e[] = 'Invalid payment mode';
        }
        if (!empty($d['bank_name']) && empty($d['branch_name']))  $e[] = 'Branch name required with bank';
        if (!empty($d['bank_name']) && empty($d['account_name'])) $e[] = 'Account name required with bank';
        if (!empty($d['bank_name']) && empty($d['account_number'])) $e[] = 'Account number required with bank';

        // Check uniqueness in DB
        if (!empty($d['email']) && empty($e)) {
            if (User::withoutGlobalScopes()->where('email', $d['email'])->exists()) {
                $e[] = 'Email already exists in system';
            }
        }
        if (!empty($d['employee_id']) && empty($e)) {
            if (EmployeeProfile::withoutGlobalScopes()->where('employee_id', $d['employee_id'])->where('tenant_id', $this->tenantId)->exists()) {
                $e[] = 'Employee ID already exists';
            }
        }

        // Validate references
        if (!empty($d['department']) && empty($e)) {
            if (!Department::withoutGlobalScopes()->whereRaw('LOWER(name) = ?', [strtolower($d['department'])])->exists()) {
                $e[] = 'Department "' . $d['department'] . '" not found';
            }
        }
        if (!empty($d['designation']) && empty($e)) {
            if (!Designation::withoutGlobalScopes()->whereRaw('LOWER(title) = ?', [strtolower($d['designation'])])->exists()) {
                $e[] = 'Designation "' . $d['designation'] . '" not found';
            }
        }

        // Validate address references
        if (!empty($d['state'])) {
            $div = Division::whereRaw('LOWER(name) = ?', [strtolower($d['state'])])->first();
            if (!$div) {
                $e[] = 'Division "' . $d['state'] . '" not found';
            } else {
                if (!empty($d['district'])) {
                    $dist = $div->districts()->whereRaw('LOWER(name) = ?', [strtolower($d['district'])])->first();
                    if (!$dist) {
                        $e[] = 'District "' . $d['district'] . '" not found in ' . $d['state'];
                    } else {
                        if (!empty($d['thana'])) {
                            $thana = $dist->thanas()->whereRaw('LOWER(name) = ?', [strtolower($d['thana'])])->first();
                            if (!$thana) {
                                $e[] = 'Thana "' . $d['thana'] . '" not found in ' . $d['district'];
                            }
                        }
                    }
                }
            }
        }

        return $e;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidRows(): array
    {
        return $this->validRows;
    }

    public function import(): array
    {
        $successCount = 0;
        $imported = [];

        foreach ($this->validRows as $rowNum => $data) {
            try {
                $result = DB::transaction(function () use ($data) {
                    $user = User::create([
                        'tenant_id' => $this->tenantId,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ]);

                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('Subscriber');
                    }

                    // Resolve references
                    $dept = Department::withoutGlobalScopes()
                        ->whereRaw('LOWER(name) = ?', [strtolower($data['department'])])->first();
                    $desig = Designation::withoutGlobalScopes()
                        ->whereRaw('LOWER(title) = ?', [strtolower($data['designation'])])->first();

                    // Resolve address
                    $stateName = $data['state'];
                    $districtName = $data['district'];
                    $thanaName = $data['thana'];
                    $cityValue = !empty($thanaName) && !empty($districtName)
                        ? $thanaName . ', ' . $districtName : $districtName;

                    $profile = EmployeeProfile::create([
                        'tenant_id'       => $this->tenantId,
                        'user_id'         => $user->id,
                        'department_id'   => $dept?->id,
                        'designation_id'  => $desig?->id,
                        'employee_id'     => $data['employee_id'],
                        'joining_date'    => $data['joining_date'],
                        'gender'          => $data['gender'],
                        'dob'             => $data['dob'],
                        'phone_number'    => $data['phone_number'],
                        'blood_group'     => $data['blood_group'] ?: null,
                        'status'          => $data['status'],
                        'employee_type'   => $data['employee_type'] ?: null,
                        'overtime_eligible' => $data['overtime_eligible'] === '1',
                        'overtime_rate'   => is_numeric($data['overtime_rate']) ? $data['overtime_rate'] : null,
                        'nid'             => $data['nid'] ?: null,
                        'religion'        => $data['religion'] ?: null,
                        'marital_status'  => $data['marital_status'] ?: null,
                        'father_name'     => $data['father_name'] ?: null,
                        'father_occupation' => $data['father_occupation'] ?: null,
                        'mother_name'     => $data['mother_name'] ?: null,
                        'mother_occupation' => $data['mother_occupation'] ?: null,
                        'guardian_name'   => $data['guardian_name'] ?: null,
                        'guardian_relation' => $data['guardian_relation'] ?: null,
                        'guardian_phone'  => $data['guardian_phone'] ?: null,
                    ]);

                    // Current address
                    $profile->addresses()->create([
                        'tenant_id'      => $this->tenantId,
                        'type'           => 'current',
                        'address_line_1' => $data['address_line_1'],
                        'city'           => $cityValue,
                        'state'          => $stateName,
                        'zip_code'       => $data['zip_code'],
                        'country'        => $data['country'] ?: 'Bangladesh',
                        'is_active'      => true,
                    ]);

                    // Permanent address
                    if (!empty($data['perm_address'])) {
                        $permCity = !empty($data['perm_district']) ? $data['perm_district'] : '';
                        $profile->addresses()->create([
                            'tenant_id'      => $this->tenantId,
                            'type'           => 'permanent',
                            'address_line_1' => $data['perm_address'],
                            'city'           => $permCity,
                            'state'          => $data['perm_state'] ?: '',
                            'zip_code'       => $data['perm_zip'] ?: '',
                            'country'        => 'Bangladesh',
                            'is_active'      => true,
                        ]);
                    }

                    // Bank info
                    if (!empty($data['bank_name'])) {
                        $profile->bankInfo()->create([
                            'tenant_id'      => $this->tenantId,
                            'bank_name'      => $data['bank_name'],
                            'branch_name'    => $data['branch_name'],
                            'account_name'   => $data['account_name'],
                            'account_number' => $data['account_number'],
                            'routing_number' => $data['routing_number'] ?: null,
                            'payment_mode'   => $data['payment_mode'] ?: 'bank_transfer',
                        ]);
                    }

                    return $user->id;
                });

                $successCount++;
                $imported[$rowNum] = $successCount;
            } catch (\Exception $ex) {
                $this->errors[$rowNum][] = 'Import failed: ' . $ex->getMessage();
            }
        }

        return [
            'success'  => $successCount,
            'errors'   => $this->errors,
            'imported' => $imported,
        ];
    }
}
