@extends('layouts.app')

@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100"> <!-- added w-100 -->
            <div class="col-lg-6">
                <div class="glass-card shadow-lg p-4 p-md-5">

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">Login to InstaDugo</h3>
                        <p class="small text-muted mb-0">Access your donor account and manage your donations</p>
                    </div>

                    <div class="card-body p-0">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row g-3">

                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <div class="input-group">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            id="togglePassword" style="border-left: none;">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">Remember Me</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="small text-danger">Forgot Your
                                            Password?</a>
                                    @endif
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm">
                                        Login
                                    </button>
                                </div>

                                <div class="col-12 mt-3 text-center">
                                    <p class="small text-muted mb-0">
                                        Don’t have an account?
                                        <a href="{{ route('register') }}" class="text-danger fw-bold">Register Now</a>
                                    </p>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const icon = document.querySelector('#toggleIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle the icon
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
    </script>
@endsection
