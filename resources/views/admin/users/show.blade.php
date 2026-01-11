@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section with Hero Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center text-2xl font-bold text-gray-700">
                            {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1 text-gray-800">{{ $user->full_name ?? $user->name }}</h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-sm font-semibold text-gray-700">
                                    {{ $user->email }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $user->status === 'ACTIVE' ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-gray-300 text-gray-600 border-gray-400' }}">
                                    {{ $user->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                        class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Manage Position Assignment
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $user->full_name ?? $user->name }}', 'user')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-all">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Employee Number</p>
                    <p class="text-lg font-bold text-gray-800">{{ $user->employee_number ?? 'N/A' }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Phone</p>
                    <p class="text-lg font-bold text-gray-800">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Position Assignments</p>
                    <p class="text-xl font-bold text-gray-800">{{ $user->positionAssignments->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Active Assignments</p>
                    <p class="text-xl font-bold text-gray-800">{{ $user->positionAssignments->where('status', 'Active')->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Horizontal Cards Row: System Role, Org Chart Position, Account Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- System Role Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-600">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M5.697 6.697l3.106 3.106a2 2 0 002.394 0l3.106-3.106M5.697 6.697L3 9.394m2.697-2.697L9.394 3m0 0l3.106 3.106M9.394 3L12 5.606m0 0L15.106 2.5M12 5.606L8.894 8.712m3.106-3.106l3.106 3.106M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                System Role
            </h3>
            <div class="space-y-4">
                @if($user->role)
                    <div class="bg-indigo-50 rounded-lg p-4 border-2 border-indigo-200">
                        <p class="text-xs font-semibold text-indigo-600 mb-2">Assigned Role</p>
                        <p class="text-lg font-bold text-indigo-800 mb-1">{{ $user->role->name }}</p>
                        @if($user->role->description)
                            <p class="text-xs text-indigo-600">{{ $user->role->description }}</p>
                        @endif
                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full {{ $user->role->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->role->status }}
                        </span>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">No Role Assigned</p>
                        <p class="text-sm text-gray-500">This user has not been assigned a system role yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Org Chart Position Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-700">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Current Org Chart Position
            </h3>
            <div class="space-y-4">
                @if($currentPosition && $currentPosition->position)
                    <div class="bg-teal-50 rounded-lg p-4 border-2 border-teal-200">
                        <p class="text-xs font-semibold text-teal-600 mb-2">Position</p>
                        <p class="text-lg font-bold text-teal-800 mb-2">{{ $currentPosition->position->name ?? $currentPosition->position->title ?? 'N/A' }}</p>
                        
                        @if($currentPosition->position->unit)
                            <div class="mt-3 pt-3 border-t border-teal-200">
                                <p class="text-xs font-semibold text-teal-600 mb-1">Organization Unit</p>
                                <p class="text-sm font-semibold text-teal-800">{{ $currentPosition->position->unit->name }}</p>
                                <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ strtoupper($currentPosition->position->unit->unit_type) }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="mt-3 pt-3 border-t border-teal-200">
                            <p class="text-xs font-semibold text-teal-600 mb-1">Assignment Type</p>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                @if($currentPosition->assignment_type === 'SUBSTANTIVE') bg-blue-100 text-blue-800
                                @elseif($currentPosition->assignment_type === 'ACTING') bg-yellow-100 text-yellow-800
                                @elseif($currentPosition->assignment_type === 'TEMPORARY') bg-orange-100 text-orange-800
                                @elseif($currentPosition->assignment_type === 'SECONDMENT') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $currentPosition->assignment_type }}
                            </span>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-teal-200">
                            <p class="text-xs font-semibold text-teal-600 mb-1">Start Date</p>
                            <p class="text-sm font-semibold text-teal-800">{{ $currentPosition->start_date->format('F d, Y') }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">No Active Position</p>
                        <p class="text-sm text-gray-500">This user does not have an active position assignment in the organizational chart.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Information Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-800">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Account Information
            </h3>
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                    <p class="text-xs font-semibold text-gray-600 mb-1">Status</p>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->status }}
                    </span>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                    <p class="text-xs font-semibold text-gray-600 mb-1">Account Created</p>
                    <p class="text-sm font-bold text-gray-800">{{ $user->created_at->format('F d, Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $user->created_at->format('h:i A') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                    <p class="text-xs font-semibold text-gray-600 mb-1">Last Updated</p>
                    <p class="text-sm font-bold text-gray-800">{{ $user->updated_at->format('F d, Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $user->updated_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Personal Information -->
        <div class="lg:col-span-1">
            <!-- Personal Details Card -->
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-500">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Information
                </h3>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Name</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Full Name</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->full_name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Email</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Phone</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Employee Number</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->employee_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Position Assignments -->
        <div class="lg:col-span-2">
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-500">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Position Assignments
                    </h3>
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold">
                        {{ $user->positionAssignments->count() }} Total
                    </span>
                </div>

                @if($user->positionAssignments->count() > 0)
                    @php
                        $activeAssignments = $user->positionAssignments->where('status', 'Active');
                        $inactiveAssignments = $user->positionAssignments->where('status', 'Ended');
                    @endphp

                    @if($activeAssignments->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Active Assignments ({{ $activeAssignments->count() }})
                            </h4>
                            <div class="space-y-4">
                                @foreach($activeAssignments as $assignment)
                                    <div class="bg-white rounded-lg p-5 border-2 border-gray-300 hover:shadow-md transition-all">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h5 class="font-bold text-gray-800 text-lg mb-1">{{ $assignment->position->name ?? $assignment->position->title }}</h5>
                                                <p class="text-sm text-gray-600 mb-2">
                                                    <span class="font-semibold">Unit:</span> {{ $assignment->position->unit->name ?? 'N/A' }}
                                                </p>
                                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Started: {{ $assignment->start_date->format('M d, Y') }}
                                                    </span>
                                                    @if($assignment->end_date)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Ends: {{ $assignment->end_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded-full font-semibold">Ongoing</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($inactiveAssignments->count() > 0)
                        <div class="border-t border-gray-300 pt-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                Historical Assignments ({{ $inactiveAssignments->count() }})
                            </h4>
                            <div class="space-y-3">
                                @foreach($inactiveAssignments as $assignment)
                                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-700">{{ $assignment->position->name ?? $assignment->position->title }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $assignment->start_date->format('M d, Y') }} - 
                                                    {{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'N/A' }}
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded-full text-xs font-semibold">
                                                Inactive
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-purple-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 font-semibold mb-2">No position assignments</p>
                        <p class="text-sm text-gray-400">This user has not been assigned to any positions yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition-colors shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>
    </div>
</div>
@endsection
