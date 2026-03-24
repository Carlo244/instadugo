<aside class="sidebar-modern d-flex flex-column" id="adminSidebar">
    <div class="p-4 pb-0">
        <div class="d-lg-none text-end mb-2">
            <button class="btn-close" onclick="toggleSidebar()"></button>
        </div>
        <div class="text-center mb-4">
            <img src="{{ asset('logo.png') }}" class="brand-logo mb-2" alt="InstaDugo Logo">
            <h4 class="fw-bold text-black-800 mb-0">Hospital Admin</h4>
            <small class="fw-bold text-black-50">InstaDugo System</small>
        </div>
    </div>

    <div class="sidebar-nav-container flex-grow-1 px-4 py-2 d-flex flex-column" style="overflow-y: auto;">
        <nav class="nav flex-column gap-3 mb-4">
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

            <a href="{{ route('hospital.manageusers') }}"
                class="nav-link d-flex align-items-center {{ Request::is('hospital/manageusers*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Manage Users
            </a>

            <a href="{{ route('hospital.reports') }}"
                class="nav-link d-flex align-items-center {{ Request::is('hospital/reports*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data me-2"></i> Reports
            </a>

            <hr class="my-2">

            <a href="{{ route('hospital.profile') }}"
                class="nav-link d-flex align-items-center {{ Request::is('hospital/profile*') ? 'active' : '' }}">
                <i class="bi bi-person-circle me-2"></i> Profile
            </a>
        </nav>

        <div class="mt-auto pt-4 pb-4 sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="fw-bold">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
