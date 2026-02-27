@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="glass-card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-people-fill text-danger me-2"></i>User Management
                    </h5>
                    <p class="text-muted small mb-0">Manage donors and hospital staff accounts.</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="input-group shadow-sm" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 rounded-end-pill"
                            placeholder="Search by name or email...">
                    </div>
                    <button class="btn btn-danger rounded-pill px-4 shadow-sm">
                        <i class="bi bi-person-plus-fill me-2"></i>Add User
                    </button>
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
                            <th class="text-end pe-4">Actions</th>
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
                                            {{-- Just the exact number and "Days" --}}
                                            <span>{{ $user->daysUntilEligible() }} Days</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                            style="border-radius: 12px;">
                                            <li><a class="dropdown-item py-2" href="#"><i
                                                        class="bi bi-pencil me-2 text-primary"></i> Edit Profile</a></li>
                                            <li><a class="dropdown-item py-2" href="#"><i
                                                        class="bi bi-shield-lock me-2 text-warning"></i> Change
                                                    Permissions</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                        class="bi bi-trash me-2"></i> Deactivate Account</a></li>
                                        </ul>
                                    </div>
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
                    {{-- {{ $users->links() }} --}} {{-- Laravel Pagination Links --}}
                </nav>
            </div>
        </div>
    </main>
@endsection
