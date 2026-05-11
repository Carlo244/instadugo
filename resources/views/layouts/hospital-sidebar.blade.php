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
        
        <!-- Notification Bell -->
        <div class="notification-bell-container mb-3">
            <div class="dropdown w-100">
                <button class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 position-relative"
                    type="button" id="notificationBellBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <span class="fw-bold">Notifications</span>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle-x" 
                        id="notificationBadge" style="display: none;">0</span>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="notificationBellBtn" id="notificationDropdown" style="max-width: 350px;">
                    <li class="dropdown-header">Recent Updates</li>
                    <li><hr class="dropdown-divider"></li>
                    <div id="notificationsList" class="notification-items-container" style="max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-item text-muted text-center py-3">
                            <small>Loading notifications...</small>
                        </li>
                    </div>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item small text-center" id="markAllReadBtn" onclick="markAllNotificationsAsRead()">
                            Mark all as read
                        </button>
                    </li>
                </ul>
            </div>
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
