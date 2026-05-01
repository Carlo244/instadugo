<div class="table-responsive">
    <table class="table request-table align-middle mb-0">
        <thead>
            <tr>
                <th class="ps-3">Requester & Type</th>
                <th>Blood Info</th>
                <th>Target Donor</th> {{-- New Column --}}
                <th class="hide-mobile">Timeline</th>
                <th>Status</th>
                <th class="text-end pe-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                @php
                    $isEmergency = $request->urgency === 'Emergency';
                    // Expanded status colors for the new invitation flow
                    $statusClasses = [
                        'pending' => 'status-pending', // Waiting for admin or donor
                        'accepted' => 'status-approved', // Donor said YES
                        'declined' => 'status-fulfilled', // Re-styled as warning/grey in CSS
                        'fulfilled' => 'status-fulfilled', // Success
                        'cancelled' => 'text-muted',
                    ];
                @endphp
                <tr class="request-row {{ $isEmergency ? 'row-emergency' : '' }}"
                    data-blood-type="{{ $request->blood_type }}">

                    {{-- Requester --}}
                    <td class="ps-3 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="requester-avatar">
                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark lh-1 mb-1">{{ $request->user->name }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($request->receiver_id)
                                        <span class="badge bg-info-subtle text-info border-0 small"
                                            style="font-size: 0.7rem;">DIRECT INVITE</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border-0 small"
                                            style="font-size: 0.7rem;">PUBLIC REQ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Blood Info --}}
                    <td>
                        <span
                            class="badge bg-blood-subtle text-blood border border-blood-subtle rounded-pill px-3 py-1 mb-1 d-inline-block">
                            <i class="bi bi-droplet-fill me-1"></i>{{ $request->blood_type }}
                        </span>
                        <div class="text-muted small ps-1 fw-semibold">{{ $request->quantity }} Units</div>
                    </td>

                    {{-- Target Donor (New!) --}}
                    <td>
                        @if ($request->receiver_id)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-heart text-danger"></i>
                                <span
                                    class="small fw-bold text-dark">{{ $request->receiver?->name ?? 'Deleted User' }}</span>
                            </div>
                        @else
                            <span class="text-muted small italic">Searching...</span>
                        @endif
                    </td>

                    {{-- Timeline --}}
                    <td class="hide-mobile">
                        <div class="fw-semibold text-dark small">
                            {{ \Carbon\Carbon::parse($request->date_needed)->format('M d, Y') }}
                        </div>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="status-badge {{ $statusClasses[$request->status] ?? 'status-default' }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end pe-4">
                        <div class="action-stack">

                            {{-- 1. For Public Requests: Admin must Approve first --}}
                            @if ($request->status == 'pending' && !$request->receiver_id)
                                <form method="POST" action="{{ route('hospital.requests.approve', $request->id) }}"
                                    class="m-0">
                                    @csrf
                                    <button type="submit" class="btn-action success" title="Approve Public Request">
                                        <i class="bi bi-check2-circle"></i><span>Approve</span>
                                    </button>
                                </form>

                                <button class="btn-match" data-bs-toggle="modal"
                                    data-bs-target="#matchModal{{ $request->id }}">
                                    <i class="bi bi-person-plus-fill"></i><span>Match</span>
                                </button>
                            @endif

                            {{-- 2. For Direct Invites: Wait for Donor, then Finalize --}}
                            @if ($request->status == 'accepted')
                                <form method="POST" action="{{ route('hospital.requests.fulfill', $request->id) }}"
                                    class="m-0">
                                    @csrf
                                    <button type="submit" class="btn-action fulfill" title="Mark as Donated">
                                        <i class="bi bi-droplet-fill"></i><span>Finalize</span>
                                    </button>
                                </form>
                            @endif

                            @if (in_array($request->status, ['pending', 'accepted']))
                                <form method="POST" action="{{ route('hospital.requests.decline', $request->id) }}"
                                    class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                        title="Decline Request">
                                        <i class="bi bi-x-circle me-1"></i>Decline
                                    </button>
                                </form>
                            @endif

                            {{-- 3. Always show Details --}}
                            <button class="btn-action info" data-bs-toggle="modal"
                                data-bs-target="#viewRequestModal{{ $request->id }}">
                                <i class="bi bi-eye"></i><span>Details</span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                {{-- ... empty state stays the same ... --}}
            @endforelse
        </tbody>
    </table>
</div>

