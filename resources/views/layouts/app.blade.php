<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Driving School') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Normalize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/booking-steps.css') }}" rel="stylesheet">
    
    <style>
        /* Reset */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Variables */
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-bg: #f9fafb;
            --dark-bg: #111827;
            --card-bg: #ffffff;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --header-height: 70px;
        }

        /* Base Styles */
        html {
            font-size: 16px;
            height: 100%;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1e293b;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Container */
        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            padding: 0;
            height: var(--header-height);
            background-color: var(--card-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0;
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
            transition: var(--transition);
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-link {
            font-weight: 500;
            color: #475569 !important;
            transition: var(--transition);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            margin: 0 0.25rem;
            position: relative;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: rgba(59, 130, 246, 0.08);
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(59, 130, 246, 0.08);
            font-weight: 600;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.5rem;
            background-color: var(--card-bg);
            margin-top: 0.5rem;
            min-width: 200px;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: var(--transition);
            color: #475569;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            font-size: 1.25rem;
        }

        .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.08);
            color: var(--primary-color);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-top-color: rgba(0, 0, 0, 0.05);
        }

        /* Alert Styles */
        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            padding: 1rem 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.5s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        @keyframes slideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Main Content */
        main {
            flex: 1 0 auto;
            padding: 2.5rem 0;
        }

        .container {
            width: 100%;
            padding-right: 1.5rem;
            padding-left: 1.5rem;
            margin-right: auto;
            margin-left: auto;
            max-width: 1200px;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            user-select: none;
            transition: var(--transition);
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #4f8df4, #2057db);
        }

        .btn i {
            margin-right: 0.5rem;
            font-size: 1.1em;
        }

        /* Footer Styles */
        .footer {
            background-color: var(--card-bg);
            padding: 2.5rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }

        .footer-link {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
            margin-left: 2rem;
            font-weight: 500;
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .footer-link i {
            margin-right: 0.5rem;
            font-size: 1.25rem;
            opacity: 0.7;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        .footer-link:hover i {
            opacity: 1;
            transform: translateX(-3px);
        }

        /* Card Styles */
        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            transform: translateY(-5px);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                height: auto;
                padding: 0.75rem 0;
            }

            .navbar-brand img {
                height: 36px;
            }

            .navbar-nav {
                padding: 1rem;
                background-color: var(--card-bg);
                border-radius: var(--border-radius);
                margin-top: 1rem;
                box-shadow: var(--card-shadow);
            }

            .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.25rem 0;
            }

            .dropdown-menu {
                box-shadow: none;
                border: none;
                padding: 0 0 0 1rem;
                margin-top: 0;
                animation: none;
            }

            .dropdown-item {
                padding: 0.75rem 1rem;
            }

            .footer {
                text-align: center;
                padding: 2rem 1.5rem;
            }

            .footer-links {
                margin-top: 1.5rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            .footer-link {
                margin: 0.5rem 1rem;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            :root {
                --light-bg: #1e293b;
                --dark-bg: #0f172a;
                --card-bg: #262f3f;
                --secondary-color: #94a3b8;
            }

            body {
                color: #e2e8f0;
            }

            .navbar, .footer {
                background-color: var(--card-bg);
                border-color: rgba(255, 255, 255, 0.05);
            }

            .nav-link {
                color: #e2e8f0 !important;
            }

            .nav-link:hover, .nav-link.active {
                background-color: rgba(59, 130, 246, 0.15);
                color: #60a5fa !important;
            }

            .dropdown-menu {
                background-color: #334155;
                border-color: rgba(255, 255, 255, 0.05);
            }

            .dropdown-item {
                color: #e2e8f0;
            }

            .dropdown-item:hover {
                background-color: rgba(59, 130, 246, 0.15);
                color: #60a5fa;
            }

            .dropdown-divider {
                border-top-color: rgba(255, 255, 255, 0.05);
            }

            .alert-success {
                background-color: rgba(16, 185, 129, 0.1);
                color: #a7f3d0;
            }

            .alert-danger {
                background-color: rgba(239, 68, 68, 0.1);
                color: #fca5a5;
            }

            .btn-primary {
                background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #4f8df4, #2057db);
            }

            .card {
                background-color: var(--card-bg);
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Wave Driving School" class="img-fluid">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('booking.*') ? 'active' : '' }}" href="{{ route('booking.index') }}">
                                <i class='bx bx-calendar-plus'></i> {{ __('Book Lesson') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                                <i class='bx bx-package'></i> {{ __('Packages') }}
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                        <i class='bx bx-log-in'></i> {{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                        <i class='bx bx-user-plus'></i> {{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class='bx bx-user-circle'></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class='bx bx-user'></i> {{ __('My Profile') }}
                                    </a>
                                    @if(Auth::user()->role === 'user')
                                        <a class="dropdown-item" href="{{ route('client.bookings.index') }}">
                                            <i class='bx bx-calendar-check'></i> {{ __('My Bookings') }}
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class='bx bx-log-out'></i> {{ __('Logout') }}
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

        <main>
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div><i class='bx bx-check-circle me-2'></i> {{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div><i class='bx bx-error-circle me-2'></i> {{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">&copy; {{ date('Y') }} Jk Driving School. All rights reserved.</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end footer-links">
                        <a href="https://jkdriving.webzoneexpertz.com.au/" class="footer-link">
                            <i class='bx bx-info-circle'></i> Home
                        </a>
                        <a href="https://jkdriving.webzoneexpertz.com.au/instructors/" class="footer-link">
                            <i class='bx bx-help-circle'></i> Instructors
                        </a>
                        <a href="https://jkdriving.webzoneexpertz.com.au/contact-us/" class="footer-link">
                            <i class='bx bx-envelope'></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>