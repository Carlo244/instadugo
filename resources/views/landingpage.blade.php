<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaDugo | Blood Donation & Matching</title>
    <meta name="description"
        content="InstaDugo helps donors, recipients, and hospitals manage blood requests, donation schedules, and compatibility matching.">
    <meta property="og:title" content="InstaDugo | Blood Donation & Matching">
    <meta property="og:description"
        content="Blood request, donation scheduling, and hospital coordination platform with rule-based matching.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/landingpage.css') }}" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a href="#" aria-label="InstaDugo Home">
                <img src="{{ asset('logo.png') }}" class="brand-logo" alt="InstaDugo Logo">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link px-3 section-link" href="#process"><i
                                class="bi bi-diagram-3 me-1"></i>How it Works</a></li>
                    <li class="nav-item"><a class="nav-link px-3 section-link" href="#about"><i
                                class="bi bi-info-circle me-1"></i>About</a></li>
                    <li class="nav-item"><a class="nav-link px-3 section-link" href="#eligibility"><i
                                class="bi bi-clipboard-pulse me-1"></i>Eligibility</a></li>
                    <li class="nav-item"><a class="nav-link px-3 section-link" href="#faq"><i
                                class="bi bi-chat-square-text me-1"></i>FAQ</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-danger btn-sm rounded-pill px-3" href="{{ route('login') }}">
                            <i class="bi bi-exclamation-circle me-1"></i>Emergency Request
                        </a>
                    </li>
                    <li class="nav-item"><a class="btn btn-outline-danger ms-lg-2" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <p class="hero-badge mb-3"><i class="bi bi-clock-history me-1"></i>Blood Request & Donation Coordination</p>
            <h1 class="display-3 fw-bold mb-3">Save a Life with <span>Live Coordination.</span></h1>
            <p class="lead mb-2 text-white-50">A Web-Based Blood Request, Donation Scheduling, and Compatibility
                Matching System</p>
            <p class="hero-tech-badge mb-5">Built with Multilevel Queue Prioritization and Rule-Based Matching</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-donate btn-lg">
                    <i class="bi bi-heart-fill me-2"></i>I want to Donate
                </a>
                <a href="{{ route('login') }}" class="btn btn-request btn-lg">
                    <i class="bi bi-droplet-half me-2"></i>I need Blood
                </a>
            </div>
            <p class="mt-4 hero-sub-note"><i class="bi bi-shield-check me-1"></i>Request tracking &middot; Hospital
                coordination
                &middot; Account-based access</p>
        </div>
    </header>

    <!-- Trust Strip -->
    <section class="trust-strip">
        <div class="container">
            <div class="row g-3 g-md-4">
                <div class="col-6 col-md">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-hospital"></i></div>
                        <div>
                            <div class="trust-label">Partner Hospitals</div>
                            <div class="trust-desc">Hospital Coordination</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-shield-lock"></i></div>
                        <div>
                            <div class="trust-label">Stored Records</div>
                            <div class="trust-desc">Account-Based Access</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-lightning-charge"></i></div>
                        <div>
                            <div class="trust-label">Queue Prioritization</div>
                            <div class="trust-desc">Urgency-Based Sorting</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-bell"></i></div>
                        <div>
                            <div class="trust-label">Live Notifications</div>
                            <div class="trust-desc">Request & Schedule Updates</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="trust-label">Cavite-Based</div>
                            <div class="trust-desc">Cavite-Based</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-5">
        <div class="container text-center">
            <span class="section-eyebrow">Our Impact</span>
            <h2 class="fw-bold mb-2">Growing Every Day</h2>
            <p class="text-muted mb-5">Current counts based on the platform records.</p>
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="glass-card stat-card h-100">
                        <div class="stat-icon"><i class="bi bi-droplet-fill"></i></div>
                        <h3 class="fw-bold stat-number" data-target="{{ $landingStats['fulfilled_requests'] }}">0</h3>
                        <p class="text-muted mb-0 small">Fulfilled Requests</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="glass-card stat-card h-100">
                        <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <h3 class="fw-bold stat-number" data-target="{{ $landingStats['successful_matches'] }}">0</h3>
                        <p class="text-muted mb-0 small">Successful Matches</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="glass-card stat-card h-100">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <h3 class="fw-bold stat-number" data-target="{{ $landingStats['active_donors'] }}">0</h3>
                        <p class="text-muted mb-0 small">Registered Donors</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="glass-card stat-card h-100">
                        <div class="stat-icon"><i class="bi bi-hospital"></i></div>
                        <h3 class="fw-bold stat-number" data-target="{{ $landingStats['partners'] }}">0
                        </h3>
                        <p class="text-muted mb-0 small">Partner Hospitals</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Eligibility Section -->
    <section id="eligibility" class="py-5 section-alt">
        <div class="container">
            <span class="section-eyebrow">Can I Donate?</span>
            <h2 class="text-center fw-bold mb-2">Quick Eligibility Check</h2>
            <p class="text-center text-muted mb-5">Most healthy adults qualify. Here's a quick overview.</p>
            <div class="row g-4 text-center">
                <div class="col-md-3 col-sm-6">
                    <div class="glass-card h-100 eligibility-card">
                        <div class="eligibility-icon"><i class="bi bi-heart-pulse"></i></div>
                        <h5 class="fw-bold mt-3 mb-2">General Health</h5>
                        <p class="text-muted mb-0 small">You should be feeling well and fit on the day of donation with
                            no active illness.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="glass-card h-100 eligibility-card">
                        <div class="eligibility-icon"><i class="bi bi-person-check"></i></div>
                        <h5 class="fw-bold mt-3 mb-2">Age & Weight</h5>
                        <p class="text-muted mb-0 small">Must be at least 18 years old and weigh at least 50 kg (110
                            lbs).</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="glass-card h-100 eligibility-card">
                        <div class="eligibility-icon"><i class="bi bi-calendar2-check"></i></div>
                        <h5 class="fw-bold mt-3 mb-2">Donation Interval</h5>
                        <p class="text-muted mb-0 small">At least 56 days (8 weeks) must pass since your last completed
                            whole blood donation.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="glass-card h-100 eligibility-card">
                        <div class="eligibility-icon"><i class="bi bi-hospital"></i></div>
                        <h5 class="fw-bold mt-3 mb-2">Medical Clearance</h5>
                        <p class="text-muted mb-0 small">Final eligibility is always confirmed by partner medical staff
                            at the donation site.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('register') }}" class="btn btn-donate rounded-pill px-4">
                    <i class="bi bi-check2-circle me-2"></i>Register & Get Verified
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-eyebrow">Why InstaDugo</span>
                    <h2 class="fw-bold mb-4">Faster Blood Coordination for Hospitals and Donors.</h2>
                    <p class="text-muted mb-4">
                        InstaDugo connects donors, recipients, and partner hospitals in one platform so blood requests
                        and donation schedules can be organized and tracked in one place. Built for Emilio Aguinaldo
                        College Cavite with hospital coordination and request tracking in mind.
                    </p>
                    <ul class="about-feature-list list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Urgency-based multilevel queue
                            prioritization</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>ABO/Rh rule-based compatibility
                            matching</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Donation scheduling and reminder
                            notifications</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Real-time notifications to matched
                            donors</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Secure hospital admin coordination
                            portal</li>
                        <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Transparent request status tracking
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="glass-card about-highlight-card text-center">
                        <div class="about-icon-ring mb-3">
                            <i class="bi bi-droplet-half"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Every Second Counts</h5>
                        <p class="text-muted mb-3">
                            Blood has a limited shelf life. Our system helps hospitals and donors coordinate the right
                            blood requests, donation schedules, and request priorities more efficiently.
                        </p>
                        <div class="row g-3 text-start">
                            <div class="col-6">
                                <div class="about-mini-stat">
                                    <i class="bi bi-lightning text-danger"></i>
                                    <span>Fast matching</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="about-mini-stat">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <span>Safe process</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="about-mini-stat">
                                    <i class="bi bi-bell text-primary"></i>
                                    <span>Instant alerts</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="about-mini-stat">
                                    <i class="bi bi-eye text-warning"></i>
                                    <span>Full visibility</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="process" class="py-5">
        <div class="container">
            <span class="section-eyebrow">The Process</span>
            <h2 class="text-center fw-bold mb-2">How the System Works</h2>
            <p class="text-center text-muted mb-5">Simple steps from registration to successful donation.</p>
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="glass-card process-card h-100">
                        <div class="step-circle"><i class="bi bi-person-plus-fill"></i></div>
                        <div class="step-label">Step 1</div>
                        <h5 class="fw-bold mt-2">Create Your Account</h5>
                        <p class="text-muted small">Register as a donor, recipient, or hospital admin depending on your
                            role in the system.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card process-card h-100">
                        <div class="step-circle"><i class="bi bi-calendar2-event-fill"></i></div>
                        <div class="step-label">Step 2</div>
                        <h5 class="fw-bold mt-2">Queue-Based Request Entry</h5>
                        <p class="text-muted small">Submit a blood request or donation schedule. The <strong>Multilevel
                                Queue</strong> organizes requests by urgency and blood type priority.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card process-card h-100">
                        <div class="step-circle"><i class="bi bi-patch-check-fill"></i></div>
                        <div class="step-label">Step 3</div>
                        <h5 class="fw-bold mt-2">Rule-Based Matching</h5>
                        <p class="text-muted small">The <strong>Rule-Based Algorithm</strong> checks ABO/Rh
                            compatibility and notifies the matching donor based on the stored rules.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 section-alt">
        <div class="container" style="max-width:800px">
            <span class="section-eyebrow">Got Questions?</span>
            <h2 class="text-center fw-bold mb-5">Frequently Asked Questions</h2>
            <div class="accordion faq-accordion" id="landingFaq">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                            <i class="bi bi-person-check me-2 text-danger"></i>Who can register as a donor?
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#landingFaq">
                        <div class="accordion-body text-muted">
                            Anyone aged 18 and above weighing at least 50 kg, who is in good health and meets the
                            donation interval requirement can register. Final eligibility is confirmed by partner
                            medical staff.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                            <i class="bi bi-sort-numeric-up me-2 text-danger"></i>How are urgent requests prioritized?
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#landingFaq">
                        <div class="accordion-body text-muted">
                            The system uses a <strong>Multilevel Queue</strong> that ranks requests by urgency level
                            (<strong>Emergency</strong>, <strong>High</strong>, <strong>Normal</strong>) and blood type
                            priority.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                            <i class="bi bi-hospital me-2 text-danger"></i>Do I need to go to the hospital in person?
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#landingFaq">
                        <div class="accordion-body text-muted">
                            Yes. InstaDugo is used for coordination and tracking, while the actual donation and
                            fulfillment take place at a partner hospital or blood center.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqFour" aria-expanded="false" aria-controls="faqFour">
                            <i class="bi bi-shield-lock me-2 text-danger"></i>Is my personal information safe?
                        </button>
                    </h2>
                    <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#landingFaq">
                        <div class="accordion-body text-muted">
                            Absolutely. Your data is stored securely and only shared with verified hospital partners
                            involved in your request. We follow strict data privacy standards and never sell personal
                            information.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Band -->
    <section class="py-5">
        <div class="container">
            <div class="cta-band text-center">
                <h2 class="fw-bold mb-2">Ready to make a difference?</h2>
                <p class="mb-4 opacity-75">Join hundreds of donors and recipients already on the platform.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-donate btn-lg rounded-pill px-5">
                        <i class="bi bi-heart-fill me-2"></i>Join as Donor
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-request btn-lg rounded-pill px-5">
                        <i class="bi bi-droplet-half me-2"></i>Request Blood
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4">
                    <img src="{{ asset('logo.png') }}" class="footer-logo mb-3" alt="InstaDugo Logo">
                    <p class="footer-brand-text mb-3">A blood donation and compatibility matching system for blood
                        requests, donation scheduling, and hospital coordination in Cavite.</p>
                    <div class="footer-badges">
                        <span class="footer-badge"><i class="bi bi-shield-check"></i> Hospital Coordination</span>
                        <span class="footer-badge"><i class="bi bi-lightning-charge"></i> Queue-Based Matching</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="footer-heading">Navigate</h6>
                    <ul class="list-unstyled footer-nav mb-0">
                        <li><a href="#about" class="footer-link">About</a></li>
                        <li><a href="#process" class="footer-link">How it Works</a></li>
                        <li><a href="#eligibility" class="footer-link">Eligibility</a></li>
                        <li><a href="#faq" class="footer-link">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="footer-heading">Account</h6>
                    <ul class="list-unstyled footer-nav mb-0">
                        <li><a href="{{ route('login') }}" class="footer-link">Login</a></li>
                        <li><a href="{{ route('register') }}" class="footer-link">Register as Donor</a></li>
                        <li><a href="{{ route('login') }}" class="footer-link">Request Blood</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h6 class="footer-heading">Contact</h6>
                    <ul class="list-unstyled footer-contact-list mb-0">
                        <li>
                            <i class="bi bi-envelope"></i>
                            <span>Email: <a href="mailto:instadugo@gmail.com"
                                    class="footer-link">instadugo@gmail.com</a></span>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt"></i>
                            <span>Location: Emilio Aguinaldo College, Cavite</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="footer-divider">
            <div class="footer-meta d-flex flex-wrap justify-content-between align-items-center gap-2">
                <small class="footer-meta-text">&copy; {{ date('Y') }} InstaDugo. All rights reserved.</small>
                <small class="footer-meta-text">Developed by: Sanchez, Balinado, Lapuz &amp; Montemayor</small>
            </div>
        </div>
    </footer>

    <!-- Mobile Sticky CTA -->
    <div class="mobile-cta d-md-none">
        <a href="{{ route('register') }}" class="btn btn-donate">
            <i class="bi bi-heart-fill me-1"></i>Donate
        </a>
        <a href="{{ route('login') }}" class="btn btn-request">
            <i class="bi bi-droplet-half me-1"></i>Request
        </a>
    </div>

    <!-- Scroll to Top -->
    <button id="scrollTopBtn" class="scroll-top-btn" aria-label="Scroll to top" title="Back to top">
        <i class="bi bi-arrow-up-short"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Active nav link on scroll ──────────────────────────────
            const sectionLinks = document.querySelectorAll('.section-link');
            const sections = Array.from(sectionLinks)
                .map((link) => document.querySelector(link.getAttribute('href')))
                .filter(Boolean);

            const activateLink = (id) => {
                sectionLinks.forEach((link) => {
                    link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
                });
            };

            const navObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) activateLink(entry.target.id);
                });
            }, {
                rootMargin: '-35% 0px -55% 0px',
                threshold: 0
            });

            sections.forEach((section) => navObserver.observe(section));

            // ── Animated count-up for stats ───────────────────────────
            const statNumbers = document.querySelectorAll('.stat-number');
            const countUp = (el) => {
                const target = parseInt(el.dataset.target, 10) || 0;
                if (target === 0) {
                    el.textContent = '0';
                    return;
                }
                const duration = 1600;
                const step = Math.ceil(target / (duration / 16));
                let current = 0;
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current.toLocaleString();
                    if (current >= target) clearInterval(timer);
                }, 16);
            };

            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        countUp(entry.target);
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            statNumbers.forEach((el) => statsObserver.observe(el));

            // ── Scroll-to-top button ──────────────────────────────────
            const scrollBtn = document.getElementById('scrollTopBtn');
            window.addEventListener('scroll', () => {
                scrollBtn.classList.toggle('visible', window.scrollY > 400);
            });
            scrollBtn.addEventListener('click', () => window.scrollTo({
                top: 0,
                behavior: 'smooth'
            }));
        });
    </script>
</body>

</html>
