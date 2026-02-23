@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Donation Appointments</h3>
                <p class="text-muted small">Organize and process blood donor schedules.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-blood-gradient px-3 py-2 shadow-sm">
                    <i class="bi bi-calendar-check me-2"></i> {{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>

        <!-- TABS NAVIGATION -->
        <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill px-4 shadow-sm" data-bs-toggle="pill"
                    data-bs-target="#tab-today">
                    <i class="bi bi-play-circle-fill me-2"></i>Today's Queue
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-upcoming">
                    <i class="bi bi-calendar-event me-2"></i>Upcoming
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-history">
                    <i class="bi bi-clock-history me-2"></i>History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- 1. TODAY'S QUEUE -->
            <div class="tab-pane fade show active" id="tab-today">
                <div class="glass-card border-0 shadow-sm">
                    <h5 class="fw-bold mb-3"><i class="bi bi-droplet-half me-2 text-danger"></i>Active Today</h5>
                    @include('partials.hospital-donations-table', [
                        'donations' => $todayQueue,
                        'showActions' => true,
                    ])
                </div>
            </div>

            <!-- 2. UPCOMING -->
            <div class="tab-pane fade" id="tab-upcoming">
                <div class="glass-card border-0 shadow-sm opacity-90">
                    <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-calendar-week me-2"></i>Future Schedules</h5>
                    @include('partials.hospital-donations-table', [
                        'donations' => $upcoming,
                        'showActions' => false,
                    ])
                </div>
            </div>

            <!-- 3. HISTORY -->
            <div class="tab-pane fade" id="tab-history">
                <div class="glass-card border-0 shadow-sm opacity-75">
                    <h5 class="fw-bold mb-3 text-muted"><i class="bi bi-archive me-2"></i>Logs</h5>
                    @include('partials.hospital-donations-table', [
                        'donations' => $history,
                        'showActions' => false,
                    ])
                </div>
            </div>
        </div>
    </main>
@endsection
