<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoTracker') }} - Choose Account Type</title>

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

        .type-selection-container {
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

        .type-selection-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            width: 100%;
            max-width: 800px;
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
            font-weight: 700;
            color: var(--eco-dark);
            margin-bottom: 0.5rem;
        }

        .app-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .account-types {
            display: flex;
            gap: 20px;
            margin-top: 2rem;
        }

        .account-type {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .account-type:hover {
            border-color: var(--eco-accent);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .account-type.selected {
            border-color: var(--eco-primary);
            background-color: rgba(45, 90, 39, 0.05);
        }

        .account-type-icon {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
        }

        .account-type:hover .account-type-icon,
        .account-type.selected .account-type-icon {
            background: linear-gradient(135deg, var(--eco-primary), var(--eco-secondary));
        }

        .account-type-icon i {
            font-size: 2.5rem;
            color: var(--eco-primary);
            transition: all 0.3s ease;
        }

        .account-type:hover .account-type-icon i,
        .account-type.selected .account-type-icon i {
            color: white;
        }

        .account-type h3 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--eco-dark);
        }

        .account-type p {
            color: #6c757d;
            margin-bottom: 1.5rem;
            min-height: 80px;
        }

        .btn-continue {
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
            margin-top: 2rem;
        }

        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 90, 39, 0.4);
            background: linear-gradient(135deg, var(--eco-dark), var(--eco-primary));
        }

        .btn-continue:active {
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

        @media (max-width: 768px) {
            .account-types {
                flex-direction: column;
            }
            
            .account-type p {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="type-selection-container">
        <div class="background-image"></div>

        <div class="type-selection-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-globe-americas"></i>
                </div>
                <h1 class="app-title">Choose Your Account Type</h1>
                <p class="app-subtitle">Select the type of account that best fits your needs</p>
            </div>

            <form method="POST" action="{{ route('register.type.process') }}" id="typeSelectionForm">
                @csrf
                <input type="hidden" name="user_type" id="userTypeInput" value="">

                <div class="account-types">
                    <div class="account-type" data-type="user" onclick="selectType('user')">
                        <div class="account-type-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <h3>Individual User</h3>
                        <p>Report environmental issues, track your impact, and connect with your community.</p>
                        <div class="features">
                            <div><i class="bi bi-check-circle text-success me-2"></i>Report incidents</div>
                            <div><i class="bi bi-check-circle text-success me-2"></i>Track personal impact</div>
                            <div><i class="bi bi-check-circle text-success me-2"></i>Join community initiatives</div>
                        </div>
                    </div>

                    <div class="account-type" data-type="authority" onclick="selectType('authority')">
                        <div class="account-type-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3>Environmental Authority</h3>
                        <p>Monitor and respond to environmental issues in your jurisdiction.</p>
                        <div class="features">
                            <div><i class="bi bi-check-circle text-success me-2"></i>Manage reported incidents</div>
                            <div><i class="bi bi-check-circle text-success me-2"></i>Provide official responses</div>
                            <div><i class="bi bi-check-circle text-success me-2"></i>Access analytics dashboard</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-continue" id="continueButton" disabled>
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    Continue
                </button>

                <div class="login-link">
                    <span class="text-muted">Already have an account?</span>
                    <a href="{{ route('login') }}">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectType(type) {
            // Update hidden input
            document.getElementById('userTypeInput').value = type;
            
            // Update UI
            document.querySelectorAll('.account-type').forEach(el => {
                el.classList.remove('selected');
            });
            
            document.querySelector(`.account-type[data-type="${type}"]`).classList.add('selected');
            
            // Enable continue button
            document.getElementById('continueButton').disabled = false;
        }
    </script>
</body>
</html>