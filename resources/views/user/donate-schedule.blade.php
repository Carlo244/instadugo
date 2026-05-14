@extends('layouts.user')

@section('content')
    <main class="content-area">

        <h3 class="fw-bold mb-0 text-uppercase tracking-wide">DONATE & SCHEDULE APPOINTMENT</h3>
        <!-- DONATION / SCHEDULING FORM -->
        <div class="glass-card mb-4">
            {{-- display any flash or validation errors at top --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!$isEligible)
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    You are not eligible to donate yet. Under the three-month whole blood donation interval, you can
                    schedule again after
                    <strong>{{ $nextEligibleDate }}</strong>.
                </div>
            @endif

            @if ($hasActiveSchedule)
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-calendar-check me-2"></i>
                    You already have an active schedule on
                    <strong>{{ $activeScheduledDonation->donation_date->format('M d, Y') }}</strong>
                    at
                    <strong>{{ \Carbon\Carbon::parse($activeScheduledDonation->donation_time)->format('h:i A') }}</strong>.
                    Please cancel or complete it before booking another one.
                </div>
            @endif

            <form method="POST" action="{{ route('user.donate-schedule.store') }}">
                @csrf
                <fieldset @disabled(!$isEligible || $hasActiveSchedule)> <!-- This disables everything inside the fieldset -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Hospital / Blood Center</label>
                            <select name="hospital_admin_id" id="hospital_admin_id"
                                class="form-select @error('hospital_admin_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Hospital</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}"
                                        {{ old('hospital_admin_id') == $hospital->id ? 'selected' : '' }}>
                                        {{ $hospital->hospital_name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_admin_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Date of Donation</label>
                            <input type="date" name="donation_date" id="donation_date"
                                class="form-control @error('donation_date') is-invalid @enderror"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" value="{{ old('donation_date') }}"
                                required>
                            @error('donation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Time</label>
                            <select name="donation_time" id="donation_time"
                                class="form-select @error('donation_time') is-invalid @enderror" required>
                                <option value="">Select Time</option>
                            </select>
                            @error('donation_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label>Blood Type</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->blood_type }}" disabled>
                        </div>

                        <div class="col-12">
                            <label>Health Declaration / Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                placeholder="Optional">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit"
                                class="btn btn-{{ $isEligible && !$hasActiveSchedule ? 'danger' : 'secondary' }} rounded-pill">
                                {{ $isEligible && !$hasActiveSchedule ? 'Schedule Donation' : 'Scheduling Locked' }}
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <!-- DONATIONS: Tabs for Upcoming and History -->
        <div class="glass-card mb-4">
            <ul class="nav nav-pills mb-3" id="donationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active btn-sm" id="upcoming-tab" data-bs-toggle="pill"
                        data-bs-target="#upcoming" type="button" role="tab">Upcoming</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">History</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hospital</th>
                                    <th>Blood Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingDonations as $donation)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                        <td>{{ $donation->hospitalAdmin->hospital_name }}</td>
                                        <td>{{ $donation->blood_type }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($donation->status) }}</span></td>
                                        <td>
                                            <form action="{{ route('user.donations.cancel', $donation->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to cancel this schedule?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No upcoming donations.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hospital</th>
                                    <th>Blood Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pastDonations as $donation)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                        <td>{{ $donation->hospitalAdmin->hospital_name }}</td>
                                        <td>{{ $donation->blood_type }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $donation->status == 'completed' ? 'success' : 'danger' }}">{{ ucfirst($donation->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No donation history yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
