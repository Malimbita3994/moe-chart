@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">All Users</h2>
                    <p class="text-sm text-gray-600">Manage system users and employees</p>
                </div>
            </div>
            @if(!auth()->user()->hasRole('viewer'))
                <a href="{{ route('admin.users.create') }}" class="px-6 py-2 rounded-lg font-semibold transition-all" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                    + Add New User
                </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Users Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-5 border-2 border-gray-300 animate-delay-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 border border-blue-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-5 border-2 border-gray-300 animate-delay-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Active Users</p>
                    <p class="text-3xl font-bold text-green-800">{{ $stats['active_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100, 1) : 0 }}% of total
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 border border-green-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Users with Position Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-5 border-2 border-gray-300 animate-delay-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">With Position</p>
                    <p class="text-3xl font-bold text-purple-800">{{ $stats['users_with_position'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stats['users_without_position'] ?? 0 }} without position
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 border border-purple-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Users with Role Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-5 border-2 border-gray-300 animate-delay-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">With System Role</p>
                    <p class="text-3xl font-bold text-indigo-800">{{ $stats['users_with_role'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stats['users_without_role'] ?? 0 }} without role
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 border border-indigo-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M5.697 6.697l3.106 3.106a2 2 0 002.394 0l3.106-3.106M5.697 6.697L3 9.394m2.697-2.697L9.394 3m0 0l3.106 3.106M9.394 3L12 5.606m0 0L15.106 2.5M12 5.606L8.894 8.712m3.106-3.106l3.106 3.106M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics Row (if needed) -->
    @if(isset($usersByRole) && $usersByRole->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach($usersByRole->take(3) as $roleStat)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-5 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">{{ $roleStat['role_name'] }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $roleStat['count'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-200">
        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name, email, employee number..."
                               class="w-full px-4 py-2 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <!-- Designation Filter -->
                <div>
                    <label for="designation_id" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                    <select name="designation_id" id="designation_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Designations</option>
                        @foreach($designations ?? [] as $designation)
                            <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Position Assignment Filter -->
                <div>
                    <label for="has_position" class="block text-sm font-medium text-gray-700 mb-2">Position Assignment</label>
                    <select name="has_position" id="has_position" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Users</option>
                        <option value="yes" {{ request('has_position') === 'yes' ? 'selected' : '' }}>With Position</option>
                        <option value="no" {{ request('has_position') === 'no' ? 'selected' : '' }}>Without Position</option>
                    </select>
                </div>
                
                <!-- Unit Filter -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Organization Unit</label>
                    <select name="unit_id" id="unit_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Units</option>
                        @foreach($units ?? [] as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->unit_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="lg:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 font-medium">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Apply Filters
                        </span>
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 font-medium">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="animated-card card-hover bg-white rounded-xl shadow-lg overflow-hidden border-2 border-gray-300 animate-delay-300">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/4">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">System Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center text-gray-700 font-bold text-sm mr-3 flex-shrink-0">
                                {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 break-words">{{ $user->full_name ?? $user->name }}</div>
                                @if($user->employee_number)
                                    <div class="text-xs text-gray-500">#{{ $user->employee_number }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        @if($user->phone)
                            <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->role)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $user->role->name }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400 italic">No role assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user->activePositionAssignments->first())
                            <div class="text-sm text-gray-900">{{ $user->activePositionAssignments->first()->position->name ?? $user->activePositionAssignments->first()->position->title }}</div>
                            <div class="text-xs text-gray-500">{{ $user->activePositionAssignments->first()->position->unit->name ?? 'N/A' }}</div>
                        @else
                            <span class="text-sm text-gray-400 italic">No assignment</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors text-xs font-semibold">
                                View Details
                            </a>
                            @if(!auth()->user()->hasRole('viewer'))
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $user->full_name ?? $user->name }}', 'user')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs">(You)</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found.</td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-300 bg-gray-50">
        {{ $users->links() }}
    </div>
</div>
</div>
@endsection
