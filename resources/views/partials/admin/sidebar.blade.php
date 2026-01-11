<!-- Sidebar -->
<aside class="w-64 sidebar-bg fixed left-0 top-0 bottom-0 flex flex-col shadow-2xl z-20">
    <!-- Logo Section -->
    <div class="p-6 border-b sidebar-border flex-shrink-0" style="border-color: #374151;">
        <div class="flex flex-col items-center justify-center">
            <a href="{{ route('admin.dashboard') }}" class="cursor-pointer group transition-all duration-300 hover:scale-105">
                <div class="bg-white rounded-xl p-3 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('image/logo.png') }}" alt="MOE Logo" class="h-12 w-auto object-contain">
                </div>
            </a>
            <h1 class="text-xl font-bold sidebar-text mt-4 text-center tracking-wide">MOE Admin</h1>
            <p class="text-xs mt-1 sidebar-subtitle text-center opacity-75">Organizational Chart</p>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-4 px-3 flex-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gold' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-gold' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <!-- Organization Units -->
        <a href="{{ route('admin.organization-units.index') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.organization-units.*') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.organization-units.*') ? 'bg-blue-500' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.organization-units.*') ? 'text-blue-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="font-medium">Organization Units</span>
        </a>
        
        <!-- Positions -->
        <a href="{{ route('admin.positions.index') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.positions.*') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.positions.*') ? 'bg-orange-500' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.positions.*') ? 'text-orange-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="font-medium">Positions</span>
        </a>
        
        <!-- Advisory Bodies -->
        <a href="{{ route('admin.advisory-bodies.index') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.advisory-bodies.*') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.advisory-bodies.*') ? 'bg-cyan-500' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.advisory-bodies.*') ? 'text-cyan-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="font-medium">Advisory Bodies</span>
        </a>
        
        <!-- System Settings -->
        @if(!auth()->user()->hasRole('viewer'))
            <a href="{{ route('admin.system-settings.index') }}" 
               class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.system-settings.*') ? 'sidebar-active' : '' }}">
                <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.system-settings.*') ? 'bg-purple-500' : 'bg-gray-600' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.system-settings.*') ? 'text-purple-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="font-medium">System Settings</span>
            </a>
        @endif
        
        <!-- Audit Trail -->
        <a href="{{ route('admin.audit-logs.index') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-indigo-500' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.audit-logs.*') ? 'text-indigo-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="font-medium">Audit Trail</span>
        </a>
        
        <!-- Reports -->
        <a href="{{ route('admin.reports.index') }}" 
           class="group flex items-center px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'sidebar-active' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-teal-500' : 'bg-gray-600' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-teal-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="font-medium">Reports</span>
        </a>
        
        <!-- User Management Section (Collapsible) -->
        <div class="mt-2">
            @php
                $isUserManagementActive = request()->routeIs('admin.users.*') || 
                                         request()->routeIs('admin.users.employees.*') || 
                                         request()->routeIs('admin.users.roles.*') || 
                                         request()->routeIs('admin.users.permissions.*');
            @endphp
            <button type="button" 
                    onclick="toggleUserManagement()" 
                    class="w-full group flex items-center justify-between px-4 py-3 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ $isUserManagementActive ? 'sidebar-active' : '' }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ $isUserManagementActive ? 'bg-green-500' : 'bg-gray-600' }}">
                        <svg class="w-5 h-5 {{ $isUserManagementActive ? 'text-green-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold uppercase tracking-wider">User Management</span>
                </div>
                <svg id="user-management-arrow" class="w-4 h-4 sidebar-icon transition-transform duration-300 {{ $isUserManagementActive ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="user-management-submenu" class="overflow-hidden transition-all duration-300 ease-in-out {{ $isUserManagementActive ? 'max-h-96' : 'max-h-0' }}">
                <a href="{{ route('admin.users.index') }}" 
                   class="group flex items-center px-4 py-2.5 pl-12 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.show') ? 'sidebar-active' : '' }}">
                    <div class="w-6 h-6 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.show') ? 'bg-green-500' : 'bg-gray-600' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.show') ? 'text-green-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Employees (Users)</span>
                </a>
                <a href="{{ route('admin.users.roles.index') }}" 
                   class="group flex items-center px-4 py-2.5 pl-12 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.users.roles.*') ? 'sidebar-active' : '' }}">
                    <div class="w-6 h-6 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.users.roles.*') ? 'bg-yellow-500' : 'bg-gray-600' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.users.roles.*') ? 'text-yellow-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Roles</span>
                </a>
                <a href="{{ route('admin.users.permissions.index') }}" 
                   class="group flex items-center px-4 py-2.5 pl-12 mb-1 rounded-lg sidebar-text sidebar-hover transition-all duration-200 {{ request()->routeIs('admin.users.permissions.*') ? 'sidebar-active' : '' }}">
                    <div class="w-6 h-6 rounded-lg bg-opacity-20 flex items-center justify-center mr-3 transition-all duration-200 {{ request()->routeIs('admin.users.permissions.*') ? 'bg-indigo-500' : 'bg-gray-600' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.users.permissions.*') ? 'text-indigo-400' : 'sidebar-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Permissions</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Logout Section -->
    <div class="p-4 sidebar-border flex-shrink-0" style="border-top: 1px solid #374151;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="group flex items-center w-full px-4 py-3 rounded-lg sidebar-text sidebar-hover transition-all duration-200">
                <div class="w-8 h-8 rounded-lg bg-opacity-20 bg-red-500 flex items-center justify-center mr-3 transition-all duration-200 group-hover:bg-opacity-30">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
