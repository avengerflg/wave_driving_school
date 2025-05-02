<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Driving School') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Normalize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/booking-steps.css') }}" rel="stylesheet">
    
    <style>
        /* CSS Reset */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Root Variables */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        /* Base Styles */
        html {
            font-size: 16px;
            height: 100%;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--background-color);
            min-height: 100vh;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            color: #1e293b;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* App Container */
        #app {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            padding: 1rem 0;
            background-color: rgba(255, 255, 255, 0.95);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            background: -webkit-linear-gradient(45deg, var(--primary-color), #1d4ed8);
            background: -moz-linear-gradient(45deg, var(--primary-color), #1d4ed8);
            background: linear-gradient(45deg, var(--primary-color), #1d4ed8);
            -webkit-background-clip: text;
            -moz-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: var(--primary-color);
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            -webkit-transition: var(--transition);
            -moz-transition: var(--transition);
            transition: var(--transition);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin: 0 0.25rem;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: #f1f5f9;
            -webkit-transform: translateY(-1px);
            -ms-transform: translateY(-1px);
            transform: translateY(-1px);
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border-radius: 12px;
            -webkit-box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.5rem;
            background-color: #fff;
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            border-radius: 8px;
            -webkit-transition: var(--transition);
            -moz-transition: var(--transition);
            transition: var(--transition);
            color: #475569;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
            -webkit-transform: translateX(5px);
            -ms-transform: translateX(5px);
            transform: translateX(5px);
        }

        /* Alert Styles */
        .alert {
            border-radius: 12px;
            margin-bottom: 1.5rem;
            padding: 1rem 1.5rem;
            border: none;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-animation: slideIn 0.5s ease;
            animation: slideIn 0.5s ease;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        @-webkit-keyframes slideIn {
            from {
                -webkit-transform: translateY(-100%);
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                -webkit-transform: translateY(-100%);
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Main Content */
        main {
            -webkit-box-flex: 1;
            -ms-flex: 1 0 auto;
            flex: 1 0 auto;
            padding: 2.5rem 0;
        }

        .container {
            width: 100%;
            padding-right: 1rem;
            padding-left: 1rem;
            margin-right: auto;
            margin-left: auto;
            max-width: 1200px;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-transition: var(--transition);
            -moz-transition: var(--transition);
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-primary {
            background: -webkit-linear-gradient(45deg, var(--primary-color), #1d4ed8);
            background: -moz-linear-gradient(45deg, var(--primary-color), #1d4ed8);
            background: linear-gradient(45deg, var(--primary-color), #1d4ed8);
            border: none;
            color: #fff;
            -webkit-box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            -webkit-transform: translateY(-2px);
            -ms-transform: translateY(-2px);
            transform: translateY(-2px);
            -webkit-box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        /* Footer Styles */
        .footer {
            background-color: #fff;
            padding: 2rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }

        .footer-link {
            color: var(--secondary-color);
            text-decoration: none;
            -webkit-transition: var(--transition);
            -moz-transition: var(--transition);
            transition: var(--transition);
            margin-left: 2rem;
            font-weight: 600;
            position: relative;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        .footer-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--primary-color);
            -webkit-transition: var(--transition);
            -moz-transition: var(--transition);
            transition: var(--transition);
        }

        .footer-link:hover::after {
            width: 100%;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .navbar-nav {
                padding: 1rem 0;
                background-color: #fff;
                border-radius: 12px;
                margin-top: 1rem;
                -webkit-box-shadow: var(--card-shadow);
                box-shadow: var(--card-shadow);
            }

            .nav-link {
                padding: 0.75rem 1.5rem;
                margin: 0.25rem 0;
            }

            .footer {
                text-align: center;
                padding: 2rem 1.5rem;
            }

            .footer-links {
                margin-top: 1.5rem;
            }

            .footer-link {
                display: inline-block;
                margin: 0.75rem 1rem;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            :root {
                --background-color: #0f172a;
                --secondary-color: #94a3b8;
            }

            body {
                color: #e2e8f0;
            }

            .navbar, .footer {
                background-color: #1e293b;
                border-color: rgba(255, 255, 255, 0.05);
            }

            .nav-link:hover {
                background-color: #2d3748;
            }

            .dropdown-menu {
                background-color: #1e293b;
                border-color: rgba(255, 255, 255, 0.05);
            }

            .dropdown-item {
                color: #e2e8f0;
            }

            .dropdown-item:hover {
                background-color: #2d3748;
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
                    Wave Driving School
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('booking.index') }}">{{ __('Book Lesson') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        {{ __('My Profile') }}
                                    </a>
                                    @if(Auth::user()->role === 'user')
                                        <a class="dropdown-item" href="{{ route('client.bookings.index') }}">
                                            {{ __('My Bookings') }}
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
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
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
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
                        <p class="mb-0">&copy; {{ date('Y') }} Wave Driving School. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end footer-links">
                        <a href="{{ route('about') }}" class="footer-link">About Us</a>
                        <a href="{{ route('faq') }}" class="footer-link">FAQ</a>
                        <a href="{{ route('contact') }}" class="footer-link">Contact Us</a>
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
