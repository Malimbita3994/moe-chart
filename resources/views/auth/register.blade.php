<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'MOE') }}</title>
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
        .animate-delay-400 { animation-delay: 0.4s; }
        
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
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen flex items-center justify-center p-4 py-8">
    <div class="w-full max-w-2xl">
        <!-- Logo and Header Section -->
        <div class="text-center mb-8 animated-card animate-delay-100">
            <a href="{{ route('org-chart.index') }}" class="inline-block mb-4 group">
                <div class="bg-white rounded-2xl p-4 shadow-lg inline-block group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <img src="{{ asset('image/logo.png') }}" alt="MOE Logo" class="h-16 w-auto object-contain mx-auto">
                </div>
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
            <p class="text-gray-600">Register to access the admin panel</p>
        </div>
        
        <!-- Registration Card -->
        <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 border-2 border-gray-200 animate-delay-200">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="name">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Name <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                placeholder="Enter your name">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- Full Name Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="full_name">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Full Name <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white"
                                placeholder="Enter your full name">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('full_name')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                
                <!-- Email and Phone Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="email">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Email Address <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white"
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
                    
                    <!-- Phone Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="phone">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Phone (Optional)
                        </label>
                        <div class="relative">
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white"
                                placeholder="Enter your phone">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Employee Number Field -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="employee_number">
                        <svg class="w-4 h-4 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        Employee Number (Optional)
                    </label>
                    <div class="relative">
                        <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number') }}"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white"
                            placeholder="Enter your employee number">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Password Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="password">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Password <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white"
                                placeholder="Create a password">
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
                    
                    <!-- Confirm Password Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="password_confirmation">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Confirm Password <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white"
                                placeholder="Confirm your password">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Register Button -->
                <button type="submit" 
                    class="w-full py-3 rounded-lg font-bold text-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center justify-center"
                    style="background-color: #D4AF37; color: #1F2937;" 
                    onmouseover="this.style.backgroundColor='#C4A027'" 
                    onmouseout="this.style.backgroundColor='#D4AF37'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Create Account
                </button>
            </form>
            
            <!-- Links Section -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-semibold text-gold hover:text-yellow-600 transition-colors">Login here</a>
                </p>
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
