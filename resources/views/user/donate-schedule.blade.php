@extends('layouts.user')

@section('content')
    <main class="content-area">

        <h3 class="fw-bold mb-0 text-uppercase tracking-wide">DONATE & SCHEDULE APPOINTMENT</h3>
        <!-- DONATION / SCHEDULING FORM -->
        <div class="glass-card mb-4">
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
                            <select name="hospital_admin_id" id="hospital_admin_id" class="form-select" required>
                                <option value="" disabled selected>Select Hospital</option>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}">{{ $hospital->hospital_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Date of Donation</label>
                            <input type="date" name="donation_date" id="donation_date" class="form-control"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label>Time</label>
                            <select name="donation_time" id="donation_time" class="form-select" required>
                                <option value="">Select Time</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Blood Type</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->blood_type }}" disabled>
                        </div>

                        <div class="col-12">
                            <label>Health Declaration / Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional"></textarea>
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
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const hospitalSelect = document.getElementById('hospital_admin_id');
                const dateInput = document.getElementById('donation_date');
                const timeSelect = document.getElementById('donation_time');

                function updateSlots() {
                    const hospitalId = hospitalSelect.value;
                    const selectedDate = dateInput.value;

                    if (!hospitalId || !selectedDate) return;

                    const url = "{{ route('user.donate-schedule.occupied-times') }}" +
                        "?hospital_id=" + hospitalId + "&date=" + selectedDate;
                    fetch(url)
                        .then(res => res.json())
                        .then(occupiedTimes => {
                            generateTimeSlots(occupiedTimes, selectedDate);
                        });
                }

                function generateTimeSlots(occupiedTimes, selectedDate) {
                    timeSelect.innerHTML = '<option value="">Select Time</option>';

                    const now = new Date();
                    const today = new Date().toISOString().split('T')[0];
                    const currentHour = now.getHours();
                    const currentMinute = now.getMinutes();

                    let start = 8 * 60; // 8:00 AM
                    let end = 16 * 60; // 4:00 PM
                    let interval = 30; // 30 min slots

                    for (let minutes = start; minutes <= end; minutes += interval) {
                        let h = Math.floor(minutes / 60);
                        let m = minutes % 60;
                        let timeValue = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':00';

                        let option = document.createElement("option");
                        option.value = timeValue;
                        option.text = timeValue;

                        const isOccupied = occupiedTimes.includes(timeValue);

                        let isPast = false;
                        if (selectedDate === today && (h < currentHour || (h === currentHour && m <= currentMinute))) {
                            isPast = true;
                        }

                        if (isOccupied) {
                            option.disabled = true;
                            option.text += " (Unavailable)";
                            option.style.color = "#ccc";
                        } else if (isPast) {
                            option.disabled = true;
                            option.text += " (Passed)";
                            option.style.display = "none";
                        }

                        timeSelect.appendChild(option);
                    }
                }

                hospitalSelect.addEventListener('change', updateSlots);
                dateInput.addEventListener('change', updateSlots);
            });
        </script>
    </main>
@endsection
