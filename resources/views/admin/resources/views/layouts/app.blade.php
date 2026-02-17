<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'Dashboard') | {{ config('app.name') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js (required for sidebar/theme stores) - use CDN allowed by CSP -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash (guard: body may not exist yet in head) -->
    <script>
        (function() {
            const html = document.documentElement;
            if (!html) return;
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                html.classList.add('dark');
                if (document.body) document.body.classList.add('dark', 'bg-gray-900');
            } else {
                html.classList.remove('dark');
                if (document.body) document.body.classList.remove('dark', 'bg-gray-900');
            }
        })();
    </script>
    
</head>

<body class="overflow-x-hidden bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    @include('tailadmin::components.common.preloader')
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('tailadmin::layouts.backdrop')
        @include('tailadmin::layouts.sidebar')

        <div class="min-w-0 flex-1 transition-all duration-300 ease-in-out overflow-x-hidden"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('tailadmin::layouts.app-header')
            <!-- app header end -->
            <div class="min-w-0 w-full max-w-screen-2xl mx-auto p-4 md:p-6 bg-gray-100 dark:bg-gray-900">
                @if(session('success'))
                    <div class="mb-4 hidden" id="flash-success" data-flash="success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 hidden" id="flash-error" data-flash="error">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="mb-4 hidden" id="flash-info" data-flash="info">{{ session('info') }}</div>
                @endif
                @if(session('warning'))
                    <div class="mb-4 hidden" id="flash-warning" data-flash="warning">{{ session('warning') }}</div>
                @endif
                <div class="admin-page-content min-w-0 max-w-full">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>

</body>

<!-- SweetAlert2 for session timeout and flash messages -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(file_exists(public_path('js/sweetalert-helpers.js')))
<script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
@endif
@stack('scripts')

</html>
