<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} | Hospital Dashboard</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/hospital.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=1">
    <script>
        window.hospitalAdminId = @json(auth('hospital_admin')->id());
    </script>
    <style>
        /* highlight animation for newly inserted donation rows */
        .flash-new {
            animation: flashRow 2.5s ease forwards;
        }

        @keyframes flashRow {
            0% {
                background-color: #e6fff2;
            }

            50% {
                background-color: #fff7d9;
            }

            100% {
                background-color: transparent;
            }
        }
    </style>
    @vite('resources/js/app.js')
</head>

<body>
    <button class="sidebar-toggler" id="sidebarToggler">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div id="dashboard-wrapper">
        @include('layouts.hospital-sidebar')
        <div>
            @yield('content')
        </div>
    </div>
    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    {{-- <script>
        const toggler = document.getElementById("sidebarToggler");
        const sidebar = document.getElementById("adminSidebar");
        const overlay = document.getElementById("sidebarOverlay");

        toggler.addEventListener("click", () => {
            sidebar.classList.add("show");
            overlay.classList.add("show");
            toggler.classList.add("inactive");
        });

        overlay.addEventListener("click", () => {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
            toggler.classList.remove("inactive");
        });
    </script> --}}
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>
</body>


</html>
