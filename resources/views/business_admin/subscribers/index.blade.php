@extends('layouts.business_admin')

@section('title', 'Subscriber Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Subscriber Accounts</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSubscriber">
        <i class="bx bx-plus me-1"></i> Register Subscriber
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Subscriber Name</th>
                        <th>Email Account</th>
                        <th>Plan</th>
                        <th>ADMS Endpoint Token</th>
                        <th>Machine Limit</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $tenant)
                        <tr>
                            <td><span class="fw-bold text-dark">{{ $tenant->name }}</span></td>
                            <td>{{ $tenant->user->email ?? 'N/A' }}</td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $tenant->plan->name ?? 'Default' }}</span></td>
                            <td><code>{{ $tenant->tenant_token }}</code></td>
                            <td><span class="badge bg-info">{{ $tenant->devices()->count() }} / {{ $tenant->max_devices }}</span></td>
                            <td>
                                <span class="badge {{ $tenant->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ strtoupper($tenant->status) }}
                                </span>
                            </td>
                            <td>{{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'Lifetime' }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.business.subscribers.toggle_status', $tenant) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light" title="Toggle Activation">
                                        <i class="bx bx-power-off {{ $tenant->status === 'active' ? 'text-danger' : 'text-success' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No subscriber accounts registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subscriber Modal -->
<div class="modal fade" id="modalAddSubscriber" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.subscribers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register New Subscriber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Subscriber / Company Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Globex Corp">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Login Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@globex.com">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Assign Subscription Plan</label>
                    <select name="subscription_plan_id" class="form-select" required>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->price_monthly, 2) }} BDT/mo - Max {{ $plan->max_devices }} Machines)</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4">Create Subscriber Account</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
