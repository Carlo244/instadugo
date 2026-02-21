@extends('layouts.user')

@section('content')
    <main class="content-area">
        <h3 class="mb-4 fw-bold text-danger">My Profile</h3>

        {{-- Profile Card --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="glass-card text-center p-4">

                    {{-- Icon Avatar --}}
                    <div class="profile-avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width:90px;height:90px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <h5 class="fw-bold">{{ auth()->user()->name }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>

                    <span class="badge bg-danger">
                        {{ auth()->user()->blood_type ?? 'N/A' }}
                    </span>

                </div>
            </div>
        </div>

        {{-- Profile Form --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="glass-card p-4">
                    <h5 class="fw-bold mb-3">Personal Information</h5>

                    <form method="POST" action="{{ route('user.profile.update') }}" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ auth()->user()->email }}" required>
                                @if (auth()->user()->hasVerifiedEmail())
                                    <span class="badge bg-success">Email Verified</span>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif

                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact" class="form-control"
                                    value="{{ auth()->user()->contact }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Blood Type</label>
                                <select name="blood_type" class="form-select">
                                    <option value="{{ auth()->user()->blood_type }}" selected>
                                        {{ auth()->user()->blood_type }}
                                    </option>
                                    <option>A+</option>
                                    <option>A-</option>
                                    <option>B+</option>
                                    <option>B-</option>
                                    <option>AB+</option>
                                    <option>AB-</option>
                                    <option>O+</option>
                                    <option>O-</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control"
                                    value="{{ auth()->user()->age }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sex</label>
                                <select name="sex" class="form-select">
                                    <option selected>{{ auth()->user()->sex }}</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ auth()->user()->address }}</textarea>
                            </div>

                            <hr class="my-3">

                            <h6 class="fw-bold">Change Password (optional)</h6>

                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="bi bi-save"></i> Update Profile
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>

    </main>
@endsection
