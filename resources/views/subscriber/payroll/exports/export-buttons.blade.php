@php
    $exportTabs = [
        'overview' => 'Visual Overview',
        'employee' => 'Employee Report',
        'department' => 'Department Report',
        'punch' => 'Punch Report',
        'leave' => 'Leave Report',
        'timecard' => 'Time Card',
        'salary' => 'Salary Sheet',
        'advance' => 'Advance Report',
    ];
@endphp
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('subscriber.payroll.report.export', ['type' => $currentTab ?? $tab]) }}?month={{ $month }}&department_id={{ $departmentId }}&employee_profile_id={{ $employeeId }}&format=pdf&tab={{ $tab }}"
       target="_blank" class="btn btn-outline-danger btn-sm">
        <i class="bx bx-file me-1"></i> PDF
    </a>
    <a href="{{ route('subscriber.payroll.report.export', ['type' => $currentTab ?? $tab]) }}?month={{ $month }}&department_id={{ $departmentId }}&employee_profile_id={{ $employeeId }}&format=csv&tab={{ $tab }}"
       class="btn btn-outline-success btn-sm">
        <i class="bx bx-table me-1"></i> Excel
    </a>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        <i class="bx bx-printer me-1"></i> Print
    </button>
</div>
