<!-- ADMIN SIDEBAR -->
<aside class="sidebar-modern d-none d-lg-flex flex-column p-4">
    <div class="text-center mb-5">
        <img src="{{ asset('logo.png') }}" class="brand-logo mb-2" alt="InstaDugo Logo">
        <h5 class="fw-bold text-white mb-0">Admin Panel</h5>
        <small class="fw-bold text-white-50">InstaDugo System</small>
    </div>

    <!-- NAVIGATION -->
    <nav class="nav flex-column gap-3 sidebar-nav">

        <a href="{{ route('hospital.dashboard') }}"
            class="nav-link d-flex align-items-center {{ Request::is('hospital/dashboard*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <a href="{{ route('hospital.requests') }}"
            class="nav-link d-flex align-items-center {{ Request::is('hospital/requests*') ? 'active' : '' }}">
            <i class="bi bi-droplet-fill me-2"></i> Blood Requests
        </a>

        <a href="{{ route('hospital.donations') }}"
            class="nav-link d-flex align-items-center {{ Request::is('hospital/donations*') ? 'active' : '' }}">
            <i class="bi bi-heart-pulse-fill me-2"></i> Donations
        </a>

        <a href=""
            class="nav-link d-flex align-items-center {{ Request::is('hospital/users*') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i> Manage Users
        </a>

        <a href=""
            class="nav-link d-flex align-items-center {{ Request::is('hospital/reports*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data me-2"></i> Reports
        </a>

        <hr>

        <a href="" class="nav-link d-flex align-items-center">
            <i class="bi bi-person-circle me-2"></i> Profile
        </a>

    </nav>

    <!-- LOGOUT -->
    <div class="mt-auto pt-4 sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
