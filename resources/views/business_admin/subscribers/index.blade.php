@extends('layouts.business_admin')

@section('title', 'Subscriber Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bx bx-building text-primary"></i> Subscriber Accounts
        <span class="badge bg-primary rounded-pill font-size-12">{{ $subscribers->total() }}</span>
    </h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" id="btnBulkEmail" style="display:none" onclick="openBulkEmailModal()">
            <i class="bx bx-mail-send me-1"></i> Email Selected (<span id="selectedCount">0</span>)
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSubscriber">
            <i class="bx bx-plus me-1"></i> Register Subscriber
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="form-check-input"></th>
                        <th>Subscriber</th>
                        <th>Plan</th>
                        <th>Device / Employee</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th width="200" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $tenant)
                        <tr>
                            <td><input type="checkbox" class="form-check-input select-subscriber" value="{{ $tenant->id }}" onchange="updateBulkBtn()"></td>
                            <td>
                                <span class="fw-bold text-dark">{{ $tenant->name }}</span>
                                <div class="font-size-11 text-muted">{{ $tenant->user->email ?? 'N/A' }}</div>
                            </td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $tenant->plan->name ?? 'Default' }}</span></td>
                            <td>
                                <span class="badge bg-info me-1"><i class="bx bx-chip me-1"></i>{{ $tenant->devices_count }}/{{ $tenant->max_devices }}</span>
                                <span class="badge bg-secondary"><i class="bx bx-user-plus me-1"></i>{{ $tenant->employees_count }}/{{ $tenant->max_employees }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $tenant->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ strtoupper($tenant->status) }}</span>
                            </td>
                            <td>
                                @if($tenant->expires_at)
                                    <span class="{{ $tenant->expires_at->isPast() ? 'text-danger fw-bold' : '' }}">{{ $tenant->expires_at->format('M d, Y') }}</span>
                                @else
                                    <span class="text-muted">Lifetime</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $tenant->id }}"><i class="bx bx-edit text-primary me-2"></i> Edit</a></li>
                                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $tenant->id }}"><i class="bx bx-money text-success me-2"></i> Record Payment</a></li>
                                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#historyModal{{ $tenant->id }}"><i class="bx bx-history text-info me-2"></i> Payment History</a></li>
                                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#emailModal{{ $tenant->id }}"><i class="bx bx-mail-send text-warning me-2"></i> Send Email</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.business.subscribers.toggle_status', $tenant) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 {{ $tenant->status === 'active' ? 'text-danger' : 'text-success' }}">
                                                    <i class="bx bx-power-off me-2"></i> {{ $tenant->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No subscriber accounts registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">{{ $subscribers->links() }}</div>

@push('modals')
{{-- ====== EDIT, PAYMENT, HISTORY, EMAIL MODALS PER SUBSCRIBER ====== --}}
@foreach($subscribers as $tenant)

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal{{ $tenant->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.subscribers.update', $tenant) }}" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-edit text-primary me-1"></i> Edit Subscriber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Subscriber</label>
                    <input type="text" class="form-control" value="{{ $tenant->name }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13"><i class="bx bx-package text-primary me-1"></i> Subscription Plan</label>
                    <select name="subscription_plan_id" class="form-select" required>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $tenant->subscription_plan_id == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ number_format($plan->price_monthly, 0) }} BDT/mo)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13"><i class="bx bx-calendar text-warning me-1"></i> Subscription End Date</label>
                    <input type="date" name="expires_at" class="form-control" value="{{ $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '' }}">
                    @if($tenant->expires_at && $tenant->expires_at->isPast())
                        <div class="form-text font-size-11 text-danger"><i class="bx bx-error-circle me-1"></i> This subscription has <strong>expired</strong>.</div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13"><i class="bx bx-chip text-info me-1"></i> Max Devices</label>
                        <input type="number" name="max_devices" class="form-control" value="{{ $tenant->max_devices }}" min="1">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13"><i class="bx bx-user-plus text-success me-1"></i> Max Employees</label>
                        <input type="number" name="max_employees" class="form-control" value="{{ $tenant->max_employees }}" min="1">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- RECORD PAYMENT MODAL --}}
