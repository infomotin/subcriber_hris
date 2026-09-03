<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Exports\EmployeeTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeImportController extends Controller
{
    public function showImportForm()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        return view('subscriber.hris.employees.import');
    }

    public function downloadTemplate()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $tenantId = $tenant?->id ?? 0;

        return Excel::download(new EmployeeTemplateExport($tenantId), 'employee_import_template.xlsx');
    }

    public function preview(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? null;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('import_file');

        try {
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return back()->with('error', 'The Excel file is empty or has no data rows.');
            }

            $headings = array_map(fn($h) => strtolower(trim(str_replace(['*', ' '], ['', '_'], $h))), $rows[0]);
            $dataRows = array_slice($rows, 1);

            $validRows = [];
            $errorRows = [];

            foreach ($dataRows as $idx => $row) {
                if (empty(array_filter($row))) continue;

                $data = array_combine($headings, $row);
                $rowNum = $idx + 2;

                $errors = $this->validateRow($data, $tenant->id);
                if (!empty($errors)) {
                    $errorRows[$rowNum] = ['data' => $data, 'errors' => $errors];
                } else {
                    $validRows[$rowNum] = $data;
                }
            }

            session([
                'import_valid_rows' => $validRows,
                'import_error_rows' => $errorRows,
                'import_file_name'  => $file->getClientOriginalName(),
            ]);

            return view('subscriber.hris.employees.import', [
                'validRows'   => $validRows,
                'errorRows'   => $errorRows,
                'fileName'    => $file->getClientOriginalName(),
                'preview'     => true,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to read Excel file: ' . $e->getMessage());
        }
    }

    public function doImport(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? null;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $validRows = session('import_valid_rows', []);
        if (empty($validRows)) {
            return back()->with('error', 'No valid rows to import. Please upload and preview first.');
        }

        $successCount = 0;
        $failedRows = [];

        foreach ($validRows as $rowNum => $data) {
            try {
                DB::transaction(function () use ($data, $tenant, &$successCount) {
                    $name   = trim($data['full_name'] ?? $data['full name'] ?? '');
                    $email  = trim($data['email_login'] ?? $data['email_(login)'] ?? $data['email'] ?? '');
                    $pass   = trim($data['password'] ?? '');
                    $empId  = trim($data['employee_id'] ?? $data['employee id'] ?? '');
                    $dept   = trim($data['department'] ?? '');
                    $desig  = trim($data['designation'] ?? '');
                    $jDate  = trim($data['joining_date'] ?? $data['joining date'] ?? '');
                    $gender = trim($data['gender'] ?? '');
                    $phone  = trim($data['phone_number'] ?? $data['phone number'] ?? '');
                    $dob    = trim($data['date_of_birth'] ?? $data['date of birth'] ?? '');
                    $bg     = trim($data['blood_group'] ?? $data['blood group'] ?? '');
                    $relig  = trim($data['religion'] ?? '');
                    $marital = trim($data['marital_status'] ?? $data['marital status'] ?? '');
                    $addr   = trim($data['address_line'] ?? $data['address line'] ?? '');
                    $state  = trim($data['state_division'] ?? $data['state_/_division'] ?? $data['state / division'] ?? '');
                    $dist   = trim($data['district'] ?? '');
                    $thana  = trim($data['thana_upazila'] ?? $data['thana_/_upazila'] ?? $data['thana / upazila'] ?? '');
                    $zip    = trim($data['zip_code'] ?? $data['zip code'] ?? '');
                    $country = trim($data['country'] ?? 'Bangladesh');
                    $permAddr  = trim($data['permanent_address'] ?? $data['permanent address'] ?? '');
                    $permState = trim($data['permanent_state'] ?? $data['permanent state'] ?? '');
                    $permDist  = trim($data['permanent_district'] ?? $data['permanent district'] ?? '');
                    $permZip   = trim($data['permanent_zip'] ?? $data['permanent zip'] ?? '');
                    $nid    = trim($data['nid_number'] ?? $data['nid number'] ?? '');
                    $empType = trim($data['employee_type'] ?? $data['employee type'] ?? '');
                    $status = trim($data['status'] ?? 'active');
                    $otElig = trim($data['overtime_eligible_1_0'] ?? $data['overtime eligible_(1/0)'] ?? '');
                    $otRate = trim($data['overtime_rate_bdt_hr'] ?? $data['overtime rate_(bdt/hr)'] ?? '');
                    $bank   = trim($data['bank_name'] ?? $data['bank name'] ?? '');
                    $branch = trim($data['branch_name'] ?? $data['branch name'] ?? '');
                    $accName = trim($data['account_holder_name'] ?? $data['account holder name'] ?? '');
                    $accNum = trim($data['account_number'] ?? $data['account number'] ?? '');
                    $route  = trim($data['routing_number'] ?? $data['routing number'] ?? '');
                    $pMode  = trim($data['payment_mode'] ?? $data['payment mode'] ?? 'bank_transfer');
                    $fName  = trim($data['father_name'] ?? $data['father name'] ?? '');
                    $fOcc  = trim($data['father_occupation'] ?? $data['father occupation'] ?? '');
                    $mName  = trim($data['mother_name'] ?? $data['mother name'] ?? '');
                    $mOcc  = trim($data['mother_occupation'] ?? $data['mother occupation'] ?? '');
                    $gName  = trim($data['guardian_name'] ?? $data['guardian name'] ?? '');
                    $gRel  = trim($data['guardian_relation'] ?? $data['guardian relation'] ?? '');
                    $gPhone = trim($data['guardian_phone'] ?? $data['guardian phone'] ?? '');

                    $user = User::create([
                        'tenant_id' => $tenant->id,
                        'name'      => $name,
                        'email'     => $email,
                        'password'  => $pass,
                    ]);

                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('Subscriber');
                    }

                    $deptModel = Department::withoutGlobalScopes()
                        ->whereRaw('LOWER(name) = ?', [strtolower($dept)])->first();
                    $desigModel = Designation::withoutGlobalScopes()
                        ->whereRaw('LOWER(title) = ?', [strtolower($desig)])->first();

                    $cityValue = !empty($thana) && !empty($dist) ? $thana . ', ' . $dist : $dist;

                    $profile = EmployeeProfile::create([
                        'tenant_id'        => $tenant->id,
                        'user_id'          => $user->id,
                        'department_id'    => $deptModel?->id,
                        'designation_id'   => $desigModel?->id,
                        'employee_id'      => $empId,
                        'joining_date'     => $jDate,
                        'gender'           => $gender,
                        'dob'              => $dob,
                        'phone_number'     => $phone,
                        'blood_group'      => $bg ?: null,
                        'status'           => $status,
                        'employee_type'    => $empType ?: null,
                        'overtime_eligible' => $otElig === '1',
                        'overtime_rate'     => is_numeric($otRate) ? $otRate : null,
                        'nid'              => $nid ?: null,
                        'religion'         => $relig ?: null,
                        'marital_status'   => $marital ?: null,
                        'father_name'      => $fName ?: null,
                        'father_occupation' => $fOcc ?: null,
                        'mother_name'      => $mName ?: null,
                        'mother_occupation' => $mOcc ?: null,
                        'guardian_name'    => $gName ?: null,
                        'guardian_relation' => $gRel ?: null,
                        'guardian_phone'   => $gPhone ?: null,
                    ]);

                    $profile->addresses()->create([
                        'tenant_id'      => $tenant->id,
                        'type'           => 'current',
                        'address_line_1' => $addr,
                        'city'           => $cityValue,
                        'state'          => $state,
                        'zip_code'       => $zip,
                        'country'        => $country ?: 'Bangladesh',
                        'is_active'      => true,
                    ]);

                    if (!empty($permAddr)) {
                        $profile->addresses()->create([
                            'tenant_id'      => $tenant->id,
                            'type'           => 'permanent',
                            'address_line_1' => $permAddr,
                            'city'           => $permDist,
                            'state'          => $permState,
                            'zip_code'       => $permZip,
                            'country'        => 'Bangladesh',
                            'is_active'      => true,
                        ]);
                    }

                    if (!empty($bank)) {
                        $profile->bankInfo()->create([
                            'tenant_id'      => $tenant->id,
                            'bank_name'      => $bank,
                            'branch_name'    => $branch,
                            'account_name'   => $accName,
                            'account_number' => $accNum,
                            'routing_number' => $route ?: null,
                            'payment_mode'   => $pMode ?: 'bank_transfer',
                        ]);
                    }

                    $successCount++;
                });
            } catch (\Exception $ex) {
                $failedRows[$rowNum] = $ex->getMessage();
            }
        }

        session()->forget(['import_valid_rows', 'import_error_rows', 'import_file_name']);

        $msg = "Import complete: {$successCount} employees added successfully.";
        if (!empty($failedRows)) {
            $msg .= " " . count($failedRows) . " rows failed.";
        }

        return redirect()->route('subscriber.hris.employees.index')->with('success', $msg);
    }

    private function validateRow(array $d, int $tenantId): array
    {
        $e = [];

        $name   = trim($d['full_name'] ?? $d['full name'] ?? '');
        $email  = trim($d['email_login'] ?? $d['email_(login)'] ?? $d['email'] ?? '');
        $pass   = trim($d['password'] ?? '');
        $empId  = trim($d['employee_id'] ?? $d['employee id'] ?? '');
        $dept   = trim($d['department'] ?? '');
        $desig  = trim($d['designation'] ?? '');
        $jDate  = trim($d['joining_date'] ?? $d['joining date'] ?? '');
        $gender = trim($d['gender'] ?? '');
        $phone  = trim($d['phone_number'] ?? $d['phone number'] ?? '');
        $dob    = trim($d['date_of_birth'] ?? $d['date of birth'] ?? '');
        $addr   = trim($d['address_line'] ?? $d['address line'] ?? '');
        $state  = trim($d['state_division'] ?? $d['state_/_division'] ?? $d['state / division'] ?? '');
        $dist   = trim($d['district'] ?? '');
        $thana  = trim($d['thana_upazila'] ?? $d['thana_/_upazila'] ?? $d['thana / upazila'] ?? '');
        $zip    = trim($d['zip_code'] ?? $d['zip code'] ?? '');
        $status = trim($d['status'] ?? '');
        $empType = trim($d['employee_type'] ?? $d['employee type'] ?? '');
        $pMode  = trim($d['payment_mode'] ?? $d['payment mode'] ?? '');
        $bank   = trim($d['bank_name'] ?? $d['bank name'] ?? '');
        $branch = trim($d['branch_name'] ?? $d['branch name'] ?? '');
        $accName = trim($d['account_holder_name'] ?? $d['account holder name'] ?? '');
        $accNum = trim($d['account_number'] ?? $d['account number'] ?? '');

        if (empty($name))  $e[] = 'Name required';
        if (empty($email)) $e[] = 'Email required';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $e[] = 'Invalid email';
        if (empty($pass))  $e[] = 'Password required';
        elseif (strlen($pass) < 8) $e[] = 'Password min 8 chars';
        if (empty($empId)) $e[] = 'Employee ID required';
        if (empty($jDate)) $e[] = 'Joining date required';
        elseif (!$this->isValidDate($jDate)) $e[] = 'Date must be YYYY-MM-DD';
        if (empty($gender)) $e[] = 'Gender required';
        if (empty($phone)) $e[] = 'Phone required';
        if (empty($dob))   $e[] = 'DOB required';
        elseif (!$this->isValidDate($dob)) $e[] = 'DOB must be YYYY-MM-DD';
        if (empty($addr))  $e[] = 'Address required';
        if (empty($state)) $e[] = 'State/Division required';
        if (empty($dist))  $e[] = 'District required';
        if (empty($thana)) $e[] = 'Thana required';
        if (empty($zip))   $e[] = 'Zip required';
        if (empty($status)) $e[] = 'Status required';
        elseif (!in_array($status, ['active','probation','terminated','resigned'])) $e[] = 'Invalid status';
        if (!empty($empType) && !in_array($empType, ['worker','staff','manager'])) $e[] = 'Invalid employee type';
        if (!empty($pMode) && !in_array($pMode, ['bank_transfer','cash','mobile_banking'])) $e[] = 'Invalid payment mode';
        if (!empty($bank) && empty($branch))  $e[] = 'Branch required with bank';
        if (!empty($bank) && empty($accName)) $e[] = 'Account name required';
        if (!empty($bank) && empty($accNum))  $e[] = 'Account number required';

        if (!empty($email) && empty($e)) {
            if (User::withoutGlobalScopes()->where('email', $email)->exists()) {
                $e[] = 'Email already exists';
            }
        }
        if (!empty($empId) && empty($e)) {
            if (EmployeeProfile::withoutGlobalScopes()->where('employee_id', $empId)->where('tenant_id', $tenantId)->exists()) {
                $e[] = 'Employee ID already exists';
            }
        }
        if (!empty($dept) && empty($e)) {
            if (!Department::withoutGlobalScopes()->whereRaw('LOWER(name) = ?', [strtolower($dept)])->exists()) {
                $e[] = 'Department "' . $dept . '" not found';
            }
        }
        if (!empty($desig) && empty($e)) {
            if (!Designation::withoutGlobalScopes()->whereRaw('LOWER(title) = ?', [strtolower($desig)])->exists()) {
                $e[] = 'Designation "' . $desig . '" not found';
            }
        }
        if (!empty($state) && empty($e)) {
            $div = Division::whereRaw('LOWER(name) = ?', [strtolower($state)])->first();
            if (!$div) {
                $e[] = 'Division "' . $state . '" not found';
            } elseif (!empty($dist)) {
                $distObj = $div->districts()->whereRaw('LOWER(name) = ?', [strtolower($dist)])->first();
                if (!$distObj) {
                    $e[] = 'District "' . $dist . '" not found in ' . $state;
                } elseif (!empty($thana)) {
                    $thanaObj = $distObj->thanas()->whereRaw('LOWER(name) = ?', [strtolower($thana)])->first();
                    if (!$thanaObj) {
                        $e[] = 'Thana "' . $thana . '" not found in ' . $dist;
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
}
