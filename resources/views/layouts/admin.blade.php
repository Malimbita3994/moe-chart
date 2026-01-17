<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'MOE') }}</title>
    
    <!-- Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Vite Assets (Tailwind CSS compiled) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
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

    @php
        $lockMinutes = (int) config('session.lifetime', 120) / 60; // Convert to minutes
        $warningSeconds = 60; // 60 seconds warning before logout
        $totalSeconds = $lockMinutes * 60;
    @endphp

    @if ($lockMinutes > 0 && $warningSeconds > 0 && $totalSeconds > $warningSeconds)
        <script>
            (function() {
                const LOCK_MINUTES = {{ $lockMinutes }};
                const WARNING_SECONDS = {{ $warningSeconds }};
                const TOTAL_SECONDS = LOCK_MINUTES * 60;
                const WARNING_START = TOTAL_SECONDS - WARNING_SECONDS;

                if (TOTAL_SECONDS < WARNING_SECONDS || WARNING_SECONDS <= 0 || typeof Swal === 'undefined') {
                    return;
                }

                let idleSeconds = 0;
                let warningShown = false;
                let countdownInterval = null;

                function performLogout() {
                    // Get CSRF token from meta tag or form
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                     document.querySelector('input[name="_token"]')?.value || 
                                     '{{ csrf_token() }}';
                    
                    // Try to logout via fetch, then redirect to login
                    fetch("{{ route('logout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: '_token=' + encodeURIComponent(csrfToken)
                    })
                    .then(() => {
                        // Always redirect to login page
                        window.location.href = "{{ route('login') }}";
                    })
                    .catch(() => {
                        // If logout fails (e.g., CSRF expired), just redirect to login
                        window.location.href = "{{ route('login') }}";
                    });
                }

                function resetIdle() {
                    if (!warningShown) {
                        idleSeconds = 0;
                    }
                }

                function showTimeoutModal() {
                    warningShown = true;
                    let remaining = WARNING_SECONDS;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Are you still there?',
                        html: `
                            <p class="text-sm text-gray-700 mb-4">
                                You will be logged out in <strong><span id="swal-time-remaining">${remaining}</span> second(s)</strong> due to inactivity.
                            </p>
                            <p class="text-xs text-gray-500">
                                Confirm you are still working to keep your session active.
                            </p>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Stay Logged In',
                        cancelButtonText: 'Lock Now',
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            confirmButton: 'swal2-confirm',
                            cancelButton: 'swal2-cancel',
                        },
                        didOpen: () => {
                            const timeEl = document.getElementById('swal-time-remaining');
                            countdownInterval = setInterval(() => {
                                remaining--;
                                if (remaining <= 0) {
                                    clearInterval(countdownInterval);
                                    Swal.close();
                                    // Auto logout when countdown reaches zero
                                    performLogout();
                                } else if (timeEl) {
                                    timeEl.textContent = remaining;
                                }
                            }, 1000);
                        }
                    }).then((result) => {
                        clearInterval(countdownInterval);
                        if (result.isConfirmed) {
                            idleSeconds = 0;
                            warningShown = false;
                        } else if (result.dismiss !== Swal.DismissReason.timer) {
                            // Lock immediately (logout)
                            performLogout();
                        }
                    });
                }

                ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach((evt) => {
                    window.addEventListener(evt, resetIdle, { passive: true });
                });

                setInterval(() => {
                    idleSeconds++;
                    if (!warningShown && idleSeconds >= WARNING_START && idleSeconds < TOTAL_SECONDS) {
                        showTimeoutModal();
                    } else if (idleSeconds >= TOTAL_SECONDS) {
                        // Auto logout when total idle time reached
                        performLogout();
                    }
                }, 1000);
            })();
        </script>
    @endif

</body>
</html>
