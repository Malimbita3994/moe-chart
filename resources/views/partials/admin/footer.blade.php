<!-- Footer -->
<footer class="bg-white border-t border-gray-200 fixed bottom-0 right-0 left-64 z-10 shadow-lg">
    <div class="px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <!-- Left Section -->
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p>&copy; {{ date('Y') }} <span class="font-semibold text-gray-800">{{ config('app.name', 'MOE') }}</span>. All rights reserved.</p>
            </div>
            
            <!-- Center Section -->
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="font-medium text-gray-700">Organizational Chart Management System</p>
            </div>
            
            <!-- Right Section -->
            <div class="flex items-center space-x-4 text-xs text-gray-500">
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>System Online</span>
                </div>
                <span class="hidden md:inline">|</span>
                <span>Version 1.0.0</span>
            </div>
        </div>
    </div>
</footer>
