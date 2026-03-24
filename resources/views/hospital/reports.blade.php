@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Analytics & Reports</h3>
                    <p class="text-muted small mb-0">
                        Overview of blood request trends and system performance
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
                            <label class="form-label small text-muted fw-semibold d-block mb-2">Quick Range</label>
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
                            <label class="form-label small text-muted fw-semibold">From</label>
                            <input type="date" name="from" class="form-control" value="{{ $from }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">To</label>
                            <input type="date" name="to" class="form-control" value="{{ $to }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-funnel-fill me-1"></i> Apply
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
                $fulfilledTotal = (int) ($statusCounts->firstWhere('status', 'fulfilled')->total ?? 0);
                $cancelledTotal = (int) ($statusCounts->firstWhere('status', 'cancelled')->total ?? 0);
            @endphp

            {{-- KPI Row --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Average Response Time</h6>
                            <h1 class="fw-bold text-danger">
                                {{ $avgResponseTime ? round($avgResponseTime, 2) : 0 }}
                            </h1>
                            <small class="text-muted">minutes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Fulfilled</h6>
                            <h1 class="fw-bold text-success">{{ $fulfilledTotal }}</h1>
                            <small class="text-muted">requests</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Cancelled</h6>
                            <h1 class="fw-bold text-secondary">{{ $cancelledTotal }}</h1>
                            <small class="text-muted">requests</small>
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
                            <h6 class="fw-semibold mb-3">Blood Requests Per Month</h6>
                            <canvas id="requestsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Fulfilled vs Cancelled --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Fulfilled vs Cancelled</h6>
                            <canvas id="statusChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Blood Type Demand --}}
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Most Requested Blood Types</h6>
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
