@extends('layouts.subscriber')

@section('title', 'Biometric Users')

@section('content')
<div class="page-title-box">
    <h4>Biometric User Roster</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>PIN</th>
                        <th>User Name</th>
                        <th>Card Number</th>
                        <th>Privilege</th>
                        <th>Machine Source</th>
                        <th>Sync Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $user->pin }}</span></td>
                            <td><strong class="text-dark">{{ $user->name }}</strong></td>
                            <td><code>{{ $user->card_number ?? 'N/A' }}</code></td>
                            <td><span class="badge bg-secondary">{{ $user->privilege_label }}</span></td>
                            <td><code>{{ $user->device->serial_number ?? 'N/A' }}</code></td>
                            <td><span class="badge bg-success">Synced</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No biometric users pushed from your machines yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
