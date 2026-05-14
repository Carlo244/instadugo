<aside class="sidebar-modern d-flex flex-column" id="mainSidebar">
    <style>
        /* Compact notification item styles (copied from hospital sidebar) */
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

        /* Nav pills styling (copied from public/css/user.css) */
        .nav-pills .nav-link.tab-request {
            color: var(--blood-red);
            border: 1px solid rgba(230, 57, 70, 0.2);
            margin-right: 8px;
        }

        .nav-pills .nav-link.tab-request.active {
            background-color: var(--blood-red) !important;
            color: white !important;
        }

        .nav-pills .nav-link.tab-donation {
            color: #6a040f;
            border: 1px solid rgba(106, 4, 15, 0.2);
        }

        .nav-pills .nav-link.tab-donation.active {
            background-color: #6a040f !important;
            color: white !important;
        }

        @media (max-width: 768px) {
            .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
            }
        }

        /* ================= DASHBOARD PILLS (HOSPITAL THEME ALIGNED) ================= */
        .nav-pills .nav-link {
            color: var(--blood-red);
            background: transparent;
            border: 1px solid rgba(230, 57, 70, 0.2);
            transition: all 0.25s ease;
        }

        .nav-pills .nav-link:hover {
            background: rgba(230, 57, 70, 0.08);
        }

        .nav-pills .nav-link.active {
            background-color: var(--blood-red) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(230, 57, 70, 0.25);
            border-color: var(--blood-red);
        }

        /* remove Bootstrap blue focus */
        .nav-pills .nav-link:focus {
            box-shadow: none;
        }
    </style>
    <div class="p-4 pb-0">
        <div class="d-lg-none text-end mb-2">
            <button class="btn-close" onclick="toggleSidebar()"></button>
        </div>
        <div class="text-center mb-4">
            <img src="{{ asset('logo.png') }}" class="brand-logo mb-2" alt="InstaDugo Logo">
            <h4 class="fw-bold text-black-800 mb-0">Hello, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
            <small class="fw-bold text-black-50">InstaDugo System</small>
        </div>
    </div>

    <div class="sidebar-nav-container flex-grow-1 px-4 py-2 d-flex flex-column" style="overflow-y: auto;">
        <nav class="nav flex-column gap-3 mb-4">
            <a href="{{ route('user.dashboard') }}"
                class="nav-link d-flex align-items-center {{ Request::is('user/dashboard*') && request('tab') !== 'invitations' ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
            </a>

            <a href="{{ route('user.invitations') }}"
                class="nav-link d-flex align-items-center {{ Request::is('user/invitations*') || Request::is('user/requests*') || (Request::is('user/dashboard*') && request('tab') === 'invitations') ? 'active' : '' }}">
                <i class="bi bi-envelope-fill me-2"></i> Invitations
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
                <span id="userNotificationBadge"
                    class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>

            <hr class="my-2">

            <a href="{{ route('user.profile') }}"
                class="nav-link d-flex align-items-center {{ Request::is('user/profile*') ? 'active' : '' }}">
                <i class="bi bi-person-circle me-2"></i> My Profile
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
