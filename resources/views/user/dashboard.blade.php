@extends('layouts.user')

@section('content')
    <main class="content-area">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-4">
            <div>
                <h3 class="fw-bold mb-0 text-uppercase tracking-wide">Dashboard</h3>
                <p class="text-muted small"> {{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="d-flex gap-2 w-100" style="max-width: 400px;">
                <a href="{{ route('user.blood-requests') }}"
                    class="btn btn-danger rounded-pill shadow-sm d-flex flex-fill align-items-center justify-content-center py-2 px-3">
                    <i class="bi bi-droplet-half fs-5 me-2"></i><span>Request</span>
                </a>
                <a href="{{ route('user.donate-schedule') }}"
                    class="btn btn-dark rounded-pill shadow-sm d-flex flex-fill align-items-center justify-content-center py-2 px-3">
                    <i class="bi bi-heart-pulse-fill me-2"></i><span>Schedule</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card mb-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">System-Recommended Compatible Donors</h5>
                        <span class="badge rounded-pill bg-light text-dark border">
                            Matching Blood Type: {{ auth()->user()->blood_type }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Donor ID</th>
                                    <th>Blood Type</th>
                                    <th>Availability</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compatibleDonors as $donor)
                                    <tr>
                                        <td class="fw-semibold">
                                            <span
                                                class="text-muted">#</span>{{ strtoupper($donor->blood_type) }}-{{ str_pad($donor->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td><span class="text-danger fw-bold">{{ $donor->blood_type }}</span></td>
                                        <td>
                                            <span
                                                class="badge rounded-pill bg-{{ $donor->isEligible() ? 'success' : 'danger' }}-subtle text-{{ $donor->isEligible() ? 'success' : 'danger' }}">
                                                {{ $donor->isEligible() ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if ($donor->isEligible())
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    data-bs-toggle="modal" data-bs-target="#sendRequestModal"
                                                    data-donor-id="{{ $donor->id }}"
                                                    data-mask-id="{{ strtoupper($donor->blood_type) }}-{{ str_pad($donor->id, 4, '0', STR_PAD_LEFT) }}">
                                                    Send Request
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary rounded-pill px-3"
                                                    disabled>Unavailable</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No compatible donors
                                            available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card border-0 shadow-sm" id="invitations-section">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill btn-sm me-2 d-flex align-items-center gap-2"
                                data-bs-toggle="pill" data-bs-target="#invitations">
                                <span class="material-symbols-outlined fs-6">mail</span> Invitations
                                {{-- Show a red dot only if there are UNREAD invitations --}}
                                @if ($user->unreadNotifications->where('type', 'App\Notifications\DonorRequestNotification')->count() > 0)
                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.5rem;">&nbsp;</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill btn-sm me-2 d-flex align-items-center gap-2"
                                data-bs-toggle="pill" data-bs-target="#requests">
                                <span class="material-symbols-outlined fs-6">hand_package</span> My Requests
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill btn-sm d-flex align-items-center gap-2"
                                data-bs-toggle="pill" data-bs-target="#donations">
                                <span class="material-symbols-outlined fs-6">volunteer_activism</span> My Donations
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="invitations">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Requester & Hospital</th>
                                            <th>Urgency</th> {{-- Added for clarity --}}
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invitations as $invite)
                                            @php
                                                $hospitalId = optional($invite->hospital)->id;
                                            @endphp
                                            <tr>
                                                <td class="small text-muted">{{ $invite->created_at->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><i class="bi bi-person-slash me-2"></i>Anonymous
                                                        Requester</div>
                                                    <div class="small text-muted"><i class="bi bi-hospital me-1"></i>
                                                        Healthcare Facility</div>
                                                </td>
                                                <td>
                                                    @php
                                                        $urgencyColor =
                                                            [
                                                                'Emergency' => 'danger',
                                                                'High' => 'warning',
                                                                'Normal' => 'info',
                                                            ][$invite->urgency] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $urgencyColor }} text-uppercase"
                                                        style="font-size: 0.65rem;">
                                                        {{ $invite->urgency }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColor =
                                                            [
                                                                'pending' => 'warning',
                                                                'accepted' => 'success',
                                                                'declined' => 'secondary',
                                                            ][$invite->status] ?? 'info';
                                                    @endphp
                                                    <span
                                                        class="badge rounded-pill bg-{{ $statusColor }}-subtle text-{{ $statusColor }} text-uppercase"
                                                        style="font-size: 0.7rem;">
                                                        {{ $invite->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    @if ($invite->status === 'pending')
                                                        <div class="d-flex gap-2 justify-content-end">
                                                            <a href="{{ route('user.requests.show', $invite->id) }}"
                                                                class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                                View
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-success rounded-pill px-3"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#acceptInvitationModal"
                                                                data-request-id="{{ $invite->id }}"
                                                                data-hospital="Healthcare Facility"
                                                                data-hospital-id="{{ $hospitalId }}"
                                                                data-requester="Anonymous Requester">
                                                                Accept
                                                            </button>

                                                            <form
                                                                action="{{ route('user.donate-schedule.respond', $invite->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="action" value="decline">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                                                    onclick="return confirm('Are you sure you want to decline this invitation?')">
                                                                    Decline
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($invite->status === 'accepted')
                                                        <div class="d-flex gap-2 justify-content-end align-items-center">
                                                            <span class="text-success small fw-bold"><i
                                                                    class="bi bi-calendar-check me-1"></i> Scheduled</span>
                                                            <a href="{{ route('user.requests.show', $invite->id) }}"
                                                                class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                                View
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="d-flex gap-2 justify-content-end align-items-center">
                                                            <span class="text-muted small italic">Declined</span>
                                                            <a href="{{ route('user.requests.show', $invite->id) }}"
                                                                class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                                View
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="bi bi-envelope text-muted fs-2 d-block mb-2"></i>
                                                    <span class="text-muted">No invitations yet. Incoming blood
                                                        requests will appear here.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="requests">@include('partials.user-requests-table')</div>
                        <div class="tab-pane fade" id="donations">
                            <div id="dashboard-donations">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Hospital</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($donationHistory as $donation)
                                                <tr>
                                                    <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                                                    <td class="small">{{ $donation->hospitalAdmin->hospital_name }}</td>
                                                    <td>
                                                        <span
                                                            class="badge rounded-pill bg-{{ $donation->status == 'completed' ? 'success' : 'danger' }}">{{ ucfirst($donation->status) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">No completed
                                                        donations yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card profile-status-card profile-card-red text-center mb-4 border-0 shadow-sm pt-4">
                    <div class="profile-avatar bg-white text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                        style="width:80px;height:80px; font-size: 1.5rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <h5 class="mb-0 fw-bold text-white">{{ auth()->user()->name }}</h5>
                    <p class="small text-white opacity-75 mb-4">{{ auth()->user()->email }}</p>

                    <div class="row g-0 border-top bg-white text-dark rounded-bottom overflow-hidden">
                        <div class="col-6 border-end p-3 text-center">
                            <small class="text-muted d-block">Blood Type</small>
                            <span class="fw-bold text-danger fs-5">{{ auth()->user()->blood_type }}</span>
                        </div>
                        <div class="col-6 p-3 text-center">
                            <small class="text-muted d-block">Status</small>
                            <span class="fw-bold text-{{ auth()->user()->isEligible() ? 'success' : 'danger' }}">
                                {{ auth()->user()->isEligible() ? 'Eligible' : 'Resting' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="glass-card border-0 shadow-sm">
                    <h6 class="fw-bold mb-3">Quick Overview</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger-subtle text-danger rounded p-2 me-3"><i
                                class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Donations</small>
                            <span
                                class="fw-bold fs-5">{{ \App\Models\Donation::where('user_id', auth()->id())->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning-subtle text-warning rounded p-2 me-3"><i
                                class="bi bi-activity"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Global Pending Requests</small>
                            <span
                                class="fw-bold fs-5">{{ \App\Models\BloodRequest::where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="sendRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('user.send-donor-request') }}">
                @csrf

                <input type="hidden" name="donor_id" id="donorId">

                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Request to Donor <span id="displayDonorId"
                                class="badge bg-white text-danger ms-2"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preferred Hospital/Center</label>
                            <select name="hospital_admin_id" class="form-select border-danger-subtle" required>
                                <option value="" disabled selected>Select a location</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}">{{ $hospital->hospital_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Where should the donor go?</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Urgency Level</label>
                            <select name="urgency" class="form-select border-danger-subtle" required>
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control border-danger-subtle" rows="3" required>You are compatible and requested to donate blood. Please visit the selected hospital if you are available.</textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Send Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="acceptInvitationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="acceptForm">
                @csrf
                <input type="hidden" name="action" value="accept">

                <input type="hidden" name="hospital_admin_id" id="modal_hospital_id">

                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Accept Invitation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <p>You are accepting an anonymous invitation from <strong>Anonymous Requester</strong> at
                            <strong>Healthcare Facility</strong>.</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">When can you visit?</label>
                            <input type="date" name="donation_date" id="modal_donation_date"
                                class="form-control border-success-subtle" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Available Time Slots</label>
                            <select name="donation_time" id="modal_donation_time"
                                class="form-select border-success-subtle" required>
                                <option value="">Select a date first</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">Confirm Acceptance</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const requestedTab = params.get('tab');

            if (requestedTab === 'invitations') {
                const invitationsTrigger = document.querySelector('[data-bs-target="#invitations"]');
                if (invitationsTrigger && window.bootstrap && bootstrap.Tab) {
                    bootstrap.Tab.getOrCreateInstance(invitationsTrigger).show();
                }

                const section = document.getElementById('invitations-section');
                if (section) {
                    section.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    </script>
@endsection
