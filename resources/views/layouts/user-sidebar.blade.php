<!-- USER SIDEBAR -->
<aside class="sidebar-modern d-none d-lg-flex flex-column p-4">
    <div class="text-center mb-5">
        <img src="{{ asset('logo.png') }}" class="brand-logo mb-2" alt="InstaDugo Logo">
        <h4 class="fw-bold text-black-800 mb-0">Hello, {{ explode(' ', auth()->user()->name)[0] }} !</h4>
        <small class="fw-bold text-black-50">InstaDugo System</small>
    </div>

    <!-- NAVIGATION -->
    <nav class="nav flex-column gap-4 sidebar-nav">
        <a href="{{ route('user.dashboard') }}"
            class="nav-link d-flex align-items-center {{ Request::is('user/dashboard*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
        </a>
        <a href="{{ route('user.blood-requests') }}"
            class="nav-link d-flex align-items-center {{ Request::is('user/blood-requests*') ? 'active' : '' }}">
            <i class="bi bi-droplet-fill me-2"></i> Request Blood
        </a>
        <a href="{{ route('user.donate-schedule') }}"
            class="nav-link d-flex align-items-center {{ Request::is('user/donate-schedule*') ? 'active' : '' }}">
            <i class="bi bi-heart-pulse-fill me-2"></i> Donate / Schedule
        </a>
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="modal"
            data-bs-target="#notificationsModal">
            <span><i class="bi bi-bell-fill me-2"></i> Notifications</span>
            <span class="badge bg-danger rounded-pill">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        </a>
        <hr>
        <a href="{{ route('user.profile') }}"
            class="nav-link d-flex align-items-center {{ Request::is('user/profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle me-2"></i> My Profile
        </a>
    </nav>

    <!-- LOGOUT -->
    <div class="mt-auto pt-4 sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
