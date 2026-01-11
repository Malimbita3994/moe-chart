<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'MOE') }}</title>
    
    <!-- Vite Assets (Tailwind CSS compiled) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom Color Scheme */
        .sidebar-bg {
            background-color: #1F2937;
            scrollbar-width: thin;
            scrollbar-color: #374151 #1F2937;
        }
        
        .sidebar-bg::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-bg::-webkit-scrollbar-track {
            background: #1F2937;
        }
        
        .sidebar-bg::-webkit-scrollbar-thumb {
            background-color: #374151;
            border-radius: 3px;
        }
        
        .sidebar-bg::-webkit-scrollbar-thumb:hover {
            background-color: #4B5563;
        }
        
        .sidebar-hover:hover {
            background-color: #374151 !important;
        }
        
        .sidebar-active {
            background-color: #374151 !important;
            border-right: 4px solid #D4AF37 !important;
        }
        
        .sidebar-text {
            color: #F9FAFB;
        }
        
        .sidebar-icon {
            color: #D1D5DB;
        }
        
        .sidebar-subtitle {
            color: #D1D5DB;
        }
        
        .sidebar-border {
            border-color: #374151;
        }
        
        /* Gold Accent for Primary Actions */
        .btn-primary {
            background-color: #D4AF37;
            color: #1F2937;
        }
        
        .btn-primary:hover {
            background-color: #C4A027;
        }
        
        .btn-primary-text {
            color: #1F2937;
        }
        
        .link-primary {
            color: #D4AF37;
        }
        
        .link-primary:hover {
            color: #C4A027;
        }
        
        .border-gold {
            border-color: #D4AF37;
        }
        
        .bg-gold {
            background-color: #D4AF37;
        }
        
        .text-gold {
            color: #D4AF37;
        }
        
        /* Alpine.js x-cloak */
        [x-cloak] {
            display: none !important;
        }
        
        /* Card Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Animated Card - applies fade in animation */
        .animated-card {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        /* Card with hover effects */
        .card-hover {
            transition: all 0.3s ease-out;
        }
        
        .card-hover:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Icon container animation on hover */
        .card-hover:hover .icon-container {
            transform: scale(1.1) rotate(5deg);
        }
        
        .card-hover:hover .icon-container svg {
            transform: scale(1.1);
        }
        
        /* Enhanced card hover with border */
        .card-hover-enhanced {
            transition: all 0.3s ease-out;
        }
        
        .card-hover-enhanced:hover {
            transform: scale(1.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-color: #9CA3AF;
        }
        
        /* Staggered animation delays */
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .animate-delay-400 { animation-delay: 0.4s; }
        .animate-delay-500 { animation-delay: 0.5s; }
        .animate-delay-600 { animation-delay: 0.6s; }
        .animate-delay-700 { animation-delay: 0.7s; }
        .animate-delay-800 { animation-delay: 0.8s; }
        
        /* Smooth sidebar item transitions */
        nav a, nav button {
            position: relative;
        }
        
        nav a::before, nav button::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background-color: #D4AF37;
            transition: width 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        
        nav a.sidebar-active::before, nav button.sidebar-active::before {
            width: 4px;
        }
        
        /* Navbar enhancements */
        header {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Footer enhancements */
        footer {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
    <!-- SweetAlert2 Helpers -->
    <script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
    <script>
        function toggleUserManagement() {
            const submenu = document.getElementById('user-management-submenu');
            const arrow = document.getElementById('user-management-arrow');
            
            if (submenu.classList.contains('max-h-0')) {
                submenu.classList.remove('max-h-0');
                submenu.classList.add('max-h-96');
                arrow.classList.add('rotate-90');
            } else {
                submenu.classList.remove('max-h-96');
                submenu.classList.add('max-h-0');
                arrow.classList.remove('rotate-90');
            }
        }
    </script>
</head>
<body class="bg-gray-100 overflow-x-hidden">
    <div class="min-h-screen flex overflow-x-hidden">
        <!-- Include Sidebar -->
        @include('partials.admin.sidebar')
        
        <!-- Main Content Area -->
        <div class="ml-64 flex flex-col flex-1 min-h-screen overflow-x-hidden">
            <!-- Include Top Navbar -->
            @include('partials.admin.navbar')
            
            <!-- Content Area with padding for fixed navbar -->
            <main class="flex-1 pt-20 overflow-x-hidden">
                <div class="p-6 pb-24">
                    <!-- Flash Messages (now handled by SweetAlert2, but keeping for fallback) -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden" id="flash-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden" id="flash-error">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded hidden" id="flash-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded hidden" id="flash-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
                    
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
            
            <!-- Include Footer -->
            @include('partials.admin.footer')
        </div>
    </div>
</body>
</html>
