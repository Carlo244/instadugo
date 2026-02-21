<!-- USER SIDEBAR -->
<aside class="sidebar-modern d-none d-lg-flex flex-column p-4">
    <div class="text-center mb-5">
        <img src="{{ asset('logo.png') }}" class="brand-logo mb-2" alt="InstaDugo Logo">
        <h5 class="fw-bold text-white mb-0">Admin Panel</h5>
        <small class="fw-bold text-white-50">InstaDugo System</small>
    </div>

    <!-- NAVIGATION -->
    <nav class="nav flex-column gap-3 sidebar-nav">
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
        <a href="{{ route('user.dashboard') }}#notifications" id="nav-notifications"
            class="nav-link d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bell-fill me-2"></i> Notifications</span>
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
            <button class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
<script>
    function checkHash() {
        const navNotif = document.getElementById('nav-notifications');
        if (window.location.hash === '#notifications') {
            // Remove active from others, add to this one
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            navNotif.classList.add('active');
        }
    }

    // Run on page load and whenever the hash changes
    window.addEventListener('hashchange', checkHash);
    window.addEventListener('load', checkHash);
</script>
