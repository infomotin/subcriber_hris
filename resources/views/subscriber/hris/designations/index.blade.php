@extends('layouts.subscriber')

@section('title', 'Designations')

@section('content')
<div class="page-title-box mb-3">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HR Directory</span>
        <h4 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;">
            <i class="bx bx-user-voice text-primary me-2 align-middle"></i>Designation Setup
        </h4>
    </div>
    <div class="page-title-right">
        <a href="{{ route('subscriber.hris.designations.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bx bx-plus me-1"></i> Add Designation
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-size-12">
                    <i class="bx bx-layer me-1"></i> {{ $designations->total() }} Total
                </span>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 font-size-13" placeholder="Search designations..." value="{{ request('search') }}">
                </div>
                @if(request('search'))
                    <a href="{{ route('subscriber.hris.designations.index') }}" class="btn btn-sm btn-light border text-muted px-2"><i class="bx bx-x"></i></a>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="font-size-12 text-uppercase text-muted fw-semibold letter-spacing-05">
                    <th class="ps-3 py-2" style="width: 40px;">#</th>
                    <th class="py-2">Designation Title</th>
                    <th class="py-2">Grade / Payscale</th>
                    <th class="py-2 text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($designations as $desig)
                    <tr>
                        <td class="ps-3 text-muted font-size-12">{{ ($designations->currentPage() - 1) * $designations->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-indigo-100 text-indigo-700 rounded-circle d-flex align-items-center justify-content-center font-size-13 fw-bold" style="width: 32px; height: 32px; min-width: 32px;">
                                    <i class="bx bx-badge text-indigo-600 font-size-14"></i>
                                </div>
                                <span class="fw-semibold text-dark">{{ $desig->title }}</span>
                            </div>
                        </td>
                        <td>
                            @if($desig->grade)
                                <code class="bg-light px-2 py-1 rounded font-size-12 text-dark">{{ $desig->grade }}</code>
                            @else
                                <span class="text-muted font-size-12">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('subscriber.hris.designations.edit', $desig) }}" class="btn btn-sm btn-light border-0 px-2 py-1" title="Edit">
                                    <i class="bx bx-edit text-primary font-size-14"></i>
                                </a>
                                <form action="{{ route('subscriber.hris.designations.destroy', $desig) }}" method="POST" onsubmit="return confirm('Delete this designation?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0 px-2 py-1" title="Delete">
                                        <i class="bx bx-trash text-danger font-size-14"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bx bx-user-voice text-muted font-size-24"></i>
                                </div>
                                <h6 class="text-muted mb-1 font-size-14">No Designations Found</h6>
                                <p class="text-muted font-size-12 mb-3">Create your first designation to get started.</p>
                                <a href="{{ route('subscriber.hris.designations.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bx bx-plus me-1"></i> Add Designation
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($designations->hasPages())
        <div class="card-footer bg-white border-top py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <span class="font-size-12 text-muted">Showing {{ $designations->firstItem() }}–{{ $designations->lastItem() }} of {{ $designations->total() }}</span>
                {{ $designations->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
