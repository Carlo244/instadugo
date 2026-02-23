<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Date/Time</th>
                <th>Donor Name</th>
                <th>Blood Type</th>
                <th>Status</th>
                @if ($showActions)
                    <th class="text-end">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr>
                    <td>
                        <span
                            class="fw-bold text-primary">{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d') }}</span><br>
                        <small
                            class="text-muted">{{ \Carbon\Carbon::parse($donation->donation_time)->format('h:i A') }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;">
                                <i class="bi bi-person text-secondary"></i>
                            </div>
                            <span>{{ $donation->user->name }}</span>
                        </div>
                    </td>
                    <td><span
                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-3">{{ $donation->blood_type }}</span>
                    </td>
                    <td>
                        <span
                            class="badge rounded-pill @if ($donation->status == 'scheduled') bg-info @elseif($donation->status == 'completed') bg-success @else bg-secondary @endif text-dark">
                            {{ ucfirst($donation->status) }}
                        </span>
                    </td>
                    @if ($showActions)
                        <td class="text-end">
                            <div class="btn-group">
                                <form method="POST" action="{{ route('hospital.donations.complete', $donation->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">Mark
                                        Done</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
