@extends('layouts.user')

@section('content')
    <main class="content-area">

        <h3 class="fw-bold mb-0 text-uppercase tracking-wide">DONATE & SCHEDULE APPOINTMENT</h3>
        <!-- DONATION / SCHEDULING FORM -->
        <div class="glass-card mb-4">
            {{-- display any flash or validation errors at top --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!$isEligible)
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    You are not eligible to donate yet. You can schedule again after
                    <strong>{{ $nextEligibleDate }}</strong>.
                </div>
            @endif

            <form method="POST" action="{{ route('user.donate-schedule.store') }}">
                @csrf
                <fieldset @disabled(!$isEligible)> <!-- This disables everything inside the fieldset -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Hospital / Blood Center</label>
                            <select name="hospital_admin_id" id="hospital_admin_id" class="form-select @error('hospital_admin_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Hospital</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}" {{ old('hospital_admin_id') == $hospital->id ? 'selected' : '' }}>{{ $hospital->hospital_name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_admin_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Date of Donation</label>
                            <input type="date" name="donation_date" id="donation_date" class="form-control @error('donation_date') is-invalid @enderror"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" value="{{ old('donation_date') }}" required>
                            @error('donation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Time</label>
                            <select name="donation_time" id="donation_time" class="form-select @error('donation_time') is-invalid @enderror" required>
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
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Optional">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-{{ $isEligible ? 'danger' : 'secondary' }} rounded-pill">
                                {{ $isEligible ? 'Schedule Donation' : 'Scheduling Locked' }}
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <!-- USER DONATION HISTORY -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">My Donation History</h5>
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
                                    <form action="{{ route('user.donations.cancel', $donation->id) }}" method="POST"
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
    </main>
@endsection
