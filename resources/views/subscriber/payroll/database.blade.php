@extends('layouts.subscriber')

@section('title', 'Salary Structures - Payroll')

@section('content')
<style>
    .card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
</style>

<div class="page-title-box mb-4">
    <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Payroll / Databases</span>
    <h4 class="fw-bold" style="font-family: 'Poppins', sans-serif; color: #0f172a;">Salary Structures</h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Total Employees</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $stats['total'] }}</h3>
                </div>
                <div class="stat-icon bg-indigo-50 border border-indigo-100 text-indigo-600"><i class="bx bx-user"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">With Salary</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $stats['withSalary'] }}</h3>
                </div>
                <div class="stat-icon bg-green-50 border border-green-100 text-green-600"><i class="bx bx-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Without Salary</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ $stats['withoutSalary'] }}</h3>
                </div>
                <div class="stat-icon bg-amber-50 border border-amber-100 text-amber-600"><i class="bx bx-error"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase font-size-11 fw-bold">Total Payroll</span>
                    <h3 class="mt-2 mb-0 fw-bold">{{ number_format($stats['totalPayroll'], 0) }}</h3>
                </div>
                <div class="stat-icon bg-cyan-50 border border-cyan-100 text-cyan-600"><i class="bx bx-money"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4">Employee Salary Structures</h5>
        @if($employees->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted font-size-12 text-uppercase">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Basic</th>
                            <th>House Rent</th>
                            <th>Medical</th>
                            <th>Conveyance</th>
                            <th>Gross</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                            <tr>
                                <td class="fw-semibold">{{ $emp->user?->name ?? $emp->employee_id }}</td>
                                <td>{{ $emp->department->name ?? '—' }}</td>
                                @if($emp->salaryStructure)
                                    <td>{{ number_format($emp->salaryStructure->basic_salary, 0) }}</td>
                                    <td>{{ number_format($emp->salaryStructure->house_rent, 0) }}</td>
                                    <td>{{ number_format($emp->salaryStructure->medical_allowance, 0) }}</td>
                                    <td>{{ number_format($emp->salaryStructure->conveyance_allowance, 0) }}</td>
                                    <td class="fw-bold">
                                        {{ number_format($emp->salaryStructure->basic_salary + $emp->salaryStructure->house_rent + $emp->salaryStructure->medical_allowance + $emp->salaryStructure->conveyance_allowance + $emp->salaryStructure->other_allowances, 0) }}
                                    </td>
                                @else
                                    <td colspan="5"><span class="text-muted">No salary structure</span></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $employees->links() }}</div>
        @else
            <div class="text-center py-5">
                <i class="bx bx-coin-stack text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No employees found. Add employees first in HRIS.</p>
            </div>
        @endif
    </div>
</div>
@endsection