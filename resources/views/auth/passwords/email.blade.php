@extends('layouts.app')

@section('content')
    <div class="auth-scene d-flex align-items-center justify-content-center">
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="row justify-content-center w-100">
                <div class="col-lg-5">
                    <div class="glass-card auth-panel shadow-lg p-4 p-md-5">

                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-1">Forgot Password?</h3>
                            <p class="small text-muted mb-0">Enter your email and we'll send you a link to reset your
                                password.
                            </p>
                        </div>

                        <div class="card-body p-0">
                            @if (session('status'))
                                <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-envelope text-muted"></i>
                                            </span>
                                            <input id="email" type="email"
                                                class="form-control border-start-0 @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" placeholder="name@example.com"
                                                required autofocus>
                                        </div>
                                        @error('email')
                                            <span class="text-danger small mt-1 d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm">
                                            Send Reset Link
                                        </button>
                                    </div>

                                    <div class="col-12 mt-3 text-center">
                                        <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                                            <i class="bi bi-arrow-left me-1"></i> Back to Login
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
