@extends('layouts.subscriber')

@section('title', 'Biometric Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-group text-primary me-2"></i> Biometric User Roster</h4>
        <p class="text-muted font-size-13 mb-0">Users synced from ZKTeco biometric machines.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary font-size-12 p-2">
            Total: {{ number_format($users->total()) }} Users
        </span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="max-width: 180px;">
                <span class="input-group-text bg-light border-end-0 py-1"><i class="bx bx-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Search PIN or name..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bx bx-search me-1"></i> Search</button>
            @if(request()->has('search'))
                <a href="{{ route('subscriber.users.index') }}" class="btn btn-sm btn-outline-secondary px-2"><i class="bx bx-x"></i> Clear</a>
            @endif
            <span class="text-muted font-size-11 ms-auto">Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }}</span>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:35px">#</th>
                        <th>PIN</th>
                        <th>User Name</th>
                        <th>Card Number</th>
                        <th>Privilege</th>
                        <th>Machine Source</th>
                        <th>Sync</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $loop->index }}</td>
                            <td><span class="fw-semibold text-primary">{{ $user->pin }}</span></td>
                            <td>{{ $user->name }}</td>
                            <td><code>{{ $user->card_number ?? 'N/A' }}</code></td>
                            <td><span class="badge bg-secondary">{{ $user->privilege_label }}</span></td>
                            <td><code>{{ $user->device->serial_number ?? 'N/A' }}</code></td>
                            <td><span class="badge bg-success"><i class="bx bx-check me-1"></i>Synced</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bx bx-info-circle me-1"></i> No biometric users pushed from your machines yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
