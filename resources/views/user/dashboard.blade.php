@extends('layouts.user')

@section('content')
    <main class="content-area">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-4">
            <div>
                <h3 class="fw-bold mb-0 text-uppercase tracking-wide">Dashboard</h3>
                <p class="text-muted small">Welcome back, {{ explode(' ', auth()->user()->name)[0] }} •
                    {{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('user.blood-requests') }}"
                    class="btn btn-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                    style="width: 45px; height: 45px;" title="Request Blood" data-bs-toggle="tooltip">
                    <i class="bi bi-droplet-half fs-5"></i>
                </a>
                <a href="{{ route('user.donate-schedule') }}"
                    class="btn btn-dark rounded-pill px-4 shadow-sm d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-heart-pulse-fill me-2"></i>
                    <span>Schedule</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card mb-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Compatible Donors</h5>
                        <span class="badge rounded-pill bg-light text-dark border">Matching:
                            {{ auth()->user()->blood_type }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Donor Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compatibleDonors as $donor)
                                    <tr>
                                        <td class="fw-semibold">{{ $donor->name }}</td>
                                        <td><span class="text-danger fw-bold">{{ $donor->blood_type }}</span></td>
                                        <td>
                                            <span
                                                class="badge rounded-pill bg-{{ $donor->isEligible() ? 'success' : 'danger' }}-subtle text-{{ $donor->isEligible() ? 'success' : 'danger' }}">
                                                {{ $donor->isEligible() ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Notify</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No compatible donors
                                            found in your area.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card border-0 shadow-sm">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill btn-sm me-2" data-bs-toggle="pill"
                                data-bs-target="#requests">My Requests</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill btn-sm" data-bs-toggle="pill"
                                data-bs-target="#donations">My Donations</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="requests">
                            @include('partials.user-requests-table')
                        </div>
                        <div class="tab-pane fade" id="donations">
                            @include('partials.user-donations-table')
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card profile-status-card text-center mb-4 border-0 shadow-sm pt-4">
                    <div class="profile-avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                        style="width:80px;height:80px; font-size: 1.5rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <h5 class="mb-0 fw-bold">{{ auth()->user()->name }}</h5>
                    <p class="small opacity-75">{{ auth()->user()->email }}</p>

                    <div class="row g-0 border-top border-primary-subtle mt-3 bg-white text-dark rounded-bottom">
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

                <div class="glass-card mb-4 border-0 shadow-sm">
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
@endsection
