<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4">User</th>
                <th>Blood</th>
                <th>Qty</th>
                <th>Date Needed</th>
                <th>Status</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $request->user->name }}</div>
                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;">ID:
                            #REQ-{{ $request->id }}</small>
                    </td>

                    <td>
                        <span class="badge bg-light text-danger border border-danger px-2 py-1">
                            <i class="bi bi-droplet-fill me-1"></i>{{ $request->blood_type }}
                        </span>
                    </td>

                    <td class="fw-semibold">{{ $request->quantity }} units</td>

                    <td class="small">{{ \Carbon\Carbon::parse($request->date_needed)->format('M d, Y') }}</td>

                    <td>
                        @php
                            $statusColor =
                                [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'fulfilled' => 'success',
                                ][$request->status] ?? 'secondary';
                        @endphp
                        <span
                            class="badge rounded-pill bg-{{ $statusColor }} {{ $request->status == 'pending' ? 'text-dark' : '' }} px-3">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>

                    <td class="text-end pe-4">
                        <div class="btn-group shadow-sm" role="group">
                            {{-- APPROVE/FULFILL ACTION --}}
                            @if ($request->status == 'pending')
                                <form method="POST" action="{{ route('hospital.requests.approve', $request->id) }}"
                                    class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary px-3" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            @elseif($request->status == 'approved')
                                <form method="POST" action="{{ route('hospital.requests.fulfill', $request->id) }}"
                                    class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success px-3" title="Fulfill">
                                        <i class="bi bi-bag-check"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- MATCH BUTTON --}}
                            <button class="btn btn-sm btn-dark px-3" data-bs-toggle="modal"
                                data-bs-target="#matchModal{{ $request->id }}" title="Match Donors">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">No requests available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- MODAL DEFINITIONS (Separated to fix clicking issues) --}}
@push('modals')
    @foreach ($requests as $request)
        <div class="modal fade" id="matchModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title fw-bold">Compatible Donors for {{ $request->blood_type }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">

                        <div class="modal-body p-4" style="background-color: #f8f9fa;">
                            @if ($request->matchedDonors->count())
                                <div class="row g-3">
                                    @foreach ($request->matchedDonors as $donor)
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm p-3 h-100 rounded-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-danger-subtle p-3 me-3 text-danger">
                                                        <i class="bi bi-person-heart fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 fw-bold">{{ $donor->name }}</h6>
                                                        <small class="text-muted d-block">{{ $donor->blood_type }} |
                                                            {{ $donor->contact }}</small>
                                                    </div>
                                                    <button class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm">
                                                        Notify
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">No compatible donors found in the database.</p>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-link text-muted text-decoration-none"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
    @endforeach
@endpush
