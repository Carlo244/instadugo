@extends('layouts.user')

@section('content')
    <!-- ===== MAIN CONTENT ===== -->
    <main class="content-area">

        <!-- HEADER -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h3 class="fw-bold">Hello, {{ explode(' ', auth()->user()->name)[0] }} 👋</h3>
                <p class="text-muted">Today is {{ now()->format('l, F jS') }}</p>
            </div>
            <div>
                <a href="{{ route('user.blood-requests') }}" class="btn btn-danger rounded-pill">Request Blood</a>
                <a href="{{ route('user.donate-schedule') }}" class="btn btn-outline-danger rounded-pill">Schedule
                    Donation</a>
            </div>
        </div>

        <!-- STATS -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card text-center">
                    <small>Blood Type</small>
                    <h5 class="fw-bold text-danger">{{ auth()->user()->blood_type }}</h5>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card text-center">
                    <small>Completed Donations</small>
                    <h4 class="fw-bold">
                        {{ \App\Models\Donation::where('user_id', auth()->id())->where('status', 'completed')->count() }}
                    </h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center">
                    <small>Active Requests</small>
                    <h4 class="fw-bold">
                        {{ \App\Models\BloodRequest::where('status', 'pending')->count() }}
                    </h4>
                </div>
            </div>

            <div class="col-md-3">
                <div class="glass-card text-center">
                    <small>Status</small>
                    <h5 class="fw-bold text-{{ auth()->user()->isEligible() ? 'success' : 'danger' }}">
                        {{ auth()->user()->isEligible() ? 'Eligible' : 'Not Eligible' }}
                    </h5>
                </div>
            </div>
        </div>

        <!-- CONTENT SECTIONS -->
        <div class="row g-4">
            <!-- PROFILE -->
            <div class="col-lg-6">
                <div class="glass-card h-100" id="profile">
                    <div class="text-center mb-3">
                        <div class="profile-avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width:90px;height:90px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h5>{{ auth()->user()->name }}</h5>
                        <span class="badge bg-secondary">{{ auth()->user()->role }}</span>
                    </div>
                    <hr>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Blood Type:</strong> <span
                            class="text-danger fw-bold">{{ auth()->user()->blood_type }}</span></p>
                    <p><strong>Address:</strong> {{ auth()->user()->address }}</p>
                    <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-3">Edit
                        Profile</a>
                </div>
            </div>

            <!-- NOTIFICATIONS -->
            <div class="col-lg-6">
                <div class="glass-card" id="notifications">
                    <h5 class="fw-bold mb-3">Notifications</h5>
                    <ul class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if ($notification->type === 'blood_request')
                                        🔔 New blood request for {{ $notification->blood_type }} at
                                        {{ $notification->hospital_name }}
                                    @elseif($notification->type === 'donation_scheduled')
                                        ❤️ Your donation on
                                        {{ \Carbon\Carbon::parse($notification->donation_date)->format('M d, Y') }}
                                        is approved
                                    @elseif($notification->type === 'donation_completed')
                                        ✔ Your donation on
                                        {{ \Carbon\Carbon::parse($notification->donation_date)->format('M d, Y') }}
                                        is completed
                                    @endif
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">No notifications yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

        <!-- MATCHED DONORS -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Compatible Donors</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Blood Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compatibleDonors as $donor)
                                <tr>
                                    <td>{{ $donor->name }}</td>
                                    <td>{{ $donor->blood_type }}</td>
                                    <td>
                                        <span class="badge bg-{{ $donor->isEligible() ? 'success' : 'danger' }}">
                                            {{ $donor->isEligible() ? 'Eligible' : 'Not Eligible' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill">Notify</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No compatible donors found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- HISTORY -->
        <div class="row mt-4">
            <div class="glass col-lg-6">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">My Blood Requests</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Blood</th>
                                <th>Units</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userRequests as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>{{ $request->blood_type }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'completed' ? 'success' : 'danger') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No blood requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="glass-card col-lg-6">
                <h5 class="fw-bold mb-3">My Donation History</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hospital</th>
                            <th>Blood Type</th>
                            <th>Status</th>
                            <th>Action</th> <!-- Added Header -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                            <tr>
                                <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                                <td>{{ $donation->hospitalAdmin->hospital_name }}</td>
                                <td>{{ $donation->blood_type }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $donation->status == 'scheduled' ? 'info' : ($donation->status == 'completed' ? 'success' : 'danger') }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($donation->status == 'scheduled')
                                        <form action="{{ route('donations.cancel', $donation->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to cancel this schedule?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No donations scheduled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
