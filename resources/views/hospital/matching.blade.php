@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="mb-4">
            <h3 class="fw-bold">Compatibility Matching</h3>
            <p class="text-muted small">Cross-referencing active requests with eligible donors.</p>
        </div>

        @forelse($requests as $request)
            <div class="glass-card mb-3 border-0 shadow-sm">
                <div class="row g-0 align-items-center">
                    <!-- Left: Request Info -->
                    <div class="col-md-4 p-3 border-end">
                        <span
                            class="badge @if ($request->urgency == 'Emergency') bg-danger @else bg-warning text-dark @endif mb-2">
                            {{ $request->urgency }}
                        </span>
                        <h6 class="fw-bold mb-1">{{ $request->user->name }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box bg-blood-gradient" style="width:35px; height:35px; font-size:0.8rem;">
                                {{ $request->blood_type }}
                            </div>
                            <span class="small text-muted">{{ $request->quantity }} Units Requested</span>
                        </div>
                    </div>

                    <!-- Right: Compatible Donors -->
                    <div class="col-md-8 p-3">
                        <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Available Compatible
                            Donors</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $allowed = $compatibilityMatrix[$request->blood_type] ?? [];
                                $matches = $donors->whereIn('blood_type', $allowed);
                            @endphp

                            @forelse($matches as $donor)
                                <div
                                    class="badge bg-white border text-dark p-2 px-3 rounded-pill shadow-sm d-flex align-items-center gap-2">
                                    <i class="bi bi-person-heart text-danger"></i>
                                    <span>{{ $donor->name }} ({{ $donor->blood_type }})</span>
                                </div>
                            @empty
                                <span class="text-muted small italic">Searching for compatible donors...</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card text-center py-5">
                <p class="text-muted mb-0">No pending blood requests to match.</p>
            </div>
        @endforelse
    </main>
@endsection
