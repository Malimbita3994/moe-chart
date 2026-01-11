<!-- Top Navigation Bar -->
<header class="bg-white shadow-md border-b border-gray-200 fixed top-0 right-0 left-64 z-40 backdrop-blur-sm bg-opacity-95">
    <div class="px-6 py-4 flex justify-between items-center">
        <!-- Page Title Section -->
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="w-1 h-8 bg-gold mr-3 rounded-full"></span>
                    @yield('page-title', 'Dashboard')
                </h2>
                @hasSection('page-subtitle')
                    <p class="text-sm text-gray-500 mt-1 ml-4">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center space-x-4">
            <!-- View Org Chart Link -->
            <a href="{{ route('org-chart.index') }}" 
               class="group relative flex items-center px-4 py-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200" 
               target="_blank" 
               title="View Org Chart">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-all duration-200">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </div>
                <span class="ml-2 text-sm font-medium hidden md:block">View Chart</span>
            </a>
            
            <!-- User Profile Dropdown -->
            <div class="relative z-50" x-data="{ open: false }">
                <button @click="open = !open" 
                        @click.away="open = false"
                        class="group flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                        {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="text-left hidden lg:block">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name ?? Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 group-hover:text-gray-700 {{ $isUserManagementActive ?? false ? 'rotate-180' : '' }}" 
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]"
                     x-cloak
                     class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-gray-200 py-2 z-[100] overflow-hidden">
                    <!-- User Info Header -->
                    <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="{{ route('admin.users.show', Auth::user()) }}" 
                           class="group flex items-center px-5 py-3 text-sm text-gray-700 hover:bg-blue-50 transition-all duration-200">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-all duration-200">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">My Profile</p>
                                <p class="text-xs text-gray-500">View and edit your profile</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.settings.index') }}" 
                           class="group flex items-center px-5 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-all duration-200">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3 group-hover:bg-gray-200 transition-all duration-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Settings</p>
                                <p class="text-xs text-gray-500">Manage your preferences</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-1"></div>
                    
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full group flex items-center px-5 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center mr-3 group-hover:bg-red-200 transition-all duration-200">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">Logout</p>
                                <p class="text-xs text-red-500">Sign out of your account</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
