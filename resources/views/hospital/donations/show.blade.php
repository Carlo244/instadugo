@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Donation Details</h3>
                <p class="text-muted small mb-0">Review the selected donation appointment.</p>
            </div>
            <a href="{{ route('hospital.donations') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Donations
            </a>
        </div>

        <div class="glass-card border-0 shadow-sm">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Donor Name</div>
                    <div class="fw-semibold">{{ $donation->user->name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Blood Type</div>
                    <div class="fw-semibold">{{ $donation->blood_type }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Donation Date</div>
                    <div class="fw-semibold">{{ optional($donation->donation_date)->format('M d, Y') ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Donation Time</div>
                    <div class="fw-semibold">{{ $donation->donation_time ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Status</div>
                    <div class="fw-semibold text-capitalize">{{ $donation->status }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Hospital</div>
                    <div class="fw-semibold">{{ $donation->hospitalAdmin->hospital_name ?? 'N/A' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Notes</div>
                    <div class="fw-semibold">{{ $donation->notes ?: 'No notes provided.' }}</div>
                </div>
            </div>
        </div>
    </main>
@endsection