@push('modals')

    {{-- ══ MATCH / DONOR MODAL ══ --}}
    @foreach ($requests as $request)
        <div class="modal fade" id="matchModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="modal-header border-0 text-white p-4"
                        style="background: linear-gradient(135deg, #e63946, #9b1c1c);">
                        <div class="d-flex align-items-center">
                            <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center me-3 shadow fw-bold"
                                style="width: 45px; height: 45px; font-size: 1.1rem;">
                                {{ $request->blood_type }}
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Compatibility Match</h5>
                                <small class="opacity-75">Finding donors for Request #{{ $request->id }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 bg-light" style="max-height: 65vh; overflow-y: auto;">
                        @php
                            $matchedDonors = $request->matchedDonors ?? collect();
                            $eligibleDonors = $matchedDonors->filter(fn($d) => $d->isEligible());
                        @endphp

                        @if ($matchedDonors->count())
                            <div class="row g-3">
                                @foreach ($matchedDonors as $donor)
                                    @php
                                        $isEligible = $donor->isEligible();
                                        $lastDonation = $donor
                                            ->donations()
                                            ->where('status', 'completed')
                                            ->latest('donation_date')
                                            ->first();
                                        $daysAgo = $lastDonation
                                            ? now()->diffInDays($lastDonation->donation_date)
                                            : null;
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm p-3 h-100 rounded-4 {{ !$isEligible ? 'opacity-75' : '' }}"
                                            style="border-left: 5px solid {{ $isEligible ? '#e63946' : '#6c757d' }} !important; background: white;">
                                            <div class="d-flex flex-column h-100">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div
                                                        class="rounded-circle {{ $isEligible ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }} p-3 me-3">
                                                        <i
                                                            class="bi bi-person-{{ $isEligible ? 'check' : 'clock' }} fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 fw-bold">{{ $donor->name }}</h6>
                                                        <small class="text-muted fw-medium">{{ $donor->blood_type }} |
                                                            {{ $donor->contact }}</small>
                                                    </div>
                                                </div>
                                                <div class="mt-auto">
                                                    <div class="mb-3">
                                                        @if ($isEligible)
                                                            <span
                                                                class="badge bg-success-subtle text-success rounded-pill px-3">
                                                                <i class="bi bi-check-circle-fill me-1"></i> Ready to Notify
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge bg-warning-subtle text-warning rounded-pill px-3">
                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Waiting
                                                                Period
                                                            </span>
                                                        @endif
                                                        <div class="mt-2 small text-muted">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            Last Donation:
                                                            {{ $daysAgo !== null ? "$daysAgo days ago" : 'Never' }}
                                                        </div>
                                                    </div>
                                                    @if ($isEligible)
                                                        <form method="POST"
                                                            action="{{ route('hospital.request.notify', ['donor' => $donor->id, 'request' => $request->id]) }}">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-danger w-100 rounded-pill shadow-sm py-2 fw-bold">
                                                                <i class="bi bi-bell-fill me-1"></i> Notify Donor
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-secondary w-100 rounded-pill py-2 fw-bold"
                                                            disabled>
                                                            <i class="bi bi-slash-circle me-1"></i> Temporarily Ineligible
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted opacity-25">
                                    <i class="bi bi-person-x" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="fw-bold">No Matches Found</h5>
                                <p class="text-muted small px-5">Currently, no donors in your region match blood type
                                    <strong>{{ $request->blood_type }}</strong> and are eligible to donate.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-0 bg-light p-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Close</button>
                        @if ($eligibleDonors->count())
                            <form method="POST" action="{{ route('hospital.request.bulk', $request->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow">
                                    <i class="bi bi-broadcast me-1"></i> Broadcast to All Eligible
                                    ({{ $eligibleDonors->count() }})
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    {{-- ══ FULL REQUEST DETAILS MODAL ══ --}}
    @foreach ($requests as $request)
        <div class="modal fade" id="viewRequestModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Request Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="text-muted small text-uppercase fw-bold">Patient / Requester</label>
                                <p class="fw-bold mb-0 text-dark">{{ $request->user->name }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <label class="text-muted small text-uppercase fw-bold">Blood Type</label>
                                <div>
                                    <span class="badge bg-danger px-3 py-2 fs-6">{{ $request->blood_type }}</span>
                                </div>
                            </div>
                            <hr class="my-2 opacity-50">
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold">Urgency Level</label>
                                @php
                                    $urgencyColors = [
                                        'Normal' => 'text-primary',
                                        'High' => 'text-warning',
                                        'Emergency' => 'text-danger',
                                    ];
                                @endphp
                                <div>
                                    <span class="fw-bold {{ $urgencyColors[$request->urgency] ?? 'text-dark' }}">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $request->urgency }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 bg-light p-3 rounded-3 border">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-2">Message /
                                    Reason</label>
                                <p class="mb-0 text-dark" style="line-height: 1.6;">
                                    {{ $request->reason ?? 'No additional details provided.' }}
                                </p>
                            </div>

                            @if (in_array($request->status, ['pending', 'accepted']))
                                <div class="col-12 bg-white p-3 rounded-3 border">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-2">Update
                                        Priority</label>
                                    <form method="POST" action="{{ route('hospital.requests.priority', $request->id) }}"
                                        class="d-flex align-items-center gap-2 flex-wrap">
                                        @csrf
                                        @method('PATCH')
                                        <select name="urgency" class="form-select" style="max-width: 220px;"
                                            aria-label="Update priority for request {{ $request->id }}">
                                            <option value="Emergency" @selected($request->urgency === 'Emergency')>Emergency</option>
                                            <option value="High" @selected($request->urgency === 'High')>High</option>
                                            <option value="Normal" @selected($request->urgency === 'Normal')>Normal</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4"
                                            title="Update Priority">
                                            Set Priority
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endpush
