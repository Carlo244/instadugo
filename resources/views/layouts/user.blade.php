<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} | User Dashboard</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">

    <link rel="icon" href="{{ asset('favicon.ico') }}?v=1">
</head>

<body>
    <div id="dashboard-wrapper">
        @include('layouts.user-sidebar')
        <div>
            <button id="sidebarToggler" class="sidebar-toggler d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-3 text-danger"></i>
            </button>

            <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
            @yield('content')
        </div>
    </div>
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationsModalLabel">
                        <i class="bi bi-bell me-2"></i> Your Notifications
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse (auth()->user()->unreadNotifications()->latest()->take(10)->get() as $notification)
                            <a href="{{ $notification->data['link'] ?? '#' }}"
                                class="list-group-item list-group-item-action {{ $notification->read_at ? 'opacity-75' : 'bg-light border-start border-danger border-4' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1 {{ $notification->read_at ? '' : 'fw-bold text-danger' }}">
                                        {{-- If you didn't add a title key, we can use a default --}}
                                        {{ $notification->data['title'] ?? 'InstaDugo Alert' }}
                                    </h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>

                                {{-- This matches the 'message' key we set in your Notification class --}}
                                <p class="mb-1 small">{{ $notification->data['message'] ?? 'You have a new update.' }}
                                </p>

                                @if (isset($notification->data['priority']) && $notification->data['priority'] === 'urgent')
                                    <span class="badge bg-danger">Urgent</span>
                                @endif
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bell-slash d-block mb-2" style="font-size: 2rem;"></i>
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>

                    {{-- Form to mark all as read --}}
                    <form action="{{ route('user.notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Mark all as read</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar-modern');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');

            // Set a data attribute on the body to tell CSS the sidebar is open
            if (sidebar.classList.contains('show')) {
                body.setAttribute('data-sidebar-open', 'true');
            } else {
                body.removeAttribute('data-sidebar-open');
            }
        }
    </script>
</body>

</html>
