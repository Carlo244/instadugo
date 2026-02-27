@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Blood Request Management</h3>
                <p class="text-muted small">Manage live queues by priority level.</p>
            </div>
            <span class="badge bg-blood-gradient rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-activity me-2"></i>
                Active:
                {{ ($queues['Emergency']->count() ?? 0) + ($queues['High']->count() ?? 0) + ($queues['Normal']->count() ?? 0) }}
            </span>
        </div>

        <ul class="nav nav-pills mb-4 gap-2" id="main-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill px-4 shadow-sm" data-bs-toggle="pill"
                    data-bs-target="#tab-live-queue">
                    <i class="bi bi-list-stars me-2"></i>Live Queue
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-history">
                    <i class="bi bi-clock-history me-2"></i>History
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- LIVE QUEUE -->
            <div class="tab-pane fade show active" id="tab-live-queue">
                <div class="glass-card border-0 shadow-sm p-4"
                    style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); border-radius: 1.5rem;">

                    <ul class="nav nav-pills nav-fill mb-4 gap-3 p-2 bg-light rounded-4" id="priority-tabs" role="tablist">
                        @foreach (['Emergency', 'High', 'Normal'] as $level)
                            @php
                                $count = ($queues[$level] ?? collect())->count();
                                $btnClass = match ($level) {
                                    'Emergency' => 'btn-priority-emergency',
                                    'High' => 'btn-priority-high',
                                    'Normal' => 'btn-priority-normal',
                                };
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link {{ $level == 'Emergency' ? 'active' : '' }} {{ $btnClass }} d-flex align-items-center justify-content-center flex-column py-3 rounded-4 transition-all border-0 shadow-sm"
                                    data-bs-toggle="tab" data-bs-target="#priority-{{ strtolower($level) }}"
                                    style="min-height: 90px;">
                                    <span
                                        class="small text-uppercase fw-black letter-spacing-1 opacity-75">{{ $level }}</span>
                                    <div class="d-flex align-items-center mt-1">
                                        <h2 class="mb-0 fw-bold">{{ $count }}</h2>
                                        @if ($level == 'Emergency' && $count > 0)
                                            <span class="ms-2 d-inline-block rounded-circle bg-white opacity-50 pulse-dot"
                                                style="width: 8px; height: 8px;"></span>
                                        @endif
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-4">
                        @foreach (['Emergency', 'High', 'Normal'] as $level)
                            <div class="tab-pane fade {{ $level == 'Emergency' ? 'show active' : '' }}"
                                id="priority-{{ strtolower($level) }}">

                                <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3 rounded-3 {{ $level == 'Emergency' ? 'bg-danger text-white' : ($level == 'High' ? 'bg-warning text-dark' : 'bg-success text-white') }}"
                                            style="padding: 10px;">
                                            <i class="bi bi-stack fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0">{{ $level }} Priority Queue</h5>
                                            <p class="text-muted small mb-0">Manage and fulfill urgent blood requirements
                                            </p>
                                        </div>
                                    </div>
                                    <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2">
                                        Showing {{ ($queues[$level] ?? collect())->count() }} Request(s)
                                    </span>
                                </div>

                                @include('partials.hospital-bloodrequest-table', [
                                    'requests' => $queues[$level] ?? collect(),
                                    'level' => $level,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- HISTORY -->
            <div class="tab-pane fade" id="tab-history">
                <div class="glass-card border-0 shadow-sm opacity-90">
                    <h5 class="fw-bold mb-3 text-muted"><i class="bi bi-archive me-2"></i>Fulfilled Logs</h5>
                    @include('partials.hospital-bloodrequest-table', [
                        'requests' => $fulfilledRequests,
                        'level' => 'History',
                    ])
                </div>
            </div>
        </div>
    </main>
@endsection
