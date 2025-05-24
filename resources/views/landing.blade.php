<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoTracker') }} - Environmental Monitoring Platform</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Inter:300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --eco-primary: #2d5a27;
            --eco-secondary: #4a7c59;
            --eco-accent: #7fb069;
            --eco-light: #d6eadf;
            --eco-dark: #1a3a17;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-eco: linear-gradient(135deg, #2d5a27 0%, #7fb069 50%, #4a7c59 100%);
            --gradient-hero: linear-gradient(135deg, rgba(45, 90, 39, 0.9) 0%, rgba(127, 176, 105, 0.8) 50%, rgba(74, 124, 89, 0.9) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: #f8fffe;
        }

        /* Animated Background */
        .hero-section {
            min-height: 100vh;
            position: relative;
            background: var(--gradient-hero);
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><radialGradient id="grad1" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:%23ffffff;stop-opacity:0.1" /><stop offset="100%" style="stop-color:%23ffffff;stop-opacity:0" /></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;50,30;0,0" dur="10s" repeatCount="indefinite"/></circle><circle cx="800" cy="300" r="150" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;-30,50;0,0" dur="15s" repeatCount="indefinite"/></circle><circle cx="400" cy="600" r="80" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;40,-20;0,0" dur="12s" repeatCount="indefinite"/></circle></svg>');
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand {
            color: var(--eco-primary) !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .nav-link {
            color: var(--eco-dark) !important;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .btn-gradient {
            background: var(--gradient-eco);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(45, 90, 39, 0.4);
            color: white;
        }

        .btn-outline-gradient {
            background: transparent;
            border: 2px solid white;
            color: white;
            font-weight: 600;
            padding: 10px 28px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-outline-gradient:hover {
            background: white;
            color: var(--eco-primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding-top: 120px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .icon-gradient-1 { background: var(--gradient-1); }
        .icon-gradient-2 { background: var(--gradient-2); }
        .icon-gradient-3 { background: var(--gradient-3); }
        .icon-gradient-eco { background: var(--gradient-eco); }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--eco-primary);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 3rem;
        }

        .stats-card {
            background: var(--gradient-eco);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
        }

        .cta-section {
            background: var(--gradient-hero);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><path d="M0,300 Q300,100 600,300 T1200,300 L1200,600 L0,600 Z" fill="%23ffffff" opacity="0.05"/></svg>');
            animation: wave 15s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(-50px); }
        }

        .footer {
            background: var(--eco-dark);
            color: white;
            padding: 50px 0 30px;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .feature-card {
                padding: 2rem;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                <i class="bi bi-globe-americas me-2"></i>
                <span>EcoTracker</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#impact">Impact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-gradient">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="{{ route('register') }}" class="btn btn-gradient">
                            <i class="bi bi-person-plus me-2"></i>Join Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-right">
                        <h1 class="hero-title">
                            Protect Our
                            <span class="d-block">Environment</span>
                            <span class="d-block text-warning">Together</span>
                        </h1>
                        <p class="hero-subtitle">
                            Join thousands of environmental stewards in monitoring, reporting, and addressing environmental issues in your community. Every report makes a difference.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="btn btn-gradient btn-lg pulse">
                                <i class="bi bi-rocket-takeoff me-2"></i>Get Started Free
                            </a>
                            <a href="#features" class="btn btn-outline-gradient btn-lg">
                                <i class="bi bi-play-circle me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="text-center">
                        <div class="position-relative">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                                 style="width: 300px; height: 300px; animation: float 6s ease-in-out infinite;">
                                <i class="bi bi-globe-americas" style="font-size: 8rem; color: var(--eco-primary);"></i>
                            </div>
                            <div class="position-absolute top-0 start-0 w-100 h-100">
                                <div class="position-absolute" style="top: 20%; left: 10%; animation: float 4s ease-in-out infinite reverse;">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-droplet text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="position-absolute" style="top: 60%; right: 15%; animation: float 5s ease-in-out infinite;">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-tree text-white" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div class="position-absolute" style="bottom: 20%; left: 20%; animation: float 7s ease-in-out infinite reverse;">
                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px;">
                                        <i class="bi bi-wind text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5" style="padding: 100px 0;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up">Powerful Features</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Everything you need to make a real environmental impact
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-1">
                            <i class="bi bi-camera"></i>
                        </div>
                        <h4 class="mb-3">Photo Documentation</h4>
                        <p class="text-muted">
                            Capture and upload multiple photos of environmental issues with automatic location tagging for comprehensive documentation.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-2">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4 class="mb-3">GPS Location Tracking</h4>
                        <p class="text-muted">
                            Automatically detect and record precise locations of environmental incidents for accurate mapping and response coordination.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="mb-3">Anonymous Reporting</h4>
                        <p class="text-muted">
                            Report environmental issues anonymously when needed, ensuring community safety while maintaining transparency.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-eco">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="mb-3">Real-time Analytics</h4>
                        <p class="text-muted">
                            Track environmental trends, monitor progress, and view detailed analytics of community environmental health.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-1">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="mb-3">Community Collaboration</h4>
                        <p class="text-muted">
                            Connect with local environmental groups, authorities, and fellow citizens to coordinate cleanup efforts and solutions.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="700">
                    <div class="feature-card">
                        <div class="feature-icon icon-gradient-2">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4 class="mb-3">Smart Notifications</h4>
                        <p class="text-muted">
                            Receive updates on reported issues, resolution progress, and environmental alerts in your area.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light" style="padding: 100px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="section-title">About EcoTracker</h2>
                    <p class="section-subtitle">Empowering communities to protect our planet</p>

                    <div class="mb-4">
                        <h5 class="text-eco-primary mb-3">Our Mission</h5>
                        <p class="text-muted">
                            EcoTracker bridges the gap between environmental incidents and effective responses by providing communities with accessible tools to document, track, and address local environmental challenges.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-eco-primary mb-3">How It Works</h5>
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; min-width: 40px;">
                                <span class="text-white fw-bold">1</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Report Issues</h6>
                                <p class="text-muted small mb-0">Easily document environmental problems with photos and location data</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; min-width: 40px;">
                                <span class="text-white fw-bold">2</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Track Progress</h6>
                                <p class="text-muted small mb-0">Monitor the status and resolution of reported environmental issues</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; min-width: 40px;">
                                <span class="text-white fw-bold">3</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Make Impact</h6>
                                <p class="text-muted small mb-0">See real environmental improvements in your community</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="stats-card">
                                <div class="stats-number">10K+</div>
                                <div>Reports Filed</div>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="stats-card">
                                <div class="stats-number">500+</div>
                                <div>Communities</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">85%</div>
                                <div>Issues Resolved</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">24/7</div>
                                <div>Monitoring</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section id="impact" class="py-5" style="padding: 100px 0;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up">Environmental Categories</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Track and address various types of environmental issues
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <div class="feature-icon icon-gradient-1 mx-auto">
                            <i class="bi bi-trash"></i>
                        </div>
                        <h5 class="mb-3">Illegal Dumping</h5>
                        <p class="text-muted small">Report unauthorized waste disposal and help keep communities clean</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="feature-card text-center">
                        <div class="feature-icon icon-gradient-2 mx-auto">
                            <i class="bi bi-droplet"></i>
                        </div>
                        <h5 class="mb-3">Water Pollution</h5>
                        <p class="text-muted small">Monitor water quality and report contamination incidents</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="400">
                    <div class="feature-card text-center">
                        <div class="feature-icon icon-gradient-3 mx-auto">
                            <i class="bi bi-cloud-haze"></i>
                        </div>
                        <h5 class="mb-3">Air Quality</h5>
                        <p class="text-muted small">Track air pollution and help improve community health</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="500">
                    <div class="feature-card text-center">
                        <div class="feature-icon icon-gradient-eco mx-auto">
                            <i class="bi bi-tree"></i>
                        </div>
                        <h5 class="mb-3">Habitat Protection</h5>
                        <p class="text-muted small">Protect local wildlife and natural habitats</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center" data-aos="fade-up">
                    <h2 class="display-4 fw-bold mb-4">Ready to Make a Difference?</h2>
                    <p class="lead mb-5">
                        Join thousands of environmental stewards and start protecting your community today.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-globe-americas me-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-0">EcoTracker</h4>
                    </div>
                    <p class="text-muted">
                        Empowering communities to monitor and address environmental issues together.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-muted text-decoration-none">Features</a></li>
                        <li><a href="#about" class="text-muted text-decoration-none">About</a></li>
                        <li><a href="#impact" class="text-muted text-decoration-none">Impact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Account</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}" class="text-muted text-decoration-none">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-muted text-decoration-none">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="mb-3">Contact</h6>
                    <p class="text-muted">
                        <i class="bi bi-envelope me-2"></i>support@ecotracker.com<br>
                        <i class="bi bi-phone me-2"></i>+1 (555) 123-4567
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 EcoTracker. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Made with <i class="bi bi-heart-fill text-danger"></i> for our planet</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
