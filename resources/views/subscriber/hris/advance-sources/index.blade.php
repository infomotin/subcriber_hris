@extends('layouts.subscriber')
@section('title', 'Advance Sources')
@section('content')
<div class="page-title-box d-flex align-items-center justify-content-between mb-4">
    <div><span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">Setup</span><h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;"><i class="bx bx-dollar text-primary me-1.5 align-middle font-size-26"></i>Advance Sources</h4></div>
    <a href="{{ route('subscriber.hris.advance-sources.create') }}" class="btn btn-primary rounded-pill px-4" style="height:40px;font-size:0.85rem;"><i class="bx bx-plus me-1"></i> New Source</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show rounded-pill px-4"><i class="bx bx-check-circle me-1 align-middle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-4">#</th><th>Name</th><th>Code</th><th>Status</th><th class="text-end pe-4">Actions</th></tr></thead>
            <tbody>
                @forelse($sources as $s)
                <tr>
                    <td class="ps-4">{{ $s->id }}</td>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td><code>{{ $s->code }}</code></td>
                    <td>{!! $s->is_active ? '<span class="badge bg-soft-success text-success px-3 py-1.5 font-size-11">Active</span>' : '<span class="badge bg-soft-secondary text-secondary px-3 py-1.5 font-size-11">Inactive</span>' !!}</td>
                    <td class="text-end pe-4"><div class="d-flex justify-content-end gap-1"><a href="{{ route('subscriber.hris.advance-sources.edit', $s) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-11"><i class="bx bx-edit me-0.5"></i> Edit</a><form action="{{ route('subscriber.hris.advance-sources.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill px-3 font-size-11"><i class="bx bx-trash me-0.5"></i></button></form></div></td>
                </tr>
                @empty<tr><td colspan="5" class="text-center py-5 text-muted"><i class="bx bx-dollar font-size-40 d-block mb-2"></i> No advance sources yet.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@if($sources->hasPages())<div class="d-flex justify-content-between align-items-center mt-3"><small class="text-muted">Showing {{ $sources->firstItem() }}-{{ $sources->lastItem() }} of {{ $sources->total() }}</small><div>{{ $sources->links() }}</div></div>@endif
@endsection
