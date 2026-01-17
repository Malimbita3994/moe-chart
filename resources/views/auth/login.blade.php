<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login - {{ config('app.name', 'MOE') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <style>
        /* ===== Base Styles ===== */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html, body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* ===== Background Pattern Section ===== */
        body {
            background:
                linear-gradient(135deg, rgba(0,0,0,0.02) 25%, transparent 25%) -50px 0,
                linear-gradient(135deg, rgba(0,0,0,0.02) 25%, transparent 25%) 0 0;
            background-size: 100px 100px, 100px 100px;
            background-color: #f8f9fc;
            background-position: center, center;
            background-repeat: repeat, repeat;
        }
        
        .bg-image-section {
            background-color: transparent !important;
            background-image: none !important;
            position: relative;
            overflow: hidden;
            height: 100vh;
            padding: 3rem 0;
        }
        
        /* Apply same pattern to login form section */
        .login-form-section {
            background-color: transparent !important;
            background-image: none !important;
        }
        
        /* Fallback if emblem image doesn't exist - only show if image fails */
        .bg-image-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("images/bg-hybrid.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
        }
        
        .bg-image-content {
            position: relative;
            z-index: 2;
        }
        
        /* ===== Glassmorphism Card ===== */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            width: 100%;
            margin: 0 auto;
        }
        
        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animated-card {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        .animated-left {
            animation: fadeInLeft 0.8s ease-out forwards;
            opacity: 0;
        }
        
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .animate-delay-400 { animation-delay: 0.4s; }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* ===== Form Styles ===== */
        .login-form-section {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            -webkit-overflow-scrolling: touch;
        }
        
        .login-form-section > div {
            margin: auto;
            padding: 0;
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            gap: 0;
        }
        
        .login-form-section > div > *:last-child {
            margin-bottom: 0;
        }
        
        .login-form-section input,
        .login-form-section button {
            transition: all 0.2s ease;
        }
        
        /* Ensure all form inputs and button are full width */
        .login-form-section form {
            width: 100%;
        }
        
        .login-form-section input[type="email"],
        .login-form-section input[type="password"] {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
            display: block;
        }

        /* Keep submit button flex so icon and text stay on one line */
        .login-form-section button[type="submit"] {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
        }
        
        .login-form-section .relative {
            max-width: 100% !important;
            width: 100% !important;
        }
        
        /* Remove any padding/margin that might affect width */
        .login-form-section form > div {
            width: 100%;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.3);
        }
        
        /* ===== Button Styles ===== */
        .btn-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #C4A027 100%);
            transition: all 0.3s ease;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .btn-gold:hover {
            background: linear-gradient(135deg, #C4A027 0%, #B8941F 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(212, 175, 55, 0.4);
        }
        
        /* ===== Decorative Elements ===== */
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .circle-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
        }
        
        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        /* ===== Responsive Design ===== */
        
        /* Input width constraints - removed to allow full width */
        @media (min-width: 640px) and (max-width: 1023px) {
            .login-form-section .relative {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
        
        @media (min-width: 1024px) {
            .login-form-section .relative {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
        
        /* Large tablets and below - hide background image */
        @media (max-width: 1024px) {
            .bg-image-section {
                display: none;
            }
            
            .login-form-section {
                width: 100%;
            }
            
            .login-form-section > div {
                max-width: 100%;
                max-height: calc(100vh - 2rem);
            }
            
            .glass-card {
                width: 100%;
                margin: 0;
            }
        }
        
        /* Tablets and below */
        @media (max-width: 768px) {
            .login-form-section {
                padding: 0.75rem;
            }
            
            .login-form-section > div {
                max-height: calc(100vh - 1.5rem);
            }
            
            .glass-card {
                padding: 1.25rem;
                border-radius: 1rem;
            }
            
            .login-form-section .lg\\:hidden {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }
            
            .login-form-section button,
            .login-form-section input[type="email"],
            .login-form-section input[type="password"],
            .login-form-section a {
                min-height: 44px;
                touch-action: manipulation;
                width: 100% !important;
                box-sizing: border-box;
            }
            
            .login-form-section label {
                margin-bottom: 0.5rem;
            }
        }
        
        /* Mobile phones */
        @media (max-width: 640px) {
            .login-form-section {
                padding: 0.75rem;
            }
            
            .login-form-section > div {
                max-height: calc(100vh - 1.5rem);
            }
            
            .glass-card {
                padding: 1rem;
                border-radius: 0.875rem;
            }
            
            .login-form-section .lg\\:hidden {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }
            
            h1 {
                font-size: 1.25rem;
                line-height: 1.3;
                margin-bottom: 0.375rem;
            }
        }
        
        /* Small mobile phones */
        @media (max-width: 480px) {
            .login-form-section {
                padding: 0.5rem;
            }
            
            .login-form-section > div {
                max-height: calc(100vh - 1rem);
            }
            
            .glass-card {
                padding: 0.875rem;
                border-radius: 0.75rem;
            }
            
            h1 {
                font-size: 1.125rem;
                margin-bottom: 0.25rem;
            }
            
            .login-form-section input[type="email"],
            .login-form-section input[type="password"],
            .login-form-section button[type="submit"] {
                font-size: 16px;
                width: 100% !important;
                box-sizing: border-box;
            }
        }
        
        /* Large screens */
        @media (min-width: 1025px) {
            .login-form-section {
                padding: 2rem;
            }
            
            .login-form-section > div {
                max-width: 28rem;
                max-height: calc(100vh - 4rem);
            }
        }
        
        /* Extra large screens */
        @media (min-width: 1281px) {
            .login-form-section {
                padding: 2.5rem;
            }
            
            .login-form-section > div {
                max-width: 32rem;
                max-height: calc(100vh - 5rem);
            }
        }
        
        /* Landscape orientation */
        @media (max-width: 1024px) and (orientation: landscape) {
            .login-form-section {
                padding: 0.75rem;
            }
            
            .login-form-section > div {
                max-height: calc(100vh - 1.5rem);
            }
            
            .glass-card {
                padding: 1rem;
            }
            
            .login-form-section .lg\\:hidden {
                margin-bottom: 0.5rem;
                margin-top: 0;
            }
            
            h1 {
                font-size: 1.125rem;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <div class="flex h-screen w-full overflow-hidden">
        <!-- Left Side - Background Image Section -->
        <div class="hidden lg:flex lg:w-1/2 bg-image-section items-center justify-center p-12 relative overflow-hidden">
            <div class="decorative-circle circle-1"></div>
            <div class="decorative-circle circle-2"></div>
            
            <div class="bg-image-content text-white max-w-lg z-10">
                <div class="animated-left animate-delay-100">
                    <div class="mb-8">
                        <div class="inline-block bg-white/20 backdrop-blur-md rounded-2xl p-6 mb-6 float-animation">
                            <img src="{{ asset('image/logo.png') }}" alt="MOE Logo" class="h-28 w-auto object-contain filter brightness-0 invert">
                        </div>
                        <h1 class="text-5xl font-bold mb-4 leading-tight">
                            Welcome to<br>
                            <span class="text-yellow-300">Organizational Excellence</span>
                        </h1>
                        <p class="text-xl text-white/90 mb-8 leading-relaxed">
                            Manage your organizational structure with precision and clarity. Connect, organize, and visualize your team hierarchy.
                        </p>
                    </div>
                    
                    <div class="space-y-4 animated-left animate-delay-200">
                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Visual Hierarchy</h3>
                                <p class="text-white/80 text-sm">Interactive organizational charts</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Team Management</h3>
                                <p class="text-white/80 text-sm">Track positions and assignments</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Secure Access</h3>
                                <p class="text-white/80 text-sm">Protected organizational data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center login-form-section">
            <div class="w-full max-w-full sm:max-w-md md:max-w-lg lg:max-w-md xl:max-w-lg flex flex-col">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-3 sm:mb-4 md:mb-6 pt-1 sm:pt-2 animated-card animate-delay-100">
                    <a href="{{ route('org-chart.index') }}" class="inline-block mb-1.5 sm:mb-2 md:mb-3 group">
                        <div class="bg-white rounded-lg sm:rounded-xl md:rounded-2xl p-2 sm:p-3 md:p-4 shadow-md sm:shadow-lg inline-block group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                            <img src="{{ asset('image/logo.png') }}" alt="MOE Logo" class="h-20 sm:h-24 md:h-28 lg:h-32 w-auto object-contain mx-auto">
                        </div>
                    </a>
                </div>
                
                <!-- Login Card -->
                <div class="glass-card rounded-2xl sm:rounded-3xl shadow-2xl p-4 sm:p-6 md:p-8 lg:p-10 animated-card animate-delay-200">
                    <div class="text-center mb-3 sm:mb-4 md:mb-6">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 mb-1 sm:mb-1.5">Digital Organizational Chart</h1>
                        <p class="text-xs sm:text-sm text-gray-600">Sign in</p>
                    </div>
                    
                    @if (session('info'))
                        <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-xl animated-card animate-delay-300">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-blue-800">{{ session('info') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-3 sm:space-y-4 md:space-y-5 w-full">
                        @csrf
                        
                        <!-- Email Field -->
                        <div class="animated-card animate-delay-300">
                            <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-2 flex items-center" for="email">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email Address
                            </label>
                            <div class="relative">
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    autocomplete="email"
                                    class="input-focus w-full px-3 sm:px-4 py-2.5 sm:py-3 pl-10 sm:pl-12 border-2 border-gray-300 rounded-lg sm:rounded-xl text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                    placeholder="Enter your email">
                                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div class="animated-card animate-delay-400">
                            <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-2 flex items-center" for="password">
                                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    autocomplete="current-password"
                                    class="input-focus w-full px-3 sm:px-4 py-2.5 sm:py-3 pl-10 sm:pl-12 border-2 border-gray-300 rounded-lg sm:rounded-xl text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white"
                                    placeholder="Enter your password">
                                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Remember Me and Forgot Password -->
                        <div class="flex items-center justify-between animated-card animate-delay-400">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" 
                                    class="w-5 h-5 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <span class="ml-3 text-xs sm:text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Remember me</span>
                            </label>
                            <a href="{{ route('password.request') }}" 
                               class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Forgot Password?
                            </a>
                        </div>
                        
                        <!-- Login Button (same width as inputs via shared .relative wrapper) -->
                        <div class="relative animated-card animate-delay-500">
                            <button type="submit" 
                                class="btn-gold w-full py-3 sm:py-4 rounded-xl font-bold text-sm sm:text-base transition-all duration-200 shadow-lg flex items-center justify-center text-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Sign In
                            </button>
                        </div>
                    </form>
                    
                    <!-- Links Section -->
                    <div class="mt-3 sm:mt-4 md:mt-6 space-y-2 sm:space-y-3">
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mr-2 sm:mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-left flex-1">
                                    <p class="text-xs sm:text-sm font-semibold text-blue-800 mb-0.5 sm:mb-1">Need an account?</p>
                                    <p class="text-xs text-blue-700 leading-tight">Contact your system administrator to request access.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center pt-2 sm:pt-3 border-t border-gray-200">
                            <a href="{{ route('org-chart.index') }}" 
                               class="inline-flex items-center text-xs sm:text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Organizational Chart
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="mt-2 sm:mt-3 md:mt-4 text-center animated-card animate-delay-400">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.name', 'MOE') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
