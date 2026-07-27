@extends('layouts.subscriber')
@section('title', 'Advance Types')
@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span>
        <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;">
            <i class="bx bx-category text-primary me-1.5 align-middle font-size-26"></i>Advance Types
        </h4>
    </div>
    <a href="{{ route('subscriber.hris.advance-types.create') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-plus me-1"></i> New Type</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show rounded-pill px-4"><i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-4">#</th><th>Name</th><th>Code</th><th>Payment Mode</th><th>Status</th><th class="text-end pe-4">Actions</th></tr></thead>
            <tbody>
                @forelse($types as $type)
                <tr>
                    <td class="ps-4">{{ $type->id }}</td>
                    <td class="fw-semibold">{{ $type->name }}</td>
                    <td><code>{{ $type->code }}</code></td>
                    <td><span class="badge bg-soft-{{ $type->payment_mode === 'one_time' ? 'info' : 'warning' }} text-{{ $type->payment_mode === 'one_time' ? 'info' : 'warning' }} px-3 py-1.5 font-size-11">{{ $type->payment_mode === 'one_time' ? 'One Time Paid' : 'Monthly Installment' }}</span></td>
                    <td>{!! $type->is_active ? '<span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">Active</span>' : '<span class="badge bg-soft-secondary text-secondary px-3 py-1.5 font-size-11">Inactive</span>' !!}</td>
                    <td class="text-end pe-4"><div class="d-flex justify-content-end gap-1"><a href="{{ route('subscriber.hris.advance-types.edit', $type) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11"><i class="bx bx-edit me-0.5"></i> Edit</a><form action="{{ route('subscriber.hris.advance-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11"><i class="bx bx-trash me-0.5"></i></button></form></div></td>
                </tr>
                @empty<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bx bx-category font-size-40 d-block mb-2"></i> No advance types yet.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@if($types->hasPages())<div class="d-flex justify-content-between align-items-center mt-3"><small class="text-muted">Showing {{ $types->firstItem() }}-{{ $types->lastItem() }} of {{ $types->total() }}</small><div>{{ $types->links() }}</div></div>@endif
@endsection
