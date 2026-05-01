@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Create User</h3>
                <p class="text-muted small mb-0">Add a new donor account from hospital admin panel.</p>
            </div>
            <a href="{{ route('hospital.manageusers') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>

        <div class="glass-card border-0 shadow-sm">
            <form method="POST" action="{{ route('hospital.manageusers.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Contact (10 digits)</label>
                        <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                            value="{{ old('contact') }}" required>
                        @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" min="18"
                            class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}" required>
                        @error('age')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sex</label>
                        <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="Male" @selected(old('sex') === 'Male')>Male</option>
                            <option value="Female" @selected(old('sex') === 'Female')>Female</option>
                        </select>
                        @error('sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Blood Type</label>
                        <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror" required>
                            <option value="">Select</option>
                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                <option value="{{ $type }}" @selected(old('blood_type') === $type)>{{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('blood_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                            value="{{ old('address') }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('hospital.manageusers') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-danger">Create User</button>
                </div>
            </form>
        </div>
    </main>
@endsection
