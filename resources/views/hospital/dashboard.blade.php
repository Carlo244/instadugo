@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Hospital Management</h3>
                <p class="text-muted small">Monitor blood requests, donor schedules, and user activity.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-danger px-3 py-2 shadow-sm rounded-pill">
                    <i class="bi bi-calendar-check me-2"></i> {{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3 h-100">
                    <div class="icon-box bg-primary"><i class="bi bi-people"></i></div>
                    <div>
                        <small class="text-muted d-block">Registered Users</small>
                        <h5 class="fw-bold mb-0">{{ $totalUsers }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3 h-100">
                    <div class="icon-box bg-warning text-dark"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <small class="text-muted d-block">Requests Pending Approval</small>
                        <h5 class="fw-bold mb-0">{{ $activeRequests }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3 h-100">
                    <div class="icon-box bg-success"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <small class="text-muted d-block">Requests Fulfilled</small>
                        <h5 class="fw-bold mb-0">{{ $matchesCompleted }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3 h-100">
                    <div class="icon-box bg-danger"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <small class="text-muted d-block">Today’s Appointments</small>
                        <h5 class="fw-bold mb-0">{{ $totalDonations }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="glass-card mb-4">
            <ul class="nav nav-pills mb-4 gap-2 p-1 bg-light rounded-pill d-inline-flex shadow-sm" id="pills-tab"
                role="tablist">
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

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="tab-requests">
                    <div class="glass-card border-0 shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-droplet-fill text-danger me-2"></i>Top 5 Urgent
                                Requests
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-danger rounded-pill px-3">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Urgency</th>
                                        <th>Needed</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($queueRequests->take(5) as $request)
                                        <tr>
                                            <td>{{ $request->created_at->format('M d') }}</td>
                                            <td class="fw-bold text-danger">{{ $request->blood_type }}</td>
                                            <td>{{ $request->quantity }} units</td>
                                            <td>
                                                <span
                                                    class="badge {{ $request->urgency == 'Emergency' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ $request->urgency }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($request->date_needed)->format('M d') }}</td>
                                            <td><span class="badge bg-info">{{ ucfirst($request->status) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No active requests</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="glass-card border-0 shadow-sm opacity-90">
                        <h5 class="fw-bold mb-3 text-success"><i class="bi bi-check-circle-fill me-2"></i>Recently Fulfilled
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Blood Type</th>
                                        <th>Qty</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fulfilledRequests->take(5) as $request)
                                        <tr>
                                            <td>#{{ $request->id }}</td>
                                            <td class="fw-semibold">{{ $request->blood_type }}</td>
                                            <td>{{ $request->quantity }}</td>
                                            <td><small>{{ $request->urgency }}</small></td>
                                            <td><span class="badge bg-success">Fulfilled</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-appointments">
                    <div class="glass-card border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>Today's Schedules
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Donor</th>
                                        <th>Type</th>
                                        <th>Center</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules->take(10) as $schedule)
                                        <tr>
                                            <td>{{ $schedule->donor_name }}</td>
                                            <td class="fw-bold">{{ $schedule->blood_type }}</td>
                                            <td>{{ $schedule->hospital }}</td>
                                            <td>{{ $schedule->schedule_date }}</td>
                                            <td><span class="badge bg-primary">{{ ucfirst($schedule->status) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No appointments scheduled</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-users">
                    <div class="glass-card border-0 shadow-sm">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>New Registrations</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Blood Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users->take(10) as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td><span class="badge bg-light text-dark">{{ ucfirst($user->role) }}</span>
                                            </td>
                                            <td class="fw-bold text-danger">{{ $user->blood_type }}</td>
                                            <td><span class="text-success small"><i class="bi bi-circle-fill me-1"
                                                        style="font-size: 8px;"></i> Active</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- NOTIFICATIONS -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Recent Notifications</h5>
            <ul class="list-group list-group-flush">
                @forelse($notifications->take(5) as $note)
                    <li class="list-group-item">🔔 {{ $note->message }}</li>
                @empty
                    <li class="list-group-item text-muted">No notifications</li>
                @endforelse
            </ul>
            <div class="text-end mt-2">
                <a href="" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </div>

    </main>
@endsection
