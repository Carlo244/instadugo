<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr class="text-muted small">
                <th>Date</th>
                <th>Blood Type</th>
                <th>Units</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userRequests as $request)
                <tr>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-danger-subtle text-danger">{{ $request->blood_type }}</span></td>
                    <td>{{ $request->quantity }} units</td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-warning text-dark',
                                'accepted' => 'bg-info text-dark',
                                'fulfilled' => 'bg-success',
                                'declined' => 'bg-secondary',
                                'cancelled' => 'bg-danger',
                            ];
                            $badgeClass = $statusClasses[$request->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge rounded-pill {{ $badgeClass }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No blood requests yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
