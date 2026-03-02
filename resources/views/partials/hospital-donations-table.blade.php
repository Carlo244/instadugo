<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">Donor Details</th>
                <th>Schedule</th>
                <th class="text-center">Blood Type</th>
                <th>Status</th>
                @if ($showActions)
                    <th class="text-end pe-3">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr data-blood-type="{{ $donation->blood_type }}">
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-blood-subtle rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px;">
                                <span
                                    class="text-blood fw-bold small">{{ strtoupper(substr($donation->user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $donation->user->name }}</div>
                                <small class="text-muted"
                                    style="font-size: 0.75rem;">{{ $donation->user->email }}</small>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="fw-semibold text-dark">
                            {{ \Carbon\Carbon::parse($donation->donation_date)->isToday() ? 'Today' : \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}
                        </div>
                        <div class="text-muted small">
                            <i
                                class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($donation->donation_time)->format('h:i A') }}
                        </div>
                    </td>

                    <td class="text-center">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2"
                            style="min-width: 50px;">
                            {{ $donation->blood_type }}
                        </span>
                    </td>

                    <td>
                        @if ($donation->status == 'scheduled')
                            @if ($showActions)
                                <!-- Today: more pronounced -->
                                <span class="badge bg-blood px-3 py-2 rounded-pill text-white">
                                    <i class="bi bi-calendar-event me-1"></i> Scheduled
                                </span>
                            @else
                                <!-- Upcoming: lighter red for subtle visibility -->
                                <span
                                    class="badge bg-blood-subtle text-blood px-3 py-2 rounded-pill border border-blood-subtle">
                                    <i class="bi bi-calendar-event me-1"></i> Scheduled
                                </span>
                            @endif
                        @elseif($donation->status == 'completed')
                            <span
                                class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle">
                                <i class="bi bi-check-circle-fill me-1"></i> Completed
                            </span>
                        @else
                            <span class="badge bg-light text-secondary px-3 py-2 rounded-pill border">
                                {{ ucfirst($donation->status) }}
                            </span>
                        @endif
                    </td>

                    @if ($showActions)
                        <td class="text-end pe-3">
                            @if ($donation->status == 'scheduled')
                                <div class="btn-group gap-2">
                                    <form method="POST"
                                        action="{{ route('hospital.donations.complete', $donation->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-blood rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-check2 me-1"></i> Mark Done
                                        </button>
                                    </form>
                                    <form method="POST"
                                        action="{{ route('hospital.donations.cancel', $donation->id) }}"
                                        onsubmit="return confirm('Cancel this donation schedule?');">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-light text-danger rounded-circle border shadow-sm"
                                            title="Cancel">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">Processed</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showActions ? 5 : 4 }}" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                        No donation records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
