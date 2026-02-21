@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="glass-card p-4 text-center shadow-sm rounded-4">

                    <div class="mb-3">
                        <i class="bi bi-envelope-check-fill text-danger fs-1"></i>
                    </div>

                    <h4 class="fw-bold mb-2">Verify Your Email Address</h4>
                    <p class="text-muted">
                        Thanks for registering with InstaDugo!
                        Please check your email and click the verification link to activate your account.
                    </p>

                    @if (session('resent'))
                        <div class="alert alert-success mt-3">
                            ✅ A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <div class="border-top pt-3 mt-3">
                        <p class="mb-2">Didn’t receive the email?</p>

                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                Resend Verification Email
                            </button>
                        </form>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="text-decoration-none text-muted">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
