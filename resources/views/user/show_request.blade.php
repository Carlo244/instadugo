@extends('layouts.user')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-uppercase tracking-wide">Invitations History</h3>
                <p class="text-muted small mb-0">View and manage your received blood requests</p>
            </div>
            <a href="{{ route('user.dashboard', ['tab' => 'invitations']) }}#invitations-section"
                class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Back to Invitations
            </a>
        </div>

        <div class="glass-card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Requester & Hospital</th>
                            <th>Blood</th>
                            <th>Qty</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invitations as $invite)
                            @php
                                $urgencyColor =
                                    [
                                        'Emergency' => 'danger',
                                        'High' => 'warning',
                                        'Normal' => 'secondary',
                                    ][$invite->urgency] ?? 'secondary';

                                $statusColor =
                                    [
                                        'pending' => 'warning',
                                        'accepted' => 'success',
                                        'declined' => 'secondary',
                                        'fulfilled' => 'info',
                                    ][$invite->status] ?? 'info';
                            @endphp
                            <tr id="invite-row-{{ $invite->id }}"
                                class="{{ (int) $selectedInvitationId === (int) $invite->id ? 'table-warning' : '' }}">
                                <td class="small text-muted">{{ $invite->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ optional($invite->user)->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ optional($invite->hospital)->hospital_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td><span class="fw-semibold text-danger">{{ $invite->blood_type }}</span></td>
                                <td>{{ $invite->quantity }}</td>
                                <td>
                                    <span class="badge bg-{{ $urgencyColor }}">{{ $invite->urgency }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $statusColor }}{{ in_array($invite->status, ['pending', 'accepted', 'fulfilled']) ? ' text-dark' : '' }}">
                                        {{ ucfirst($invite->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-dark rounded-pill px-3 view-invite-btn"
                                        data-id="{{ $invite->id }}"
                                        data-requester="{{ optional($invite->user)->name ?? 'N/A' }}"
                                        data-hospital="{{ optional($invite->hospital)->hospital_name ?? 'N/A' }}"
                                        data-blood-type="{{ $invite->blood_type }}"
                                        data-quantity="{{ $invite->quantity }}" data-urgency="{{ $invite->urgency }}"
                                        data-status="{{ $invite->status }}"
                                        data-date-needed="{{ \Carbon\Carbon::parse($invite->date_needed)->format('M d, Y') }}"
                                        data-message="{{ $invite->reason ?: 'No additional message provided.' }}"
                                        data-selected="{{ (int) $selectedInvitationId === (int) $invite->id ? '1' : '0' }}">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-envelope text-muted fs-2 d-block mb-2"></i>
                                    <span class="text-muted">No invitations available yet.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="inviteDetailsModal" tabindex="-1" aria-labelledby="inviteDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="inviteDetailsModalLabel">Invitation Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Request ID</label>
                                <div id="modalRequestId" class="fw-semibold"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Date Needed</label>
                                <div id="modalDateNeeded"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Requester</label>
                                <div id="modalRequester"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Hospital</label>
                                <div id="modalHospital"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Blood Type</label>
                                <div id="modalBloodType" class="text-danger fw-semibold"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Quantity</label>
                                <div id="modalQuantity"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Urgency</label>
                                <div id="modalUrgency"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Status</label>
                                <div id="modalStatus"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Reason / Message</label>
                                <div id="modalMessage" class="p-3 bg-light rounded border"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('inviteDetailsModal');
                const viewButtons = document.querySelectorAll('.view-invite-btn');
                if (!modalElement || !viewButtons.length || !window.bootstrap) return;

                const modal = new bootstrap.Modal(modalElement);

                const fillAndShowModal = (button) => {
                    document.getElementById('modalRequestId').textContent = '#' + (button.dataset.id || 'N/A');
                    document.getElementById('modalDateNeeded').textContent = button.dataset.dateNeeded || 'N/A';
                    document.getElementById('modalRequester').textContent = button.dataset.requester || 'N/A';
                    document.getElementById('modalHospital').textContent = button.dataset.hospital || 'N/A';
                    document.getElementById('modalBloodType').textContent = button.dataset.bloodType || 'N/A';
                    document.getElementById('modalQuantity').textContent = button.dataset.quantity || 'N/A';
                    document.getElementById('modalUrgency').textContent = button.dataset.urgency || 'N/A';
                    document.getElementById('modalStatus').textContent = (button.dataset.status || 'N/A').replace(
                        /^./,
                        (char) => char.toUpperCase());
                    document.getElementById('modalMessage').textContent = button.dataset.message ||
                        'No additional message provided.';

                    modal.show();
                };

                viewButtons.forEach((button) => {
                    button.addEventListener('click', function() {
                        fillAndShowModal(button);
                    });
                });

                const selectedButton = document.querySelector('.view-invite-btn[data-selected="1"]');
                if (selectedButton) {
                    const selectedRow = document.getElementById('invite-row-' + selectedButton.dataset.id);
                    if (selectedRow) {
                        selectedRow.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        </script>
    </main>
@endsection
