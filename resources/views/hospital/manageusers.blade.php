@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="glass-card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-people-fill text-danger me-2"></i>User Management
                    </h5>
                    <p class="text-muted small mb-0">Manage donors and recipients accounts.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="input-group shadow-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="search-users" class="form-control border-start-0 rounded-end-pill"
                            placeholder="Search users..." value="{{ request('search') }}">
                    </div>
                    <select id="blood-type-filter" class="form-select form-select-sm shadow-sm" style="width: 120px;">
                        <option value="">All Blood Types</option>
                        <option value="A+" {{ request('blood_type') === 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ request('blood_type') === 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ request('blood_type') === 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ request('blood_type') === 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ request('blood_type') === 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ request('blood_type') === 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ request('blood_type') === 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ request('blood_type') === 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                    {{-- Trigger Add User Modal --}}
                    <button class="btn btn-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill me-2"></i>Add User
                    </button>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-people fs-2 text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($totalUsers ?? 0) }}</h3>
                                    <small class="text-muted">Total Users</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-check-circle fs-2 text-success"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($eligibleCount ?? 0) }}</h3>
                                    <small class="text-muted">Eligible Donors</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">User Details</th>
                            <th>Blood Type</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center text-danger fw-bold"
                                            style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($user->blood_type)
                                        <span class="badge bg-light text-danger border border-danger px-2">
                                            <i class="bi bi-droplet-fill me-1"></i>{{ $user->blood_type }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">Not Set</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->isEligible())
                                        <div class="d-flex align-items-center text-success small fw-bold">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <span
                                                class="badge bg-success-subtle text-success border border-success px-2">Eligible</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-warning small fw-bold">
                                            <i class="bi bi-clock-history me-2"></i>
                                            <span>{{ $user->daysUntilEligible() }} Days</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                                        No users found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <p class="text-muted small">Showing {{ $users->count() }} out of {{ $users->total() ?? $users->count() }}
                    users</p>
                <nav>
                    {{-- {{ $users->links() }} --}}
                </nav>
            </div>
        </div>
    </main>

    {{-- ===================== ADD USER MODAL ===================== --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                {{-- Modal Header --}}
                <div class="modal-header text-white border-0 px-4 pt-4 pb-3"
                    style="background: linear-gradient(135deg, #c0392b, #e74c3c);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="addUserModalLabel">
                            <i class="bi bi-person-plus-fill me-2"></i>Add New User
                        </h5>
                        <p class="small mb-0 opacity-75">Register a new donor or recipient to the system.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body px-4 py-4">
                    <form action="{{ route('hospital.manageusers.store') }}" method="POST" id="addUserForm">
                        {{ csrf_field() }}
                        <div class="row g-3">
                            {{-- Divider --}}
                            <div class="col-12">
                                <p class="small fw-semibold text-muted text-uppercase mb-0">
                                    <i class="bi bi-person me-1"></i> Personal Information
                                </p>
                            </div>

                            {{-- Full Name --}}
                            <div class="col-12">
                                <label for="modal_name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="modal_name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Juan Dela Cruz" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12">
                                <label for="modal_email" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" id="modal_email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="e.g. juan@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6">
                                <label for="modal_password" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="modal_password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Min. 8 characters" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="modal_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-md-6">
                                <label for="modal_password_confirmation" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="modal_password_confirmation"
                                        class="form-control" placeholder="Re-enter password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="modal_password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Contact --}}
                            <div class="col-md-8">
                                <label for="modal_contact" class="form-label">Contact Number <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">+63</span>
                                    <input type="tel" name="contact" id="modal_contact" class="form-control"
                                        maxlength="10" value="{{ old('contact') }}" placeholder="9123456789" required>
                                </div>
                            </div>

                            {{-- Age --}}
                            <div class="col-md-4">
                                <label for="modal_age" class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age" id="modal_age" class="form-control"
                                    value="{{ old('age') }}" min="18" placeholder="18+" required>
                            </div>

                            {{-- Sex --}}
                            <div class="col-md-6">
                                <label class="form-label">Sex <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sex" id="modal_male"
                                            value="Male" required {{ old('sex') == 'Male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modal_male">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sex" id="modal_female"
                                            value="Female" {{ old('sex') == 'Female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modal_female">Female</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Blood Type --}}
                            <div class="col-md-6">
                                <label for="modal_blood_type" class="form-label">Blood Type <span
                                        class="text-danger">*</span></label>
                                <select name="blood_type" id="modal_blood_type" class="form-select" required>
                                    <option value="" selected disabled>Select Type</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('blood_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Address --}}
                            <div class="col-12">
                                <label for="modal_address" class="form-label">Address (City/Barangay) <span
                                        class="text-danger">*</span></label>
                                <textarea name="address" id="modal_address" class="form-control" rows="2"
                                    placeholder="e.g. Brgy. San Jose, Manila" required>{{ old('address') }}</textarea>
                            </div>

                            {{-- Terms --}}
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="modal_agreeTerms" required>
                                    <label class="form-check-label small text-muted" for="modal_agreeTerms">
                                        I confirm this user's information will be used solely for blood donation
                                        scheduling and compatibility matching in accordance with data privacy
                                        regulations.
                                    </label>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" form="addUserForm" class="btn btn-danger rounded-pill px-4 shadow-sm">
                        <i class="bi bi-person-check-fill me-2"></i>Register User
                    </button>
                </div>

            </div>
        </div>
    </div>
    {{-- ===================== END MODAL ===================== --}}

    <style>
        .role-card {
            transition: border-color 0.2s, background-color 0.2s;
        }

        .role-card:has(input:checked) {
            border-color: #e74c3c !important;
            background-color: #fff5f5;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Search and filter functionality
        let searchTimeout;
        const searchInput = document.getElementById('search-users');
        const bloodTypeFilter = document.getElementById('blood-type-filter');

        function applyFilters() {
            const url = new URL(window.location.href);
            const search = searchInput?.value || '';
            const bloodType = bloodTypeFilter?.value || '';

            // Update URL parameters
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (bloodType) {
                url.searchParams.set('blood_type', bloodType);
            } else {
                url.searchParams.delete('blood_type');
            }

            // Reset to page 1 when filtering
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        // Debounced search
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });
        }

        // Immediate filter on select change
        if (bloodTypeFilter) {
            bloodTypeFilter.addEventListener('change', applyFilters);
        }

        // Keyboard shortcut to focus search (Ctrl+K or Cmd+K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput?.focus();
            }
        });
    </script>
@endpush
