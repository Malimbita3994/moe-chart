<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'MOE') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
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
        
        .animated-card {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        
        .card-hover {
            transition: all 0.3s ease-out;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .bg-gold {
            background-color: #D4AF37;
        }
        
        .text-gold {
            color: #D4AF37;
        }
        
        .bg-charcoal {
            background-color: #1F2937;
        }
        
        .bg-dark-gray {
            background-color: #374151;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo and Header Section -->
        <div class="text-center mb-8 animated-card animate-delay-100">
            <a href="{{ route('org-chart.index') }}" class="inline-block mb-4 group">
                <div class="bg-white rounded-2xl p-4 shadow-lg inline-block group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <img src="{{ asset('image/logo.png') }}" alt="MOE Logo" class="h-16 w-auto object-contain mx-auto">
                </div>
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
            <p class="text-gray-600">Login to access the admin panel</p>
        </div>
        
        <!-- Login Card -->
        <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 border-2 border-gray-200 animate-delay-200">
            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-semibold text-blue-800">{{ session('info') }}</p>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="email">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                            placeholder="Enter your email">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="password">
                        <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white"
                            placeholder="Enter your password">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Remember Me and Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" 
                            class="w-5 h-5 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" 
                       class="text-sm font-semibold text-gold hover:text-yellow-600 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Forgot Password?
                    </a>
                </div>
                
                <!-- Login Button -->
                <button type="submit" 
                    class="w-full py-3 rounded-lg font-bold text-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center justify-center"
                    style="background-color: #D4AF37; color: #1F2937;" 
                    onmouseover="this.style.backgroundColor='#C4A027'" 
                    onmouseout="this.style.backgroundColor='#D4AF37'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login
                </button>
            </form>
            
            <!-- Links Section -->
            <div class="mt-6 text-center">
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-blue-800 mb-1">Need an account?</p>
                            <p class="text-xs text-blue-700">User accounts are created by administrators. Please contact your system administrator to request access.</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-3">
                    <a href="{{ route('org-chart.index') }}" 
                       class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Organizational Chart
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-6 text-center animated-card animate-delay-300">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'MOE') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
