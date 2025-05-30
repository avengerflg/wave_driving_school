<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Dashboard'))</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Custom styles -->
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>

<body class="h-full bg-gray-50">
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 z-50 hidden bg-black/50">
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary-500 border-t-transparent"></div>
        </div>
    </div>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="sidebar-transition fixed inset-y-0 z-40 flex w-64 flex-col border-r bg-white">
            <div class="flex h-16 items-center border-b px-4">
                <h2 class="text-xl font-bold text-gray-800">{{ config('app.name') }}</h2>
            </div>
            
            <nav class="flex-1 overflow-y-auto px-4 py-4">
                <!-- Sidebar content -->
                @include('partials.sidebar')
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex min-w-0 flex-1 flex-col md:ml-64">
            <!-- Navbar -->
            <header class="sticky top-0 z-30 flex h-16 items-center border-b bg-white px-4 shadow-sm">
                
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page Content -->
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t bg-white px-6 py-4">
                <div class="flex flex-col items-center justify-between md:flex-row">
                    <div class="mb-2 md:mb-0">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                    <div class="space-x-4">
                        <a href="/" class="text-gray-600 hover:text-gray-800">Privacy Policy</a>
                        <a href="/" class="text-gray-600 hover:text-gray-800">Terms of Service</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Global Scripts -->
    <script>
        // Show/hide loading spinner
        function showLoading() {
            document.getElementById('loading-spinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-spinner').classList.add('hidden');
        }

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.remove();
            }, 5000);
        });
    </script>

    <!-- Page Scripts -->
    @stack('scripts')
</body>
</html>
