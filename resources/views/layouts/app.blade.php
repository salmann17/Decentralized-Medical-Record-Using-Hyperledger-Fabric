<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Decentralized Medical Record')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo/Title -->
                    <div class="flex items-center">
                        <!-- Mobile toggle button -->
                        <button type="button" class="md:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-menu-button">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        
                        <!-- Desktop toggle button -->
                        <button type="button" class="hidden md:inline-flex -ml-0.5 -mt-0.5 h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="desktop-menu-button">
                            <span class="sr-only">Toggle sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        
                        <h1 class="ml-4 text-xl font-semibold text-gray-900">
                            Decentralized Medical Record
                        </h1>
                    </div>

                    <!-- User info -->
                    @auth
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="flex-shrink-0 w-72 bg-white border-r border-gray-200 transition-all duration-300 md:block" id="sidebar">
                <div class="flex flex-col h-full overflow-y-auto px-6 pt-4">
                    <!-- Mobile Close Button -->
                    <div class="flex items-center justify-between md:hidden mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-close-button">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @include('layouts.sidebar')
                </div>
            </aside>

            <!-- Mobile backdrop -->
            <div class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 md:hidden hidden" id="mobile-backdrop"></div>

            <!-- Main content -->
            <main class="flex-1 flex flex-col overflow-hidden" id="main-content">
                <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        {{ session('success') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const desktopMenuButton = document.getElementById('desktop-menu-button');
            const mobileCloseButton = document.getElementById('mobile-close-button');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            
            // Track sidebar state for desktop
            let isSidebarCollapsed = false;

            function openMobileSidebar() {
                // On mobile, show sidebar as overlay
                sidebar.classList.remove('hidden');
                sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-50');
                backdrop.classList.remove('hidden');
            }

            function closeMobileSidebar() {
                // On mobile, hide sidebar overlay
                sidebar.classList.add('hidden');
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50');
                backdrop.classList.add('hidden');
            }

            function toggleDesktopSidebar() {
                if (isSidebarCollapsed) {
                    // Show sidebar - expand to normal width
                    sidebar.classList.remove('w-0', 'overflow-hidden');
                    sidebar.classList.add('w-72');
                    isSidebarCollapsed = false;
                } else {
                    // Hide sidebar - collapse to width 0
                    sidebar.classList.remove('w-72');
                    sidebar.classList.add('w-0', 'overflow-hidden');
                    isSidebarCollapsed = true;
                }
            }

            // Event listeners
            mobileMenuButton?.addEventListener('click', openMobileSidebar);
            mobileCloseButton?.addEventListener('click', closeMobileSidebar);
            backdrop?.addEventListener('click', closeMobileSidebar);
            desktopMenuButton?.addEventListener('click', toggleDesktopSidebar);

            // Close mobile sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 768) {
                    closeMobileSidebar();
                }
            });

            // Initialize sidebar state based on screen size
            function initializeSidebar() {
                if (window.innerWidth >= 768) { // md breakpoint
                    // Desktop - show sidebar as part of layout
                    sidebar.classList.remove('hidden', 'fixed', 'inset-y-0', 'left-0', 'z-50', 'w-0', 'overflow-hidden');
                    sidebar.classList.add('w-72');
                    backdrop.classList.add('hidden');
                    isSidebarCollapsed = false;
                } else {
                    // Mobile - hide sidebar by default
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50');
                    backdrop.classList.add('hidden');
                }
            }

            // Initialize on load
            initializeSidebar();

            // Reinitialize on window resize
            window.addEventListener('resize', function() {
                initializeSidebar();
            });
        });
    </script>
</body>
</html>