@extends('layouts.user')

@section('content')
    <main class="content-area">

        <h3 class="fw-bold mb-0 text-uppercase tracking-wide">My Blood Requests</h3>

        <!-- NEW REQUEST FORM -->
        <div class="glass-card mb-4">
            <form method="POST" action="{{ route('user.blood-requests.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Blood Type Needed</label>
                        <select name="blood_type" class="form-select" required>
                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Units Needed (Quantity)</label>
                        <input type="number" name="quantity" class="form-control" min="1" placeholder="e.g. 2"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Date Needed</label>
                        <!-- This matches your schema's 'date_needed' column -->
                        <input type="date" name="date_needed" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Hospital / Blood Center</label>
                        <select name="hospital_admin_id" class="form-select" required>
                            <option value="" disabled selected>Select Hospital</option>
                            @foreach ($hospitals as $hospital)
                                <option value="{{ $hospital->id }}">{{ $hospital->hospital_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Urgency Level</label>
                        <select name="urgency" class="form-select" required>
                            <option value="Normal">Normal (Routine)</option>
                            <option value="High">High (Urgent)</option>
                            <option value="Emergency">Emergency (Immediate)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Medical Reason</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Describe why the blood is needed..." required></textarea>
                    </div>

                    <div class="col-12 d-grid mt-4">
                        <button type="submit" class="btn btn-danger rounded-pill shadow-sm py-2">
                            <i class="bi bi-send-fill me-2"></i> Submit Request
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- REQUESTS: Tabs for Active and History -->
        <div class="glass-card mb-4">
            <ul class="nav nav-pills mb-3" id="requestTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active btn-sm" id="active-tab" data-bs-toggle="pill" data-bs-target="#active"
                        type="button" role="tab">Active</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="req-history-tab" data-bs-toggle="pill" data-bs-target="#req-history"
                        type="button" role="tab">History</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood</th>
                                    <th>Units</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentRequests as $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        <td>{{ $request->blood_type }}</td>
                                        <td>{{ $request->quantity }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->urgency == 'Emergency' ? 'danger' : ($request->urgency == 'High' ? 'warning' : 'secondary') }}">{{ $request->urgency }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'info' }}">{{ ucfirst($request->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No active requests.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="req-history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood</th>
                                    <th>Units</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyRequests as $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        <td>{{ $request->blood_type }}</td>
                                        <td>{{ $request->quantity }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->urgency == 'Emergency' ? 'danger' : ($request->urgency == 'High' ? 'warning' : 'secondary') }}">{{ $request->urgency }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->status == 'fulfilled' ? 'success' : 'danger' }}">{{ ucfirst($request->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No past requests.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
