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
                        <span
                            class="badge rounded-pill bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'completed' ? 'success' : 'danger') }}">
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
