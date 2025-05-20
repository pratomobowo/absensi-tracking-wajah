<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Attendance System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 hidden md:block bg-blue-800 text-white shadow-xl">
            <div class="p-4 text-white">
                <h2 class="text-xl font-bold mb-6">Admin Panel</h2>
                <nav class="mt-6">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                                <span class="ml-2">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.employees.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.employees.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                                <span class="ml-2">Employees</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.departments.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.departments.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                                <span class="ml-2">Departments</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                                <span class="ml-2">Reports</span>
                            </a>
                            <ul class="pl-6 mt-1 space-y-1">
                                <li>
                                    <a href="{{ route('admin.reports.daily') }}" class="block py-1.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.daily') ? 'bg-blue-600' : 'hover:bg-blue-600' }}">
                                        <span class="ml-2">Daily Report</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.reports.monthly') }}" class="block py-1.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.monthly') ? 'bg-blue-600' : 'hover:bg-blue-600' }}">
                                        <span class="ml-2">Monthly Report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="py-4 px-4 flex justify-between items-center">
                    <button class="md:hidden text-gray-700 focus:outline-none" id="menu-toggle">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">@yield('header-title', 'Admin Dashboard')</h1>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">{{ Auth::guard('admin')->user()->name }}</span>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-sm">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-100">
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
        </div>
    </div>

    <!-- Mobile Menu Modal -->
    <div id="mobile-menu" class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 hidden">
        <div class="bg-blue-800 text-white w-64 min-h-screen p-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Admin Panel</h2>
                <button id="close-menu" class="text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                            <span class="ml-2">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employees.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.employees.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                            <span class="ml-2">Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.departments.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.departments.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                            <span class="ml-2">Departments</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                            <span class="ml-2">Reports</span>
                        </a>
                        <ul class="pl-6 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('admin.reports.daily') }}" class="block py-1.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.daily') ? 'bg-blue-600' : 'hover:bg-blue-600' }}">
                                    <span class="ml-2">Daily Report</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.monthly') }}" class="block py-1.5 px-4 rounded transition duration-200 {{ Route::is('admin.reports.monthly') ? 'bg-blue-600' : 'hover:bg-blue-600' }}">
                                    <span class="ml-2">Monthly Report</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    @yield('scripts')
    
    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.remove('hidden');
        });
        
        document.getElementById('close-menu')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.add('hidden');
        });
    </script>
</body>
</html> 