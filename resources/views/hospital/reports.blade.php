@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Hospital Duty Summary</h3>
                    <p class="text-muted small mb-0">
                        Simple overview for on-duty staff.
                    </p>
                </div>
                <span class="badge bg-blood-gradient px-3 py-2 shadow-sm rounded-pill">
                    <i class="bi bi-calendar-check me-2"></i>Last updated: {{ now()->format('M d, Y') }}
                </span>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('hospital.reports') }}" class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label small text-muted fw-semibold d-block mb-2">Quick view</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" name="preset" value="last7"
                                    class="btn btn-sm {{ ($preset ?? null) === 'last7' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill px-3">
                                    Last 7 Days
                                </button>
                                <button type="submit" name="preset" value="last30"
                                    class="btn btn-sm {{ ($preset ?? null) === 'last30' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill px-3">
                                    Last 30 Days
                                </button>
                                <button type="submit" name="preset" value="this_month"
                                    class="btn btn-sm {{ ($preset ?? null) === 'this_month' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill px-3">
                                    This Month
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">Start date</label>
                            <input type="date" name="from" class="form-control" value="{{ $from }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">End date</label>
                            <input type="date" name="to" class="form-control" value="{{ $to }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-funnel-fill me-1"></i> Show
                            </button>
                            <a href="{{ route('hospital.reports') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Reset
                            </a>
                        </div>
                    </form>
                    <p class="small text-muted mb-0 mt-3">
                        Showing:
                        @if ($from || $to)
                            {{ $from ? \Carbon\Carbon::parse($from)->format('M d, Y') : 'Beginning' }}
                            to
                            {{ $to ? \Carbon\Carbon::parse($to)->format('M d, Y') : 'Present' }}
                        @else
                            All time
                        @endif
                    </p>
                </div>
            </div>

            @php
                $fulfilledTotal = (int) (optional($statusCounts->firstWhere('status', 'fulfilled'))->total ?? 0);
                $cancelledTotal = (int) (optional($statusCounts->firstWhere('status', 'cancelled'))->total ?? 0);
            @endphp

            {{-- Simple Summary Row --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Requests received</h6>
                            <h1 class="fw-bold text-danger">
                                {{ $totalRequests }}
                            </h1>
                            <small class="text-muted">total requests in the selected period</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Still waiting</h6>
                            <h1 class="fw-bold text-warning">{{ $waitingRequests }}</h1>
                            <small class="text-muted">pending or accepted</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Handled</h6>
                            <h1 class="fw-bold text-success">{{ $completedRequests }}</h1>
                            <small class="text-muted">fulfilled requests</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Cancelled</h6>
                            <h2 class="fw-bold text-secondary mb-0">{{ $cancelledRequests }}</h2>
                            <small class="text-muted">requests that were stopped</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Average time to respond</h6>
                            <h2 class="fw-bold text-danger mb-0">
                                {{ $avgResponseTime ? round($avgResponseTime, 2) : 0 }}
                            </h2>
                            <small class="text-muted">minutes</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Needed Now --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Action Needed Now</h6>
                        <span class="badge text-bg-light">Live operations snapshot</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-light-subtle">
                                <p class="text-muted small mb-1">Requests waiting now</p>
                                <h3 class="fw-bold text-dark mb-1">{{ $actionWaitingNow }}</h3>
                                <small class="text-muted">Pending and accepted requests requiring follow-up.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-danger-subtle">
                                <p class="text-muted small mb-1">Urgent today</p>
                                <h3 class="fw-bold text-danger mb-1">{{ $actionUrgentToday }}</h3>
                                <small class="text-muted">Emergency requests needed today or earlier.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-warning-subtle">
                                <p class="text-muted small mb-1">Pending over 2 hours</p>
                                <h3 class="fw-bold text-warning mb-1">{{ $actionOverduePending }}</h3>
                                <small class="text-muted">Old pending requests that should be reviewed now.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="row g-4">

                {{-- Requests Per Month --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2">Requests received over time</h6>
                            <p class="small text-muted mb-3">Shows when requests came in during the selected period.</p>
                            <canvas id="requestsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Fulfilled vs Cancelled --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2">Handled vs cancelled</h6>
                            <p class="small text-muted mb-3">Shows which requests were completed and which were stopped.
                            </p>
                            <canvas id="statusChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Blood Type Demand --}}
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2">Most needed blood types</h6>
                            <p class="small text-muted mb-3">Shows which blood types were requested most often.</p>
                            <canvas id="bloodTypeChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const requestsData = @json($requestsPerMonth);
        const statusData = @json($statusCounts);
        const bloodTypeData = @json($bloodTypeCounts);

        const hasRequestsData = requestsData.length > 0;
        const hasStatusData = statusData.length > 0;
        const hasBloodTypeData = bloodTypeData.length > 0;

        // Requests per month (Line Chart)
        new Chart(document.getElementById('requestsChart'), {
            type: 'line',
            data: {
                labels: hasRequestsData ? requestsData.map(d => {
                    const [year, month] = String(d.month).split('-');
                    return new Date(year, Number(month) - 1, 1).toLocaleDateString('en-US', {
                        month: 'short',
                        year: 'numeric'
                    });
                }) : ['No Data'],
                datasets: [{
                    label: 'Requests',
                    data: hasRequestsData ? requestsData.map(d => d.total) : [0],
                    tension: 0.3,
                    fill: true
                }]
            }
        });

        // Fulfilled vs Cancelled (Pie Chart)
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: hasStatusData ? statusData.map(d => d.status.toUpperCase()) : ['NO DATA'],
                datasets: [{
                    data: hasStatusData ? statusData.map(d => d.total) : [1]
                }]
            }
        });

        // Blood Type Demand (Bar Chart)
        new Chart(document.getElementById('bloodTypeChart'), {
            type: 'bar',
            data: {
                labels: hasBloodTypeData ? bloodTypeData.map(d => d.blood_type) : ['No Data'],
                datasets: [{
                    label: 'Requests',
                    data: hasBloodTypeData ? bloodTypeData.map(d => d.total) : [0]
                }]
            }
        });
    </script>
@endpush
