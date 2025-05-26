<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoTracker') }} - Register</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Inter:300,400,500,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --eco-primary: #2d5a27;
            --eco-secondary: #4a7c59;
            --eco-accent: #7fb069;
            --eco-light: #d6eadf;
            --eco-dark: #1a3a17;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .register-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg,
                rgba(45, 90, 39, 0.8) 0%,
                rgba(74, 124, 89, 0.7) 50%,
                rgba(127, 176, 105, 0.6) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="nature" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="50" cy="50" r="2" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23nature)"/><path d="M0,400 Q300,200 600,400 T1200,400 L1200,800 L0,800 Z" fill="%23ffffff" opacity="0.05"/></svg>');
            background-size: cover;
            background-position: center;
            filter: blur(1px);
            z-index: 1;
        }

        .register-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            width: 100%;
            max-width: 480px;
            margin: 2rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--eco-primary), var(--eco-accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(45, 90, 39, 0.3);
        }

        .logo-icon i {
            font-size: 2rem;
            color: white;
        }

        .app-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--eco-primary);
            margin-bottom: 0.5rem;
        }

        .app-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--eco-primary);
            box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.15);
            background: white;
        }

        .form-label {
            font-weight: 500;
            color: var(--eco-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--eco-primary), var(--eco-secondary));
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 90, 39, 0.4);
            background: linear-gradient(135deg, var(--eco-dark), var(--eco-primary));
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .login-link a {
            color: var(--eco-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--eco-dark);
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
        }

        .form-control.with-icon {
            padding-left: 3rem;
        }

        @media (max-width: 576px) {
            .register-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 16px;
            }

            .app-title {
                font-size: 1.5rem;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .logo-icon i {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="background-image"></div>

        <div class="register-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-globe-americas"></i>
                </div>
                <h1 class="app-title">Join EcoTracker</h1>
                <p class="app-subtitle">Help protect our environment together</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                @if(session('user_type') === 'authority')
                    <input type="hidden" name="user_type" value="authority">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        You're registering as an Environmental Authority. After registration, you'll need to complete your authority profile.
                    </div>
                @else
                    <input type="hidden" name="user_type" value="user">
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Please fix the errors below
                    </div>
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <i class="bi bi-person input-icon"></i>
                        <input id="name" type="text"
                               class="form-control with-icon @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}"
                               required autocomplete="name" autofocus
                               placeholder="Enter your full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input id="email" type="email"
                               class="form-control with-icon @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}"
                               required autocomplete="email"
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input id="password" type="password"
                               class="form-control with-icon @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password"
                               placeholder="Create a password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input id="password-confirm" type="password"
                               class="form-control with-icon"
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="bi bi-person-plus me-2"></i>
                    Create Account
                </button>

                <div class="login-link">
                    <span class="text-muted">Already have an account?</span>
                    <a href="{{ route('login') }}">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

