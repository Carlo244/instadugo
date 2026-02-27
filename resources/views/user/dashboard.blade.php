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

                <div class="glass-card border-0 shadow-sm">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <button
                                class="nav-link active rounded-pill btn-sm me-2 tab-request d-flex align-items-center gap-2"
                                data-bs-toggle="pill" data-bs-target="#requests">
                                <span class="material-symbols-outlined fs-6">hand_package</span> My Requests
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill btn-sm tab-donation d-flex align-items-center gap-2"
                                data-bs-toggle="pill" data-bs-target="#donations">
                                <span class="material-symbols-outlined fs-6">volunteer_activism</span> My Donations
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="requests">@include('partials.user-requests-table')</div>
                        <div class="tab-pane fade" id="donations">@include('partials.user-donations-table')</div>
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
                        <div class="icon-box bg-danger-subtle text-danger rounded p-2 me-3"><i class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Donations</small>
                            <span
                                class="fw-bold fs-5">{{ \App\Models\Donation::where('user_id', auth()->id())->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning-subtle text-warning rounded p-2 me-3"><i class="bi bi-activity"></i>
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
    <script>
        const sendRequestModal = document.getElementById('sendRequestModal');

        sendRequestModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const donorId = button.getAttribute('data-donor-id');
            const maskId = button.getAttribute('data-mask-id'); // Grab the mask

            const hiddenInput = sendRequestModal.querySelector('#donorId');
            const displaySpan = sendRequestModal.querySelector('#displayDonorId');

            if (hiddenInput) hiddenInput.value = donorId;
            if (displaySpan) displaySpan.textContent = maskId; // Show the mask to the user
        });
    </script>
@endsection
