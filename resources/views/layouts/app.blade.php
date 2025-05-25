<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoTracker') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        :root {
            --eco-primary: #2d5a27;
            --eco-secondary: #4a7c59;
            --eco-accent: #7fb069;
            --eco-light: #d6eadf;
            --eco-dark: #1a3a17;
            --gradient-eco: linear-gradient(135deg, #2d5a27 0%, #7fb069 50%, #4a7c59 100%);
            --gradient-bg: linear-gradient(135deg, #f8fffe 0%, #e8f5e8 50%, #f0f9f0 100%);
            --gradient-card: linear-gradient(135deg, #ffffff 0%, #fafffe 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fffe 0%, #f0f9f0 100%);
            min-height: 100vh;
            position: relative;
            font-weight: 400;
            letter-spacing: -0.01em;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><radialGradient id="grad1" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:%2327ae60;stop-opacity:0.03" /><stop offset="100%" style="stop-color:%2327ae60;stop-opacity:0" /></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;50,30;0,0" dur="20s" repeatCount="indefinite"/></circle><circle cx="800" cy="300" r="150" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;-30,50;0,0" dur="25s" repeatCount="indefinite"/></circle><circle cx="400" cy="600" r="80" fill="url(%23grad1)"><animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;40,-20;0,0" dur="18s" repeatCount="indefinite"/></circle></svg>');
            pointer-events: none;
            z-index: -1;
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--eco-primary) !important;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .btn-eco-primary {
            background: var(--gradient-eco);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }

        .btn-eco-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 90, 39, 0.4);
            color: white;
        }

        .text-eco-primary {
            color: var(--eco-primary) !important;
        }

        .bg-eco-light {
            background: linear-gradient(135deg, var(--eco-light) 0%, #e8f5e8 100%) !important;
        }

        .card {
            border: none;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-radius: 20px;
            background: var(--gradient-card);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(45, 90, 39, 0.1);
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 20px;
            margin: 0 5px;
        }

        .nav-link:hover {
            background: rgba(45, 90, 39, 0.1);
            transform: translateY(-1px);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999 !important;
            position: absolute !important;
        }

        .dropdown-item {
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 2px 5px;
        }

        .dropdown-item:hover {
            background: var(--gradient-eco);
            color: white;
            transform: translateX(5px);
        }

        /* Navbar dropdown specific styles */
        .navbar {
            position: relative;
            z-index: 1030;
        }

        .navbar .dropdown {
            position: static;
        }

        .navbar .dropdown-menu {
            position: absolute !important;
            z-index: 9999 !important;
            top: 100% !important;
            right: 15px !important;
            left: auto !important;
            transform: none !important;
        }

        .navbar .dropdown-menu.show {
            z-index: 9999 !important;
            position: absolute !important;
        }

        /* Ensure dropdown is above all other content */
        .navbar-nav .dropdown-menu {
            z-index: 9999 !important;
            position: absolute !important;
        }

        /* Override any conflicting z-index from other components */
        .nav-tabs,
        .tab-content,
        .card,
        .modal,
        .leaflet-container {
            z-index: auto !important;
        }

        /* Specific fix for tabs that might interfere */
        .nav-tabs {
            z-index: 1 !important;
        }

        .tab-content {
            z-index: 1 !important;
        }

        /* Mobile dropdown fixes */
        @media (max-width: 767.98px) {
            .navbar .dropdown-menu {
                position: static !important;
                z-index: auto !important;
                box-shadow: none;
                border: 1px solid rgba(0,0,0,0.1);
                margin-top: 0.5rem;
                right: auto !important;
            }
        }

        /* Tablet and small desktop adjustments */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .navbar .dropdown-menu {
                right: 10px !important;
            }
        }

        /* Large desktop - more spacing from edge */
        @media (min-width: 1200px) {
            .navbar .dropdown-menu {
                right: 20px !important;
            }
        }

        /* Ensure dropdown toggle works properly */
        .navbar .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }

        .navbar .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        /* Fix for any potential overflow issues */
        .navbar-collapse {
            overflow: visible !important;
        }

        .navbar-nav {
            overflow: visible !important;
        }

        /* Animated elements */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-eco);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--eco-dark);
        }

        /* Loading animation for page transitions */
        .page-content {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Enhanced button animations */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: scale(0.98);
        }

        /* Form enhancements */
        .form-control, .form-select {
            border: 2px solid rgba(45, 90, 39, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--eco-primary);
            box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.15);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Badge animations */
        .badge {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        /* Enhanced card animations */
        .card {
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .card:hover::before {
            transform: translateX(100%);
        }

        /* Floating elements */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Stagger animations for lists */
        .stagger-animation > * {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
        .stagger-animation > *:nth-child(6) { animation-delay: 0.6s; }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
        }

        /* Smooth page transitions */
        .page-transition {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }

        .page-transition.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        /* Enhanced navbar */
        .navbar {
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
        }

        /* Icon animations */
        .icon-hover {
            transition: all 0.3s ease;
        }

        .icon-hover:hover {
            transform: rotate(10deg) scale(1.1);
            color: var(--eco-primary);
        }

        /* Loading spinner */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-spinner {
            border: 3px solid rgba(45, 90, 39, 0.1);
            border-top: 3px solid var(--eco-primary);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        /* Form button visibility fix */
        form .btn {
            opacity: 1 !important;
            transform: none !important;
        }

        /* Responsive enhancements */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
                border-radius: 16px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>


    <!-- Scripts -->

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                    <i class="bi bi-globe-americas me-2 text-eco-primary"></i>
                    <span class="text-eco-primary">EcoTracker</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('incidents.index') }}">
                                    <i class="bi bi-list-ul me-1"></i>All Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('incidents.map') }}">
                                    <i class="bi bi-geo-alt me-1"></i>Map View
                                </a>
                            </li>

                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-1"></i>{{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person me-2"></i>My Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('incidents.my') }}">
                                        <i class="bi bi-file-earmark-text me-2"></i>My Reports
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fix dropdown z-index issues
        document.addEventListener('DOMContentLoaded', function() {
            // Handle dropdown show/hide events
            const dropdowns = document.querySelectorAll('.navbar .dropdown');

            dropdowns.forEach(function(dropdown) {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');

                dropdown.addEventListener('show.bs.dropdown', function() {
                    // Ensure dropdown menu is positioned correctly
                    if (dropdownMenu) {
                        dropdownMenu.style.position = 'absolute';
                        dropdownMenu.style.zIndex = '9999';
                        dropdownMenu.style.top = '100%';
                        dropdownMenu.style.left = 'auto';
                        dropdownMenu.style.transform = 'none';

                        // Responsive positioning
                        const screenWidth = window.innerWidth;
                        if (screenWidth >= 1200) {
                            dropdownMenu.style.right = '20px';
                        } else if (screenWidth >= 768) {
                            dropdownMenu.style.right = '15px';
                        } else {
                            dropdownMenu.style.right = '10px';
                        }
                    }
                });

                dropdown.addEventListener('shown.bs.dropdown', function() {
                    // Double-check positioning after dropdown is shown
                    if (dropdownMenu) {
                        dropdownMenu.style.zIndex = '9999';
                    }
                });
            });

            // Handle clicks outside dropdown to ensure proper closing
            document.addEventListener('click', function(event) {
                const openDropdowns = document.querySelectorAll('.navbar .dropdown.show');
                openDropdowns.forEach(function(dropdown) {
                    if (!dropdown.contains(event.target)) {
                        const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                        if (dropdownToggle) {
                            bootstrap.Dropdown.getInstance(dropdownToggle)?.hide();
                        }
                    }
                });
            });

            // Enhanced UI interactions
            initializeEnhancedUI();
        });

        function initializeEnhancedUI() {
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Page transition effect
            const pageContent = document.querySelector('.page-content');
            if (pageContent) {
                pageContent.classList.add('page-transition');
                setTimeout(() => {
                    pageContent.classList.add('loaded');
                }, 100);
            }

            // Stagger animation for cards
            const cardContainers = document.querySelectorAll('.row');
            cardContainers.forEach(container => {
                const cards = container.querySelectorAll('.card');
                if (cards.length > 1) {
                    container.classList.add('stagger-animation');
                }
            });

            // Enhanced button interactions
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Icon hover effects
            const icons = document.querySelectorAll('.bi');
            icons.forEach(icon => {
                if (!icon.closest('.btn')) {
                    icon.classList.add('icon-hover');
                }
            });

            // Form focus animations
            const formControls = document.querySelectorAll('.form-control, .form-select');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });

                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Smooth scrolling for anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
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

            // Loading states for forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<div class="loading-spinner me-2"></div>Processing...';
                        submitBtn.disabled = true;

                        // Re-enable after 5 seconds as fallback
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 5000);
                    }
                });
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe cards for scroll animations (exclude form buttons)
            const observableElements = document.querySelectorAll('.card');
            observableElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease-out';
                observer.observe(el);
            });

            // Observe navigation buttons (not form buttons)
            const navButtons = document.querySelectorAll('.btn-eco-primary:not(form .btn-eco-primary)');
            navButtons.forEach(el => {
                // Only apply to buttons that are not inside forms
                if (!el.closest('form')) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(30px)';
                    el.style.transition = 'all 0.6s ease-out';
                    observer.observe(el);
                }
            });
        }

        // Enhanced SweetAlert styling
        if (typeof Swal !== 'undefined') {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-eco-primary me-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false,
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            });

            // Replace default Swal with styled version
            window.Swal = swalWithBootstrapButtons;
        }
    </script>

    @stack('scripts')
</body>
</html>
