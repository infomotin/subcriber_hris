<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\EmployeeProfile;
use App\Models\EmployeeVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    public function show(Request $request, $module)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        // Map general mockups dynamically based on route module
        $config = [];
        switch ($module) {
            case 'calendar':
                $config = [
                    'title' => 'Holiday Calendar Setup',
                    'subtitle' => 'Configure organization holidays and calendar year parameters',
                    'fields' => [
                        ['name' => 'year', 'label' => 'Calendar Year', 'type' => 'number', 'placeholder' => 'e.g. ' . date('Y'), 'value' => date('Y')],
                        ['name' => 'title', 'label' => 'Holiday Occasion', 'type' => 'text', 'placeholder' => 'e.g. Eid-ul-Fitr / Independence Day'],
                        ['name' => 'date', 'label' => 'Date', 'type' => 'date'],
                        ['name' => 'is_paid', 'label' => 'Paid Holiday', 'type' => 'select', 'options' => ['1' => 'Yes', '0' => 'No']]
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => date('Y'), 'col2' => 'International Mother Language Day', 'col3' => 'Feb 21, ' . date('Y'), 'col4' => 'Paid Holiday'],
                        ['id' => 2, 'col1' => date('Y'), 'col2' => 'Independence Day', 'col3' => 'Mar 26, ' . date('Y'), 'col4' => 'Paid Holiday']
                    ],
                    'headers' => ['Year', 'Occasion', 'Date', 'Type']
                ];
                break;
            case 'addresses':
                $config = [
                    'title' => 'Geographic Address Settings',
                    'subtitle' => 'Configure locations, zipcodes, and corporate headquarters addresses',
                    'fields' => [
                        ['name' => 'location_name', 'label' => 'Location Office Name', 'type' => 'text', 'placeholder' => 'e.g. Dhaka HQ / Chittagong Branch'],
                        ['name' => 'city', 'label' => 'City', 'type' => 'text', 'placeholder' => 'Dhaka'],
                        ['name' => 'zip', 'label' => 'Zip Code', 'type' => 'text', 'placeholder' => '1212'],
                        ['name' => 'country', 'label' => 'Country', 'type' => 'text', 'placeholder' => 'Bangladesh']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'Dhaka Headquarters', 'col2' => 'Dhaka', 'col3' => '1212', 'col4' => 'Bangladesh'],
                        ['id' => 2, 'col1' => 'Chittagong Distribution Hub', 'col2' => 'Chittagong', 'col3' => '4000', 'col4' => 'Bangladesh']
                    ],
                    'headers' => ['Office Location', 'City', 'Zip Code', 'Country']
                ];
                break;
            case 'other':
                $config = [
                    'title' => 'Other Parameters & Configs',
                    'subtitle' => 'Audit logs, system preferences, and other SaaS parameters',
                    'fields' => [
                        ['name' => 'parameter_name', 'label' => 'Parameter Key', 'type' => 'text', 'placeholder' => 'e.g. LATE_BUFFER_IN_MINUTES'],
                        ['name' => 'value', 'label' => 'Parameter Value', 'type' => 'text', 'placeholder' => '15']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'FORCE_OTP_AUTHENTICATION', 'col2' => 'True', 'col3' => 'System security parameters', 'col4' => 'Active'],
                        ['id' => 2, 'col1' => 'MAX_DEVICES_ALLOWED', 'col2' => $tenant->max_devices, 'col3' => 'Limit of physical ZK terminals', 'col4' => 'Active']
                    ],
                    'headers' => ['Parameter Key', 'Value', 'Scope', 'Status']
                ];
                break;
            case 'verification':
                $query = EmployeeProfile::with(['user', 'department', 'designation', 'verifications']);

                $search = request('search');
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('employee_id', 'like', "%{$search}%")
                          ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
                    });
                }
                if (request('status_filter') === 'verified') {
                    $query->whereHas('verifications', fn($vq) => $vq->where('status', 'verified'), '=', 6);
                } elseif (request('status_filter') === 'pending') {
                    $query->whereHas('verifications', fn($vq) => $vq->where('status', 'pending'), '>', 0);
                } elseif (request('status_filter') === 'expired') {
                    $query->whereHas('verifications', fn($vq) => $vq->where('expires_at', '<', now()), '>', 0);
                }
                if (request('section')) {
                    $query->whereHas('verifications', fn($vq) => $vq->where('section', request('section'))->where('status', 'pending'));
                }

                $employees = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();
                $sections = EmployeeVerification::SECTIONS;
                return view('subscriber.hris.verification', compact('employees', 'sections'));
                break;
            case 'increments':
                $config = [
                    'title' => 'Increments & Salary Adjustments',
                    'subtitle' => 'Record annual increments and basic pay adjustments',
                    'fields' => [
                        ['name' => 'emp_id', 'label' => 'Employee ID', 'type' => 'text', 'placeholder' => 'EMP-1001'],
                        ['name' => 'amount', 'label' => 'Salary Increment Amount (BDT)', 'type' => 'number', 'placeholder' => '5000'],
                        ['name' => 'effective_date', 'label' => 'Effective Date', 'type' => 'date']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'EMP-1001', 'col2' => '5,000.00 BDT', 'col3' => 'Annual Performance Increment', 'col4' => 'Jan 01, 2026'],
                        ['id' => 2, 'col1' => 'EMP-1005', 'col2' => '2,500.00 BDT', 'col3' => 'Promotion adjustment', 'col4' => 'Apr 15, 2026']
                    ],
                    'headers' => ['Employee ID', 'Increment Amount', 'Reasoning', 'Effective Date']
                ];
                break;
            case 'applications':
                $config = [
                    'title' => 'General General Applications System',
                    'subtitle' => 'Manage expense reimbursements, equipment requests, and other tools',
                    'fields' => [
                        ['name' => 'title', 'label' => 'Application Title', 'type' => 'text', 'placeholder' => 'e.g. Laptop replacement / Petty cash reimbursement'],
                        ['name' => 'amount', 'label' => 'Associated Cost (if applicable)', 'type' => 'number', 'placeholder' => '0'],
                        ['name' => 'date', 'label' => 'Application Date', 'type' => 'date']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'Petty Cash Requisition', 'col2' => '1,500.00 BDT', 'col3' => 'Office stationeries purchase', 'col4' => 'Approved & Disbursed'],
                        ['id' => 2, 'col1' => 'Travel Expense Claim', 'col2' => '4,200.00 BDT', 'col3' => 'Biometric device installation onsite', 'col4' => 'Pending manager approval']
                    ],
                    'headers' => ['Application Detail', 'Cost Estimate', 'Remarks', 'Status']
                ];
                break;
            case 'advances':
                $config = [
                    'title' => 'Salary Advances & Loans',
                    'subtitle' => 'Manage advances and provident fund loans allocations',
                    'fields' => [
                        ['name' => 'emp_id', 'label' => 'Employee ID', 'type' => 'text', 'placeholder' => 'EMP-1002'],
                        ['name' => 'loan_amount', 'label' => 'Advance Loan Amount (BDT)', 'type' => 'number', 'placeholder' => '20000'],
                        ['name' => 'installments', 'label' => 'Monthly Installments to Deduct', 'type' => 'number', 'placeholder' => '5']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'EMP-1002', 'col2' => '15,000.00 BDT', 'col3' => '3 Months Installment Plan', 'col4' => 'Disbursed'],
                        ['id' => 2, 'col1' => 'EMP-1008', 'col2' => '20,000.00 BDT', 'col3' => '5 Months Installment Plan', 'col4' => 'Pending Verification']
                    ],
                    'headers' => ['Employee ID', 'Loan Amount', 'Terms Plan', 'Status']
                ];
                break;
            case 'reports':
                $config = [
                    'title' => 'Analytical HRIS Dashboard Reports',
                    'subtitle' => 'Printable documents, monthly logs, and system sheets',
                    'fields' => [
                        ['name' => 'report_type', 'label' => 'Select Report Type', 'type' => 'select', 'options' => [
                            'employee_roster' => 'Employee Master Directory Report',
                            'monthly_attendance' => 'Monthly Attendance Summary Sheet',
                            'salary_payslips' => 'Monthly Salary Sheet & Payslips Log'
                        ]],
                        ['name' => 'month', 'label' => 'Month', 'type' => 'month']
                    ],
                    'dummy_data' => [
                        ['id' => 1, 'col1' => 'Employee Master Directory Report', 'col2' => 'All Departments', 'col3' => 'PDF / Excel Export', 'col4' => 'Generate Report'],
                        ['id' => 2, 'col1' => 'Monthly Attendance Summary Sheet', 'col2' => 'July 2026', 'col3' => 'PDF / Excel Export', 'col4' => 'Generate Report']
                    ],
                    'headers' => ['Report Sheet Title', 'Scope Filters', 'Export Format', 'Action']
                ];
                break;
        }

        if ($module === 'calendar') {
            return view('subscriber.hris.calendar', compact('tenant', 'module'));
        }
        return view('subscriber.hris.general_form', compact('config', 'module'));
    }

    public function submit(Request $request, $module)
    {
        return redirect()->back()->with('success', 'Form submitted successfully! (Mock Action Saved).');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_profiles,id',
            'section' => 'required|string|in:' . implode(',', array_keys(EmployeeVerification::SECTIONS)),
            'verified_by' => 'nullable|string|max:100',
            'verification_method' => 'required|string|in:' . implode(',', array_keys(EmployeeVerification::METHODS)),
        ]);

        $employee = EmployeeProfile::findOrFail($validated['employee_id']);
        $verification = $employee->verifications()->where('section', $validated['section'])->first();

        if ($verification) {
            $verification->update([
                'status' => 'verified',
                'verified_by' => $validated['verified_by'] ?? EmployeeVerification::VERIFIED_BY[$validated['section']] ?? 'HR Admin',
                'verification_method' => $validated['verification_method'],
                'verified_at' => now(),
                'expires_at' => now()->addYear(),
                'remarks' => $request->input('remarks', 'Verified successfully'),
            ]);
        }

        return redirect()->back()->with('success', ucfirst($validated['section']) . ' section verified successfully for ' . ($employee->user->name ?? 'employee') . '.');
    }
}
