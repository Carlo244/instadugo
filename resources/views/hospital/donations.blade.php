@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Donation Appointments</h3>
                <p class="text-muted small">Organize and process blood donor schedules.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button
                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 btn btn-link text-decoration-none"
                    data-bs-toggle="modal" data-bs-target="#editPhlebotomistModal"
                    style="cursor: pointer; border: none; padding: 0.5rem 1rem;">
                    <i class="bi bi-people-fill"></i>
                    <span><strong>{{ $phlebotomistCount }}</strong> Phlebotomist{{ $phlebotomistCount !== 1 ? 's' : '' }} On
                        Duty</span>
                    <i class="bi bi-pencil-square ms-1 opacity-75" style="font-size: 0.85rem;"></i>
                </button>
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

        <!-- EDIT PHLEBOTOMIST MODAL -->
        <div class="modal fade" id="editPhlebotomistModal" tabindex="-1" aria-labelledby="editPhlebotomistLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary-subtle border-0">
                        <h5 class="modal-title fw-bold text-primary" id="editPhlebotomistLabel">
                            <i class="bi bi-people-fill me-2"></i>Update Phlebotomist Count
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('hospital.update-phlebotomist') }}" id="phlebotomistForm">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <p class="text-muted small mb-3">
                                Set the number of phlebotomists on duty. This determines how many donors can book the same
                                time slot.
                            </p>
                            <div class="mb-3">
                                <label for="phlebotomistInput" class="form-label fw-semibold">Number of
                                    Phlebotomists</label>
                                <input type="number" class="form-control form-control-lg border-2"
                                    id="phlebotomistInput" name="phlebotomist_count" value="{{ $phlebotomistCount }}"
                                    min="1" max="10" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Valid range: 1-10 phlebotomists
                                </small>
                            </div>
                            <div class="alert alert-info border-0" role="alert">
                                <i class="bi bi-lightbulb me-2"></i>
                                <small>
                                    With <strong id="previewCount">{{ $phlebotomistCount }}</strong>
                                    phlebotomist{{ $phlebotomistCount !== 1 ? 's' : '' }},
                                    users can book <strong id="previewSlots">{{ $phlebotomistCount }}</strong>
                                    slot{{ $phlebotomistCount !== 1 ? 's' : '' }} per time slot.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-check-circle me-2"></i>Update
                            </button>
                        </div>
                    </form>
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

            // Handle phlebotomist count input preview
            const phlebotomistInput = document.getElementById('phlebotomistInput');
            const previewCount = document.getElementById('previewCount');
            const previewSlots = document.getElementById('previewSlots');

            if (phlebotomistInput) {
                phlebotomistInput.addEventListener('input', function() {
                    const count = parseInt(this.value) || 1;
                    previewCount.textContent = count;
                    previewSlots.textContent = count;
                });
            }
        });
    </script>
@endpush
