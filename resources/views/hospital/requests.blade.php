@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Blood Request Management</h3>
                <p class="text-muted small">Manage live queues by priority level.</p>
            </div>
            <div class="d-flex gap-2">
                <select id="blood-type-filter" class="form-select form-select-sm" style="width: 120px;">
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
                <span class="badge bg-blood-gradient rounded-pill px-3 py-2 shadow-sm" aria-live="polite"
                    aria-label="Total active requests">
                    <i class="bi bi-activity me-2"></i>
                    Active: <strong>{{ $totalActive }}</strong>
                </span>
            </div>
        </div>

        <ul class="nav nav-pills mb-4 gap-2" id="main-tabs" role="tablist" aria-label="Request sections">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 shadow-sm" data-bs-toggle="pill"
                    data-bs-target="#tab-live-queue" role="tab" aria-selected="true" aria-controls="tab-live-queue">
                    <i class="bi bi-list-stars me-2"></i>Live Queue
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-history"
                    role="tab" aria-selected="false" aria-controls="tab-history">
                    <i class="bi bi-clock-history me-2"></i>History
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- LIVE QUEUE -->
            <div class="tab-pane fade show active" id="tab-live-queue" role="tabpanel" aria-labelledby="tab-live-queue">
                <div class="glass-card-container">
                    <div class="glass-card border-0 shadow-sm p-4">
                        <!-- Priority Level Tabs -->
                        <ul class="nav nav-pills nav-fill mb-4 gap-3 p-2 bg-light rounded-4" id="priority-tabs"
                            role="tablist" aria-label="Request priority levels">
                            @foreach ($priorityOrder as $level)
                                @php
                                    $queueData = $queues[$level];
                                    $config = $queueData['config'];
                                    $count = $queueData['count'];
                                    $isActive = $level === 'Emergency';
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ $isActive ? 'active' : '' }} {{ $config['button_class'] }} d-flex align-items-center justify-content-center flex-column py-3 rounded-4 transition-all border-0 shadow-sm"
                                        data-bs-toggle="tab" data-bs-target="#priority-{{ strtolower($level) }}"
                                        role="tab" aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                        aria-controls="priority-{{ strtolower($level) }}"
                                        aria-label="{{ $level }} priority queue with {{ $count }} request{{ $count !== 1 ? 's' : '' }}">
                                        <span
                                            class="small text-uppercase fw-black letter-spacing-1 opacity-75">{{ $level }}</span>
                                        <div class="d-flex align-items-center mt-1">
                                            <h2 class="mb-0 fw-bold">{{ $count }}</h2>
                                            @if ($level === 'Emergency' && $count > 0)
                                                <span class="ms-2 d-inline-block rounded-circle opacity-50 pulse-dot"
                                                    style="width: 8px; height: 8px; background-color: white;"
                                                    aria-hidden="true"></span>
                                            @endif
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Priority Tab Content -->
                        <div class="tab-content mt-4">
                            @foreach ($priorityOrder as $level)
                                @php
                                    $queueData = $queues[$level];
                                    $config = $queueData['config'];
                                    $requests = $queueData['requests'];
                                    $count = $queueData['count'];
                                    $isActive = $level === 'Emergency';
                                @endphp
                                <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                                    id="priority-{{ strtolower($level) }}" role="tabpanel"
                                    aria-labelledby="priority-{{ strtolower($level) }}">

                                    <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box me-3 rounded-3 {{ $config['icon_bg'] }}"
                                                style="padding: 10px;">
                                                <i class="bi {{ $config['icon'] }} fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-0">{{ $level }} Priority Queue</h5>
                                                <p class="text-muted small mb-0">Manage and fulfill urgent blood
                                                    requirements</p>
                                            </div>
                                        </div>
                                        <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2"
                                            aria-live="polite"
                                            aria-label="Number of requests in {{ strtolower($level) }} queue">
                                            Showing {{ $count }} Request{{ $count !== 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    <div id="bloodrequests-live-{{ strtolower($level) }}">
                                        @include('partials.hospital-bloodrequest-table', [
                                            'requests' => $requests,
                                            'level' => $level,
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORY -->
            <div class="tab-pane fade" id="tab-history" role="tabpanel" aria-labelledby="tab-history">
                <div class="glass-card border-0 shadow-sm opacity-90">
                    <h5 class="fw-bold mb-3 text-muted"><i class="bi bi-archive me-2"></i>Fulfilled Logs</h5>
                    <div id="bloodrequests-history">
                        @include('partials.hospital-bloodrequest-table', [
                            'requests' => $fulfilledRequests,
                            'level' => 'History',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Keyboard shortcuts for priority tabs
        document.addEventListener('keydown', function(e) {
            // Number keys 1, 2, 3 for priority levels
            if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.target.matches('input, textarea')) {
                if (e.key === '1') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#priority-emergency"]')?.click();
                } else if (e.key === '2') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#priority-high"]')?.click();
                } else if (e.key === '3') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#priority-normal"]')?.click();
                } else if (e.key === 'h') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-history"]')?.click();
                } else if (e.key === 'q') {
                    e.preventDefault();
                    document.querySelector('[data-bs-target="#tab-live-queue"]')?.click();
                }
            }
        });

        // Blood type filter
        const bloodTypeFilter = document.getElementById('blood-type-filter');

        if (bloodTypeFilter) {
            bloodTypeFilter.addEventListener('change', filterTable);
        }

        function filterTable() {
            const bloodType = bloodTypeFilter?.value || '';

            // Get all priority tab panes
            document.querySelectorAll('[id^="priority-"], #tab-history').forEach(tabPane => {
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

                // Update count badge in the current tab
                const countBadge = tabPane.querySelector('.badge[aria-live]');
                if (countBadge && visibleCount !== tables.length) {
                    const originalText = countBadge.textContent;
                    countBadge.textContent =
                        `Showing ${visibleCount} Request${visibleCount !== 1 ? 's' : ''} (filtered)`;
                }
            });
        }

        // Add keyboard shortcut hints to buttons
        const priorityButtons = document.querySelectorAll('#priority-tabs .nav-link');
        priorityButtons.forEach((btn, index) => {
            const key = index + 1;
            btn.title = `Press ${key} to switch`;
        });

        const mainTabButtons = document.querySelectorAll('#main-tabs .nav-link');
        mainTabButtons[0].title = 'Press Q for Queue';
        mainTabButtons[1].title = 'Press H for History';

        // Loading state helper
        function showLoading(elementId) {
            const el = document.getElementById(elementId);
            if (el) {
                el.innerHTML =
                    '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            }
        }

        // Initialize from URL parameters
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const bloodType = urlParams.get('blood_type');

            if (bloodType && bloodTypeFilter) {
                bloodTypeFilter.value = bloodType;
            }

            if (bloodType) {
                filterTable();
            }
        });

        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.js-open-match-from-details');
            if (!trigger) {
                return;
            }

            const currentModalEl = trigger.closest('.modal');
            const targetSelector = trigger.getAttribute('data-match-target');
            if (!currentModalEl || !targetSelector) {
                return;
            }

            const targetModalEl = document.querySelector(targetSelector);
            if (!targetModalEl) {
                return;
            }

            const currentModal = bootstrap.Modal.getOrCreateInstance(currentModalEl);
            const targetModal = bootstrap.Modal.getOrCreateInstance(targetModalEl);

            const onHidden = () => {
                currentModalEl.removeEventListener('hidden.bs.modal', onHidden);
                targetModal.show();
            };

            currentModalEl.addEventListener('hidden.bs.modal', onHidden, {
                once: true
            });
            currentModal.hide();
        });

        document.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => {
                const openModals = document.querySelectorAll('.modal.show').length;
                if (openModals === 0) {
                    document.querySelectorAll('.modal-backdrop').forEach((backdrop, index) => {
                        if (index > 0) {
                            backdrop.remove();
                        }
                    });
                }
            }, 50);
        });
    </script>
@endpush
