<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Attendance System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Face API - versi fixed -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="/js/face-init.js"></script>
    <script>
        // Pre-load models when page loads
        document.addEventListener('DOMContentLoaded', async () => {
            console.log('Pre-loading face-api.js models...');
            // Wait for face-api to initialize
            try {
                if (window.faceApiInit && typeof window.faceApiInit === 'function') {
                    await window.faceApiInit();
                }
            } catch (err) {
                console.error('Error pre-loading models:', err);
            }
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <h1 class="text-xl md:text-2xl font-bold">@yield('header-title', 'Attendance System')</h1>
                
                @if(Route::is('admin.*'))
                    <!-- Admin Navigation -->
                    <div class="flex items-center space-x-4">
                        @auth('admin')
                            <div class="hidden md:flex space-x-4">
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-200 {{ Route::is('admin.dashboard') ? 'text-white font-bold' : 'text-blue-100' }}">Dashboard</a>
                                <a href="{{ route('admin.dashboard.attendance') }}" class="hover:text-blue-200 {{ Route::is('admin.dashboard.*') ? 'text-white font-bold' : 'text-blue-100' }}">Attendance Dashboard</a>
                                <a href="{{ route('admin.employees.index') }}" class="hover:text-blue-200 {{ Route::is('admin.employees.*') ? 'text-white font-bold' : 'text-blue-100' }}">Employees</a>
                                <a href="{{ route('admin.departments.index') }}" class="hover:text-blue-200 {{ Route::is('admin.departments.*') ? 'text-white font-bold' : 'text-blue-100' }}">Departments</a>
                                <a href="{{ route('admin.reports.daily') }}" class="hover:text-blue-200 {{ Route::is('admin.reports.*') ? 'text-white font-bold' : 'text-blue-100' }}">Reports</a>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="hidden md:inline">{{ Auth::guard('admin')->user()->name }}</span>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-sm">Logout</button>
                                </form>
                            </div>
                        @endauth
                    </div>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white text-center py-4">
            <div class="container mx-auto">
                <p>&copy; {{ date('Y') }} Attendance System. All rights reserved.</p>
            </div>
        </footer>
    </div>

    @yield('scripts')
</body>
</html> 