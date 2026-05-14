<aside class="sidebar-modern d-flex flex-column" id="adminSidebar">
    <style>
        /* Compact notification item styles */
        .notification-items-container .notification-item-button.compact {
            padding: .25rem .5rem;
            gap: .5rem;
            white-space: nowrap;
        }

        .notification-item-icon {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .notification-item-title {
            font-size: 0.85rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-item-time {
            font-size: 0.72rem;
            color: #6c757d;
            flex: 0 0 auto;
            margin-left: .5rem;
            white-space: nowrap;
        }

        .notification-items-container .dropdown-item.notification-empty-state {
            padding: .5rem .75rem;
        }
    </style>
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
            <div class="notification-bell-shell">
                <button
                    class="notification-bell-trigger btn w-100 d-flex align-items-center justify-content-between gap-3 position-relative"
                    type="button" id="notificationBellBtn" onclick="toggleNotificationDropdown(event)"
                    aria-expanded="false">
                    <span class="d-flex align-items-center gap-3 text-start">
                        <span class="notification-bell-icon-wrap">
                            <i class="bi bi-bell-fill"></i>
                        </span>
                        <span class="d-flex flex-column">
                            <span class="notification-bell-label">Notifications</span>
                            <small class="notification-bell-subtitle">Hospital updates and actions</small>
                        </span>
                    </span>
                    <span class="notification-bell-meta d-flex align-items-center gap-2">
                        <span class="badge notification-count-badge" id="notificationBadge"
                            style="display: none;">0</span>
                        <i class="bi bi-chevron-down notification-bell-chevron"></i>
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end w-100 notification-dropdown-panel p-0"
                    aria-labelledby="notificationBellBtn" id="notificationDropdown"
                    style="max-width: 350px; overflow: hidden;">
                    <li
                        class="notification-dropdown-header px-3 py-3 d-flex align-items-start justify-content-between gap-3">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none"
                            id="markAllReadBtn" onclick="markAllNotificationsAsRead()">
                            Mark all read
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider my-0">
                    </li>
                    <li class="px-2 py-2">
                        <div id="notificationsList" class="notification-items-container"
                            style="max-height: 320px; overflow-y: auto;">
                            <button type="button"
                                class="dropdown-item text-muted text-center py-3 w-100 notification-empty-state">
                                <small>Loading notifications...</small>
                            </button>
                        </div>
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
