<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaDugo | Blood Donation & Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/landingpage.css') }}" rel="stylesheet">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <img src="{{ asset('logo.png') }}" class="brand-logo" alt="InstaDugo Logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#process">How it Works</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#">About Us</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger ms-lg-3"
                            href="{{ route('login') }}">{{ __('Login') }}</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Save a Life in <span>Real-Time.</span></h1>
            <p class="lead mb-5 text-white-50">A Web-Based Blood Donation and Compatibility Matching System <br>
                powered by Multilevel Queue and Rule-Based Algorithms.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-donate btn-lg">Donate Now</a>
                <a href="{{ route('login') }}" class="btn btn-request btn-lg">Request Blood Support</a>
            </div>
        </div>
    </header>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Our Impact So Far</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="glass-card stat-card">
                        <h3 class="fw-bold text-danger">3,200+</h3>
                        <p class="text-muted mb-0">Lives Saved</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card stat-card">
                        <h3 class="fw-bold text-success">1,402</h3>
                        <p class="text-muted mb-0">Successful Matches</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card stat-card">
                        <h3 class="fw-bold text-primary">850+</h3>
                        <p class="text-muted mb-0">Active Donors</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card stat-card">
                        <h3 class="fw-bold text-warning">120+</h3>
                        <p class="text-muted mb-0">Hospitals & Blood Centers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- How It Works Section -->
    <section id="process" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">How the System Works</h2>
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="step-number">1</div>
                        <h5 class="fw-bold mt-2">Easy Registration</h5>
                        <p class="text-muted">Create a profile as a donor or recipient. Input your blood type and health
                            details to join the centralized database.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="step-number">2</div>
                        <h5 class="fw-bold mt-2">Smart Scheduling</h5>
                        <p class="text-muted">Book an appointment online. Our <strong>Multilevel Queue</strong>
                            prioritizes
                            urgent cases and rare blood types instantly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="step-number">3</div>
                        <h5 class="fw-bold mt-2">Automated Matching</h5>
                        <p class="text-muted">The <strong>Rule-Based Algorithm</strong> verifies ABO/Rh compatibility,
                            ensuring safe and accurate matches for transfusions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container text-center">
            <p class="mb-1">&copy; 2024 InstaDugo - Emilio Aguinaldo College Cavite</p>
            <small>Developed by: Balinado, Lapuz, Montemayor, Sanchez</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
