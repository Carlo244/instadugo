@extends('layouts.app')

@section('content')
    <div class="auth-scene d-flex align-items-center justify-content-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="glass-card auth-panel shadow-lg p-4 p-md-5">

                        <!-- Hero Header -->
                        <div class="card-header">
                            <h3 class="fw-bold mb-1">Blood Donor Registration</h3>
                            <p class="small mb-0">Your contribution can save up to three lives.</p>
                        </div>

                        <div class="card-body mt-4">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="password_confirmation">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <label for="contact" class="form-label">Contact Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">+63</span>
                                            <input type="tel" name="contact" id="contact" class="form-control"
                                                maxlength="10" value="{{ old('contact') }}" placeholder="9123456789"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" name="age" id="age" class="form-control"
                                            value="{{ old('age') }}" min="18" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Sex</label>
                                        <div class="d-flex gap-3 pt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="sex" id="male"
                                                    value="Male" required {{ old('sex') == 'Male' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="male">Male</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="sex" id="female"
                                                    value="Female" {{ old('sex') == 'Female' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="female">Female</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="blood_type" class="form-label">Blood Type</label>
                                        <select name="blood_type" id="blood_type" class="form-select" required>
                                            <option value="" selected disabled>Select Type</option>
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label">Address (City/Barangay)</label>
                                        <textarea name="address" id="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit"
                                            class="btn btn-danger w-100 py-3 fw-bold shadow-sm">Register</button>
                                        <div class="col-12 mt-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                                <label class="form-check-label small text-muted" for="agreeTerms">
                                                    I agree that my information will be used solely for blood donation
                                                    scheduling
                                                    and compatibility matching in accordance with data privacy regulations.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3 text-center">
                                        <p class="small text-muted mb-0">
                                            Already have an account?
                                            <a href="{{ route('login') }}" class="text-danger fw-bold">Login here</a>
                                        </p>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