<div class="modal fade" id="paymentModal{{ $tenant->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.subscribers.record_payment', $tenant) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-money text-success me-1"></i> Record Manual Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;">
                    <span class="fw-bold">{{ $tenant->name }}</span>
                    <span class="badge bg-soft-primary text-primary ms-2">{{ $tenant->plan->name ?? 'Default' }}</span>
                    <div class="font-size-12 text-muted mt-1">Current expiry: {{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">Amount (BDT)</label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0.01" placeholder="e.g. 3000">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold font-size-13">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any reference notes..."></textarea>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmail{{ $tenant->id }}" checked>
                    <label class="form-check-label fw-medium" for="sendEmail{{ $tenant->id }}">
                        <i class="bx bx-mail-send text-primary me-1"></i> Send payment confirmation email to subscriber
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success px-4"><i class="bx bx-check-circle me-1"></i> Record Payment</button>
            </div>
        </form>
    </div>
</div>

{{-- PAYMENT HISTORY MODAL --}}
<div class="modal fade" id="historyModal{{ $tenant->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-history text-info me-1"></i> Payment History - {{ $tenant->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyBody{{ $tenant->id }}">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted font-size-13">Loading payment history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- SEND EMAIL MODAL --}}
<div class="modal fade" id="emailModal{{ $tenant->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.subscribers.send_email', $tenant) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-mail-send text-warning me-1"></i> Send Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted font-size-13 mb-3">
                    To: <strong>{{ $tenant->name }}</strong> {{ $tenant->user ? '(' . $tenant->user->email . ')' : '' }}
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Subject</label>
                    <input type="text" name="subject" class="form-control" required placeholder="Email subject line...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Message</label>
                    <textarea name="message_body" class="form-control" rows="6" required placeholder="Write your message here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-send me-1"></i> Send Email</button>
            </div>
        </form>
    </div>
</div>

@endforeach

{{-- ====== BULK EMAIL MODAL ====== --}}
<div class="modal fade" id="bulkEmailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.business.subscribers.bulk_email') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-mail-send text-primary me-1"></i> Send Bulk Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tenant_ids" id="bulkTenantIds">
                <div class="p-3 rounded-3 mb-3" style="background:#f0fdf4;">
                    <i class="bx bx-info-circle text-success me-1"></i>
                    Sending email to <strong id="bulkSelectedNames">0</strong> selected subscriber(s).
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Subject</label>
                    <input type="text" name="subject" class="form-control" required placeholder="Bulk email subject...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Message</label>
                    <textarea name="message_body" class="form-control" rows="6" required placeholder="Write your bulk message here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-send me-1"></i> Send to All Selected</button>
            </div>
        </form>
    </div>
</div>

{{-- ====== ADD SUBSCRIBER MODAL ====== --}}
<div class="modal fade" id="modalAddSubscriber" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.business.subscribers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-user-plus text-primary me-1"></i> Register New Subscriber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Subscriber / Company Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Globex Corp">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Login Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@globex.com">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13"><i class="bx bx-package text-primary me-1"></i> Subscription Plan</label>
                    <select name="subscription_plan_id" class="form-select" required>
                        <option value="">-- Select Plan --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->price_monthly, 0) }} BDT/mo)</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check-circle me-1"></i> Create Account</button>
            </div>
        </form>
    </div>
</div>
@endpush

@endsection

@push('scripts')
<script>
// Bulk Email Selection
function toggleSelectAll(source) {
    document.querySelectorAll('.select-subscriber').forEach(cb => cb.checked = source.checked);
    updateBulkBtn();
}

function updateBulkBtn() {
    const checked = document.querySelectorAll('.select-subscriber:checked');
    const btn = document.getElementById('btnBulkEmail');
    const count = document.getElementById('selectedCount');
    count.textContent = checked.length;
    btn.style.display = checked.length > 0 ? 'inline-block' : 'none';
}

function openBulkEmailModal() {
    const checked = document.querySelectorAll('.select-subscriber:checked');
    const ids = Array.from(checked).map(cb => cb.value).join(',');
    document.getElementById('bulkTenantIds').value = ids;
    document.getElementById('bulkSelectedNames').textContent = checked.length;
    new bootstrap.Modal(document.getElementById('bulkEmailModal')).show();
}

// Payment History Load
@foreach($subscribers as $tenant)
document.getElementById('historyModal{{ $tenant->id }}').addEventListener('show.bs.modal', function() {
    const body = document.getElementById('historyBody{{ $tenant->id }}');
    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted font-size-13">Loading...</p></div>';

    fetch('{{ route('admin.business.subscribers.payments', $tenant) }}')
        .then(r => r.json())
        .then(data => {
            if (data.payments.length === 0) {
                body.innerHTML = '<div class="text-center py-4 text-muted"><i class="bx bx-info-circle font-size-24 d-block mb-2"></i> No payments recorded yet.</div>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead><tr><th>Transaction ID</th><th>Amount</th><th>Method</th><th>Plan</th><th>Status</th><th>Date</th></tr></thead><tbody>';
            data.payments.forEach(p => {
                const statusBadge = p.status === 'success' ? 'bg-success' : 'bg-danger';
                html += `<tr>
                    <td><code>${p.tran_id}</code></td>
                    <td class="fw-bold">${p.amount} ${p.currency}</td>
                    <td>${p.method}</td>
                    <td>${p.plan}</td>
                    <td><span class="badge ${statusBadge}">${p.status.toUpperCase()}</span></td>
                    <td class="font-size-12">${p.date}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            body.innerHTML = html;
        })
        .catch(() => {
            body.innerHTML = '<div class="text-center py-4 text-danger"><i class="bx bx-error-circle font-size-24 d-block mb-2"></i> Failed to load payment history.</div>';
        });
});
@endforeach
</script>
@endpush
