@extends('layouts.hospital')

@section('content')
    <main class="content-area">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Hospital Management</h3>
                <p class="text-muted small mb-0">Monitor blood requests, donor schedules, and user activity.</p>
            </div>

            <span class="badge bg-danger px-3 py-2 shadow-sm rounded-pill">
                <i class="bi bi-calendar-check me-2"></i> {{ now()->format('M d, Y') }}
            </span>
        </div>

        <!-- STATS -->
        <div class="row g-3 mb-4 stats-row">

            <div class="col-md-3 col-6">
                <div class="glass-card stat-card-users h-100 mb-0">
                    <div class="icon-box"><i class="bi bi-people"></i></div>
                    <div class="text-part">
                        <small class="text-muted">Registered Users</small>
                        <h5 class="fw-bold mb-0">{{ $totalUsers }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="glass-card stat-card-pending h-100 mb-0">
                    <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                    <div class="text-part">
                        <small class="text-muted">Pending Requests</small>
                        <h5 class="fw-bold mb-0">{{ $activeRequests }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="glass-card stat-card-done h-100 mb-0">
                    <div class="icon-box"><i class="bi bi-check2-circle"></i></div>
                    <div class="text-part">
                        <small class="text-muted">Fulfilled Requests</small>
                        <h5 class="fw-bold mb-0">{{ $matchesCompleted }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="glass-card stat-card-appt h-100 mb-0">
                    <div class="icon-box"><i class="bi bi-calendar-check"></i></div>
                    <div class="text-part">
                        <small class="text-muted">Today's Appointments</small>
                        <h5 class="fw-bold mb-0">{{ $totalDonations }}</h5>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABS -->
        <div class="glass-card mb-4">

            {{-- Removed flex-wrap so pills scroll horizontally instead of wrapping on mobile --}}
            <ul class="nav nav-pills mb-4 gap-2 p-1 bg-light rounded-pill shadow-sm" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-requests">
                        <i class="bi bi-exclamation-diamond-fill me-2"></i>Urgent Requests
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-appointments">
                        <i class="bi bi-calendar2-heart-fill me-2"></i>Donations
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-users">
                        <i class="bi bi-people-fill me-2"></i>User Directory
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- URGENT REQUESTS -->
                <div class="tab-pane fade show active" id="tab-requests">

                    <div class="p-0 p-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-droplet-fill text-danger me-2"></i>Urgent Blood Requests
                                </h5>
                                <p class="text-muted small mb-0">High-priority queue based on urgency.</p>
                            </div>
                            <a href="{{ route('hospital.requests') }}"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                View All
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID / Date</th>
                                        <th class="text-center">Type</th>
                                        <th class="hide-mobile">Qty</th>
                                        <th>Urgency</th>
                                        <th class="hide-mobile">Deadline</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($queueRequests->take(5) as $request)
                                        <tr>
                                            <td>
                                                <strong>#{{ $request->id }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger px-3 py-2">{{ $request->blood_type }}</span>
                                            </td>
                                            <td class="hide-mobile">{{ $request->quantity }} Units</td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill
                                                    {{ $request->urgency == 'Emergency' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                                                    {{ $request->urgency }}
                                                </span>
                                            </td>
                                            <td class="hide-mobile">
                                                {{ \Carbon\Carbon::parse($request->date_needed)->format('M d') }}
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-info-subtle text-info rounded-pill">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No active requests.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- DONATIONS -->
                <div class="tab-pane fade" id="tab-appointments">
                    <div class="p-0 p-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-calendar2-check text-primary me-2"></i>Today's Donation Queue
                                </h5>
                                <p class="text-muted small mb-0">Scheduled donors for today.</p>
                            </div>
                            <a href="{{ route('hospital.donations') }}"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                View All
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Donor</th>
                                        <th class="hide-mobile">Schedule</th>
                                        <th class="text-center">Blood Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donations as $donation)
                                        <tr>
                                            <td>
                                                <strong>{{ $donation->user->name }}</strong><br>
                                                <small class="text-muted">{{ $donation->user->email }}</small>
                                            </td>
                                            <td class="hide-mobile">
                                                {{ \Carbon\Carbon::parse($donation->donation_date)->format('M d') }}<br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($donation->donation_time)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger px-3 py-2">{{ $donation->blood_type }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill
                                                    {{ $donation->status == 'completed' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' }}">
                                                    {{ ucfirst($donation->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if ($donation->status == 'scheduled')
                                                    <form method="POST"
                                                        action="{{ route('hospital.donations.complete', $donation->id) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success rounded-pill">Done</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No donations today.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- USERS -->
                <div class="tab-pane fade" id="tab-users">
                    <div class="p-0 p-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-people-fill text-primary me-2"></i>New Users
                                </h5>
                                <p class="text-muted small mb-0">Recently registered members.</p>
                            </div>
                            <a href="{{ route('hospital.manageusers') }}"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                View All
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th class="hide-mobile">Contact</th>
                                        <th class="text-center">Blood Type</th>
                                        <th class="hide-mobile">Status</th>
                                        <th class="text-end">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users->take(10) as $user)
                                        <tr>
                                            <td>
                                                {{ $user->name }}<br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </td>
                                            <td class="hide-mobile">{{ $user->contact ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ $user->blood_type ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td class="hide-mobile">
                                                {{ $user->isEligible() ? 'Eligible' : $user->daysUntilEligible() . ' days' }}
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $user->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No users yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>
@endsection
