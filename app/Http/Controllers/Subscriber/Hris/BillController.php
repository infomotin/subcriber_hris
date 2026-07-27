<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillType;
use App\Models\BillPurpose;
use App\Models\BillModification;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['employee.user', 'billType', 'billPurpose', 'actionedBy']);

        $status = $request->input('status', 'all');
        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($status === 'modified') {
            $query->where('status', 'modified');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bill_no', 'like', "%{$search}%")
                  ->orWhereHas('employee', fn($eq) => $eq->where('employee_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")));
            });
        }

        $bills = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('subscriber.hris.bills.index', compact('bills', 'status'));
    }

    public function apply()
    {
        $employees = EmployeeProfile::with(['user', 'department', 'designation', 'salaryStructure'])
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

        $billTypes = BillType::where('is_active', true)->orderBy('name')->get();
        $billPurposes = BillPurpose::where('is_active', true)->orderBy('name')->get();

        return view('subscriber.hris.bills.apply', compact('employees', 'billTypes', 'billPurposes'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'bill_purpose_id' => 'required|exists:bill_purposes,id',
            'amount' => 'required|numeric|min:0.01',
            'bill_no' => 'nullable|string|max:50',
            'voucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = $tenant->id;
        unset($validated['voucher']);

        if ($request->hasFile('voucher')) {
            $validated['voucher_path'] = $request->file('voucher')->store('bills/vouchers', 'public');
        }

        Bill::create($validated);

        return redirect()->route('subscriber.hris.bills.index')
            ->with('success', 'Bill submitted successfully. Awaiting approval.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['employee.user', 'employee.department', 'employee.designation', 'billType', 'billPurpose', 'actionedBy', 'modifications.modifier']);
        return view('subscriber.hris.bills.show', compact('bill'));
    }

    public function employeeInfo(Request $request)
    {
        $profile = EmployeeProfile::with(['user', 'department', 'designation'])
            ->findOrFail($request->employee_profile_id);

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
        ]);
    }

    public function approval()
    {
        $bills = Bill::with(['employee.user', 'employee.department', 'billType', 'billPurpose'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(fn($b) => $b->employee->department?->name ?? 'Unassigned');

        return view('subscriber.hris.bills.approval', compact('bills'));
    }

    public function approve(Request $request, Bill $bill)
    {
        $bill->update([
            'status' => 'approved',
            'approved_amount' => $bill->amount,
            'actioned_by' => auth()->id(),
            'action_remarks' => $request->input('action_remarks', 'Approved'),
        ]);

        return redirect()->back()->with('success', 'Bill approved successfully.');
    }

    public function reject(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'action_remarks' => 'required|string|max:500',
        ]);

        $bill->update([
            'status' => 'rejected',
            'actioned_by' => auth()->id(),
            'action_remarks' => $validated['action_remarks'],
        ]);

        return redirect()->back()->with('success', 'Bill rejected.');
    }

    public function modify(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'new_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        BillModification::create([
            'bill_id' => $bill->id,
            'original_amount' => $bill->approved_amount ?? $bill->amount,
            'new_amount' => $validated['new_amount'],
            'reason' => $validated['reason'],
            'modified_by' => auth()->id(),
        ]);

        $bill->update([
            'status' => 'modified',
            'approved_amount' => $validated['new_amount'],
            'actioned_by' => auth()->id(),
            'action_remarks' => 'Amount modified: ' . $validated['reason'],
        ]);

        return redirect()->back()->with('success', 'Bill amount modified and approved.');
    }

    public function pdf(Bill $bill)
    {
        $bill->load(['employee.user', 'employee.department', 'employee.designation', 'billType', 'billPurpose', 'actionedBy', 'modifications.modifier']);

        $pdf = Pdf::loadView('subscriber.hris.bills.pdf', compact('bill'))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('bill-' . $bill->id . '.pdf');
    }
}
