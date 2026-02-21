@extends('layouts.hospital')

@section('content')
    <main class="content-area">

        <!-- HEADER -->
        <div class="mb-4">
            <h3 class="fw-bold text-danger">InstaDugo Admin Dashboard</h3>
            <p class="text-muted">
                Web-Based Blood Donation Scheduling & Rule-Based Compatibility Matching System
            </p>
        </div>

        <!-- SYSTEM SUMMARY CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary"><i class="bi bi-people"></i></div>
                    <div>
                        <small>Registered Users</small>
                        <h5 class="fw-bold">{{ $totalUsers }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <small>Requests Pending Approval</small>
                        <h5 class="fw-bold">{{ $activeRequests }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-success"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <small>Requests Fulfilled</small>
                        <h5 class="fw-bold">{{ $matchesCompleted }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-danger"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <small>Today’s Appointments</small>
                        <h5 class="fw-bold">{{ $totalDonations }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP 5 PRIORITY QUEUE REQUESTS -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Top 5 Urgent Blood Requests</h5>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date Requested</th>
                        <th>Blood Type</th>
                        <th>Quantity</th>
                        <th>Urgency Level</th>
                        <th>Date Needed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queueRequests->take(5) as $request)
                        <tr>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="fw-semibold text-danger">{{ $request->blood_type }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>
                                <span
                                    class="badge {{ $request->urgency == 'Emergency' ? 'bg-danger' : ($request->urgency == 'High' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ $request->urgency }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($request->date_needed)->format('M d, Y') }}</td>
                            <td>
                                <span
                                    class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-info' : 'bg-success') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No blood requests in queue</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-end mt-2">
                <a href="" class="btn btn-sm btn-outline-primary">View All Requests</a>
            </div>
        </div>

        <!-- TOP 5 FULFILLED REQUESTS -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Recently Fulfilled Requests</h5>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Blood Type</th>
                        <th>Quantity</th>
                        <th>Urgency Level</th>
                        <th>Date Needed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fulfilledRequests->take(5) as $request)
                        <tr>
                            <td>#{{ $request->id }}</td>
                            <td class="fw-semibold text-danger">{{ $request->blood_type }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>
                                <span
                                    class="badge {{ $request->urgency == 'Emergency' ? 'bg-danger' : ($request->urgency == 'High' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ $request->urgency }}
                                </span>
                            </td>
                            <td>{{ $request->date_needed }}</td>
                            <td><span class="badge bg-success">Fulfilled</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No fulfilled requests yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-end mt-2">
                <a href="" class="btn btn-sm btn-outline-success">View All Fulfilled</a>
            </div>
        </div>

        <!-- TODAY’S DONATION SCHEDULE -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Today’s Donation Appointments</h5>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Donor</th>
                        <th>Blood Type</th>
                        <th>Blood Center</th>
                        <th>Schedule Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules->take(5) as $schedule)
                        <tr>
                            <td>{{ $schedule->donor_name }}</td>
                            <td class="fw-semibold text-danger">{{ $schedule->blood_type }}</td>
                            <td>{{ $schedule->hospital }}</td>
                            <td>{{ $schedule->schedule_date }}</td>
                            <td><span class="badge bg-primary">{{ ucfirst($schedule->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No schedules today</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-end mt-2">
                <a href="" class="btn btn-sm btn-outline-primary">View All
                    Appointments</a>
            </div>
        </div>

        <!-- TOP 5 LATEST USERS -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Recently Registered Users</h5>
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
                    @forelse($users->take(5) as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td class="fw-semibold text-danger">{{ $user->blood_type }}</td>
                            <td><span class="badge bg-success">{{ ucfirst($user->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-end mt-2">
                <a href="" class="btn btn-sm btn-outline-primary">View All Users</a>
            </div>
        </div>

        <!-- REPORTS -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">System Evaluation Reports</h5>
            <ul class="list-group">
                <li class="list-group-item">📊 Blood Request Statistics Report</li>
                <li class="list-group-item">🧬 Compatibility Matching Accuracy Report</li>
                <li class="list-group-item">📅 Donation Completion Report</li>
                <li class="list-group-item">📈 User Satisfaction (ISO/IEC 25010 & TAM)</li>
            </ul>
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
