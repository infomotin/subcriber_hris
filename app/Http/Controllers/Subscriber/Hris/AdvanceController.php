<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use App\Models\AdvanceType;
use App\Models\AdvanceSource;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use App\Models\TenantConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Advance::with(['employee.user', 'advanceType', 'advanceSource', 'actionedBy']);

        $status = $request->input('status', 'all');
        if ($status === 'pending') $query->where('status', 'pending');
        elseif ($status === 'approved') $query->where('status', 'approved');
        elseif ($status === 'rejected') $query->where('status', 'rejected');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', fn($eq) => $eq->where('employee_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")));
            });
        }

        $advances = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        return view('subscriber.hris.advances.index', compact('advances', 'status'));
    }

    public function apply()
    {
        $employees = EmployeeProfile::with(['user', 'department', 'designation'])
            ->where('status', 'active')
            ->orderBy('id')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->user?->name ?? 'N/A',
                'emp_id' => $e->employee_id,
                'department' => $e->department?->name ?? 'N/A',
                'designation' => $e->designation?->title ?? 'N/A',
                'joining_date' => $e->joining_date,
                'status' => $e->status,
            ]);

        $advanceTypes = AdvanceType::where('is_active', true)->orderBy('name')->get();
        $advanceSources = AdvanceSource::where('is_active', true)->orderBy('name')->get();

        return view('subscriber.hris.advances.apply', compact('employees', 'advanceTypes', 'advanceSources'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'advance_type_id' => 'required|exists:advance_types,id',
            'advance_source_id' => 'required|exists:advance_sources,id',
            'reference_employee_id' => 'nullable|exists:employee_profiles,id',
            'amount' => 'required|numeric|min:1',
            'installments' => 'required|integer|min:1|max:60',
            'reason' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = $tenant->id;

        // Calculate monthly deduction
        $type = AdvanceType::find($validated['advance_type_id']);
        if ($type->payment_mode === 'monthly_installment' && $validated['installments'] > 1) {
            $validated['monthly_deduction'] = round($validated['amount'] / $validated['installments'], 2);
        } else {
            $validated['installments'] = 1;
            $validated['monthly_deduction'] = $validated['amount'];
        }

        $advance = Advance::create($validated);

        // Auto-send email to employee
        $this->sendSubmissionMail($advance);

        return redirect()->route('subscriber.hris.advances.index')
            ->with('success', 'Advance application submitted. A confirmation email has been sent to the employee.');
    }

    public function show(Advance $advance)
    {
        $advance->load(['employee.user', 'employee.department', 'employee.designation', 'advanceType', 'advanceSource', 'referenceEmployee.user', 'actionedBy']);
        return view('subscriber.hris.advances.show', compact('advance'));
    }

    public function employeeInfo(Request $request)
    {
        $profile = EmployeeProfile::with(['user', 'department', 'designation'])
            ->findOrFail($request->employee_profile_id);

        $existingAdvances = Advance::where('employee_profile_id', $profile->id)
            ->where('status', 'approved')
            ->get()
            ->map(fn($a) => [
                'type' => $a->advanceType?->name,
                'amount' => $a->amount,
                'approved_amount' => $a->approved_amount,
                'installments' => $a->installments,
                'monthly_deduction' => $a->monthly_deduction,
                'date' => $a->created_at->format('d M, Y'),
            ]);

        return response()->json([
            'name' => $profile->user?->name,
            'employee_id' => $profile->employee_id,
            'department' => $profile->department?->name,
            'designation' => $profile->designation?->title,
            'joining_date' => $profile->joining_date,
            'phone' => $profile->phone,
            'blood_group' => $profile->blood_group,
            'status' => $profile->status,
            'photo_url' => $profile->photo ? Storage::disk('public')->url($profile->photo) : null,
            'existing_advances' => $existingAdvances,
        ]);
    }

    public function approval()
    {
        $advances = Advance::with(['employee.user', 'employee.department', 'advanceType', 'advanceSource', 'referenceEmployee.user'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(fn($a) => $a->employee->department?->name ?? 'Unassigned');

        return view('subscriber.hris.advances.approval', compact('advances'));
    }

    public function approve(Request $request, Advance $advance)
    {
        $validated = $request->validate([
            'action_remarks' => 'nullable|string|max:500',
        ]);

        $advance->update([
            'status' => 'approved',
            'approved_amount' => $advance->amount,
            'actioned_by' => auth()->id(),
            'action_remarks' => $validated['action_remarks'] ?? 'Approved',
        ]);

        $this->sendApprovalMail($advance, 'approved');

        return redirect()->back()->with('success', 'Advance approved. Confirmation email sent to employee.');
    }

    public function reject(Request $request, Advance $advance)
    {
        $validated = $request->validate([
            'action_remarks' => 'required|string|max:500',
        ]);

        $advance->update([
            'status' => 'rejected',
            'actioned_by' => auth()->id(),
            'action_remarks' => $validated['action_remarks'],
        ]);

        $this->sendApprovalMail($advance, 'rejected');

        return redirect()->back()->with('success', 'Advance rejected. Notification email sent to employee.');
    }

    private function sendSubmissionMail(Advance $advance)
    {
        try {
            $employee = $advance->employee;
            if (!$employee || !$employee->user) return;

            $config = TenantConfig::getGroup('mail');
            if (empty($config)) return;

            config([
                'mail.mailers.smtp.host' => $config['mail_host'],
                'mail.mailers.smtp.port' => $config['mail_port'],
                'mail.mailers.smtp.username' => $config['mail_username'],
                'mail.mailers.smtp.password' => $config['mail_password'],
                'mail.mailers.smtp.encryption' => $config['mail_encryption'],
                'mail.from.address' => $config['mail_from_address'],
                'mail.from.name' => $config['mail_from_name'],
            ]);

            $tenant = auth()->user()->tenant;
            $subject = "Advance Application Submitted - " . $tenant->name;
            $body = "Dear {$employee->user->name},\n\n";
            $body .= "Your advance application has been submitted successfully.\n\n";
            $body .= "Application Details:\n";
            $body .= "Type: {$advance->advanceType->name}\n";
            $body .= "Amount: " . number_format($advance->amount, 2) . " BDT\n";
            $body .= "Installments: {$advance->installments}\n";
            $body .= "Monthly Deduction: " . number_format($advance->monthly_deduction, 2) . " BDT\n";
            $body .= "Status: Pending Approval\n\n";
            $body .= "You will receive another email once your application is reviewed.\n\n";
            $body .= "Regards,\n{$tenant->name} HR System";

            Mail::raw($body, function ($message) use ($employee, $subject, $config) {
                $message->to($employee->user->email)
                    ->subject($subject)
                    ->from($config['mail_from_address'] ?? 'noreply@example.com', $config['mail_from_name'] ?? config('app.name'));
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send advance submission mail: " . $e->getMessage());
        }
    }

    private function sendApprovalMail(Advance $advance, string $action)
    {
        try {
            $employee = $advance->employee;
            if (!$employee || !$employee->user) return;

            $config = TenantConfig::getGroup('mail');
            if (empty($config)) return;

            config([
                'mail.mailers.smtp.host' => $config['mail_host'],
                'mail.mailers.smtp.port' => $config['mail_port'],
                'mail.mailers.smtp.username' => $config['mail_username'],
                'mail.mailers.smtp.password' => $config['mail_password'],
                'mail.mailers.smtp.encryption' => $config['mail_encryption'],
                'mail.from.address' => $config['mail_from_address'],
                $config['mail_from_name'] => $config['mail_from_name'],
            ]);

            $tenant = auth()->user()->tenant;
            $statusText = ucfirst($action);
            $subject = "Advance Application {$statusText} - " . $tenant->name;

            $body = "Dear {$employee->user->name},\n\n";
            $body .= "Your advance application has been {$action}.\n\n";
            $body .= "Application Details:\n";
            $body .= "Type: {$advance->advanceType->name}\n";
            $body .= "Requested Amount: " . number_format($advance->amount, 2) . " BDT\n";

            if ($action === 'approved') {
                $body .= "Approved Amount: " . number_format($advance->approved_amount, 2) . " BDT\n";
                $body .= "Installments: {$advance->installments}\n";
                $body .= "Monthly Deduction: " . number_format($advance->monthly_deduction, 2) . " BDT\n";
                $body .= "Source: {$advance->advanceSource->name}\n";
            } else {
                $body .= "Reason: {$advance->action_remarks}\n";
            }

            $body .= "\nRegards,\n{$tenant->name} HR System";

            Mail::raw($body, function ($message) use ($employee, $subject, $config) {
                $message->to($employee->user->email)
                    ->subject($subject)
                    ->from($config['mail_from_address'] ?? 'noreply@example.com', $config['mail_from_name'] ?? config('app.name'));
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send advance approval mail: " . $e->getMessage());
        }
    }
}
