@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Blood Request Management</h3>
                <p class="text-muted small">Manage live queues by priority level.</p>
            </div>
            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
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
            <div class="tab-pane fade show active" id="tab-live-queue">
                <div class="glass-card border-0 shadow-sm p-4">

                    <ul class="nav nav-tabs nav-fill mb-4 border-bottom-0 gap-2" id="priority-tabs" role="tablist">
                        @foreach (['Emergency', 'High', 'Normal'] as $level)
                            <li class="nav-item">
                                <button
                                    class="nav-link {{ $level == 'Emergency' ? 'active' : '' }} rounded-3 border shadow-sm py-3"
                                    data-bs-toggle="tab" data-bs-target="#priority-{{ strtolower($level) }}">
                                    <div class="small text-uppercase fw-bold">{{ $level }}</div>
                                    <h4 class="mb-0">{{ ($queues[$level] ?? collect())->count() }}</h4>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3">
                        @foreach (['Emergency', 'High', 'Normal'] as $level)
                            <div class="tab-pane fade {{ $level == 'Emergency' ? 'show active' : '' }}"
                                id="priority-{{ strtolower($level) }}">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="vr me-2 {{ $level == 'Emergency' ? 'text-danger' : ($level == 'High' ? 'text-warning' : 'text-success') }}"
                                        style="width: 4px; opacity: 1; border-radius: 4px;"></div>
                                    <h5 class="fw-bold mb-0">{{ $level }} Priority List</h5>
                                </div>

                                @include('partials.hospital-bloodrequest-table', [
                                    'requests' => $queues[$level] ?? collect(),
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-history">
                <div class="glass-card border-0 shadow-sm opacity-90">
                    <h5 class="fw-bold mb-3 text-muted"><i class="bi bi-archive me-2"></i>Fulfilled Logs</h5>
                    @include('partials.hospital-bloodrequest-table', ['requests' => $fulfilledRequests])
                </div>
            </div>
        </div>
    </main>
@endsection
