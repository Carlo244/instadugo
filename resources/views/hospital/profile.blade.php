@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <h3 class="fw-bold text-dark mb-1">Hospital Profile</h3>
        <p class="text-muted small mb-4">Update your hospital account details and credentials.</p>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="glass-card border-0 shadow-sm p-4">
            <form method="POST" action="{{ route('hospital.profile.update') }}" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Hospital Name</label>
                        <input type="text" name="hospital_name"
                            class="form-control @error('hospital_name') is-invalid @enderror"
                            value="{{ old('hospital_name', $hospital->hospital_name) }}" required>
                        @error('hospital_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $hospital->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                            value="{{ old('contact', $hospital->contact) }}">
                        @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phlebotomists On Duty</label>
                        <input type="number" name="phlebotomist_count"
                            class="form-control @error('phlebotomist_count') is-invalid @enderror"
                            value="{{ old('phlebotomist_count', $hospital->phlebotomist_count ?? 1) }}" min="1"
                            max="10" required>
                        @error('phlebotomist_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $hospital->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-2">

                    <h6 class="fw-bold mb-0">Change Password (Optional)</h6>

                    <div class="col-md-6">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="password_confirmation">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-danger px-4 rounded-pill">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
