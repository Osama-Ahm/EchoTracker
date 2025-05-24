<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoTracker') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:300,400,500,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

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
            font-family: 'Inter', sans-serif;
            background: var(--gradient-bg);
            min-height: 100vh;
            position: relative;
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
    </style>


    <!-- Scripts -->

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
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

    @stack('scripts')
</body>
</html>
