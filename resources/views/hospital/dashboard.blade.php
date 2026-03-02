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
            @foreach ($stats as $key => $stat)
                <div class="col-md-3 col-6">
                    <div class="glass-card {{ $stat['config']['card_class'] }} h-100 mb-0">
                        <div class="icon-box"><i class="bi {{ $stat['config']['icon'] }}"></i></div>
                        <div class="text-part">
                            <small class="text-muted">{{ $stat['config']['label'] }}</small>
                            <h5 class="fw-bold mb-0">{{ number_format($stat['value']) }}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
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
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="blood-type-filter"
                                    onchange="filterBloodType(this.value)">
                                    <option value="">All Blood Types</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                                <a href="{{ route('hospital.requests') }}"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    View All
                                </a>
                            </div>
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
                                <tbody id="requests-table-body">
                                    @forelse($queueRequests->take(8) as $request)
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
                            <div class="d-flex gap-2">
                                <a href="{{ route('hospital.manageusers') }}"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    View All
                                </a>
                            </div>
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

@push('scripts')
    <script>
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + 1, 2, 3 for tab switching
            if (e.altKey && !e.ctrlKey && !e.shiftKey) {
                if (e.key === '1') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-requests"]').click();
                } else if (e.key === '2') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-appointments"]').click();
                } else if (e.key === '3') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-users"]').click();
                }
            }
        });

        // Blood type filter with loading state
        function filterBloodType(bloodType) {
            const tbody = document.getElementById('requests-table-body');
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-danger" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

            const url = new URL(window.location.href);
            if (bloodType) {
                url.searchParams.set('blood_type', bloodType);
            } else {
                url.searchParams.delete('blood_type');
            }

            window.location.href = url.toString();
        }

        // Tooltips for keyboard shortcuts
        const tabButtons = document.querySelectorAll('.nav-pills .nav-link');
        tabButtons.forEach((btn, index) => {
            const shortcut = index + 1;
            btn.title = `Alt+${shortcut}`;
        });
    </script>
@endpush
