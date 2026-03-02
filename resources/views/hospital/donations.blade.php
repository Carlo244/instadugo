@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Donation Appointments</h3>
                <p class="text-muted small">Organize and process blood donor schedules.</p>
            </div>
            <div class="d-flex gap-2">
                <select id="blood-type-filter" class="form-select form-select-sm" style="width: 110px;">
                    <option value="">All Types</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
                <span class="badge bg-blood-gradient px-3 py-2 shadow-sm rounded-pill">
                    <i class="bi bi-calendar-check me-2"></i> {{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>

        <!-- TABS NAVIGATION -->
        <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist" aria-label="Donation sections">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 shadow-sm" data-bs-toggle="pill"
                    data-bs-target="#tab-today" role="tab" aria-selected="true" title="Press T">
                    <i class="bi bi-play-circle-fill me-2"></i>Today's Queue
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-upcoming"
                    role="tab" aria-selected="false" title="Press U">
                    <i class="bi bi-calendar-event me-2"></i>Upcoming
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-history"
                    role="tab" aria-selected="false" title="Press H">
                    <i class="bi bi-clock-history me-2"></i>History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- 1. TODAY'S QUEUE -->
            <div class="tab-pane fade show active" id="tab-today">
                <div class="glass-card border-0 shadow-sm">
                    <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-droplet-half me-2"></i>Active Today</h5>
                    <div id="hospital-donations-today">
                        @include('partials.hospital-donations-table', [
                            'donations' => $todayQueue,
                            'showActions' => true,
                        ])
                    </div>
                </div>
            </div>

            <!-- 2. UPCOMING -->
            <div class="tab-pane fade" id="tab-upcoming">
                <div class="glass-card border-0 shadow-sm opacity-90">
                    <h5 class="fw-bold mb-3 text-blood-dark"><i class="bi bi-calendar-week me-2"></i>Future Schedules</h5>
                    <div id="hospital-donations-upcoming">
                        @include('partials.hospital-donations-table', [
                            'donations' => $upcoming,
                            'showActions' => false,
                        ])
                    </div>
                </div>
            </div>

            <!-- 3. HISTORY -->
            <div class="tab-pane fade" id="tab-history">
                <div class="glass-card border-0 shadow-sm opacity-75">
                    <h5 class="fw-bold mb-3 text-muted"><i class="bi bi-archive me-2"></i>Logs</h5>
                    <div id="hospital-donations-history">
                        @include('partials.hospital-donations-table', [
                            'donations' => $history,
                            'showActions' => false,
                        ])
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
            if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.target.matches('input, textarea, select')) {
                if (e.key === 't' || e.key === 'T') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-today"]')?.click();
                } else if (e.key === 'u' || e.key === 'U') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-upcoming"]')?.click();
                } else if (e.key === 'h' || e.key === 'H') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-history"]')?.click();
                }
            }
        });

        // Blood type filter functionality
        const bloodTypeFilter = document.getElementById('blood-type-filter');

        if (bloodTypeFilter) {
            bloodTypeFilter.addEventListener('change', filterDonations);
        }

        function filterDonations() {
            const bloodType = bloodTypeFilter?.value || '';

            document.querySelectorAll('[id^="tab-"]').forEach(tabPane => {
                const tables = tabPane.querySelectorAll('table tbody tr:not(.empty-row)');
                let visibleCount = 0;

                tables.forEach(row => {
                    const rowBloodType = row.dataset.bloodType || '';
                    const matchesBloodType = !bloodType || rowBloodType === bloodType;

                    if (matchesBloodType) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Initialize from URL
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const bloodType = urlParams.get('blood_type');

            if (bloodType && bloodTypeFilter) bloodTypeFilter.value = bloodType;

            if (bloodType) filterDonations();
        });
    </script>
@endpush
