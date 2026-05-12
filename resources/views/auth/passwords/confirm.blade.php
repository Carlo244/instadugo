@extends('layouts.app')

@section('content')
    <div class="auth-scene d-flex align-items-center justify-content-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="glass-card auth-panel shadow-lg p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-1">Confirm Password</h3>
                            <p class="small text-muted mb-0">Please confirm your password before continuing.</p>
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div
                                    class="col-12 mt-4 d-flex flex-column flex-sm-row gap-2 justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-danger fw-bold px-4">
                                        {{ __('Confirm Password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link text-decoration-none text-danger"
                                            href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
