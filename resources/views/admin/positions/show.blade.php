@extends('layouts.admin')

@section('title', 'Position Details')
@section('page-title', 'Position Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section with Hero Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold border-2 border-gray-300">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1 text-gray-800">{{ $position->name ?? $position->title->name ?? 'N/A' }}</h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($position->is_head)
                                    <span class="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-sm font-semibold flex items-center text-gray-700">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Head Position
                                    </span>
                                @endif
                                <span class="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-sm font-semibold text-gray-700">
                                    {{ $position->grade ?? 'N/A' }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $position->status === 'ACTIVE' ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-gray-300 text-gray-600 border-gray-400' }}">
                                    {{ $position->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if(!auth()->user()->hasRole('viewer'))
                        <a href="{{ route('admin.positions.edit', $position) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $position->name }}', 'position')">
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
                    <p class="text-sm font-semibold text-gray-600 mb-1">Organization Unit</p>
                    <p class="text-lg font-bold text-gray-800 truncate">{{ $position->unit->name ?? 'N/A' }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Active Assignments</p>
                    <p class="text-xl font-bold text-gray-800">{{ $position->assignments->where('status', 'Active')->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Subordinates</p>
                    <p class="text-xl font-bold text-gray-800">{{ $position->subordinates->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Total Assignments</p>
                    <p class="text-xl font-bold text-gray-800">{{ $position->assignments->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Unit, Reports To, Subordinates -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Organization Unit Card -->
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-500">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Organization Unit
                </h3>
                @if($position->unit)
                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300">
                        <p class="font-semibold text-gray-800 mb-1">{{ $position->unit->name }}</p>
                        <p class="text-sm text-gray-600 mb-3">{{ strtoupper($position->unit->unit_type) }}</p>
                        <a href="{{ route('admin.organization-units.show', $position->unit) }}" class="text-sm text-gray-700 hover:text-gray-900 font-semibold flex items-center">
                            View Unit Details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-gray-500 italic">No unit assigned</p>
                    </div>
                @endif
            </div>

            <!-- Reports To Card -->
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-600">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    Reports To
                </h3>
                @if($position->reportsTo)
                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300">
                        <p class="font-semibold text-gray-800 mb-1">{{ $position->reportsTo->name ?? 'N/A' }}</p>
                        @if($position->reportsTo->title)
                            <p class="text-xs text-gray-500 mb-1">Title: {{ $position->reportsTo->title->name ?? 'N/A' }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mb-3">
                            Unit: {{ $position->reportsTo->unit->name ?? 'N/A' }}
                        </p>
                        <a href="{{ route('admin.positions.show', $position->reportsTo) }}" class="text-sm text-gray-700 hover:text-gray-900 font-semibold flex items-center">
                            View Position
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-gray-500 italic">This position reports to no one (top level)</p>
                    </div>
                @endif
            </div>

            <!-- Subordinates Card -->
            @if($position->subordinates->count() > 0)
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-700">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Subordinate Positions
                    </span>
                    <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded-full text-xs font-semibold">
                        {{ $position->subordinates->count() }}
                    </span>
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($position->subordinates as $subordinate)
                        <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300 hover:bg-gray-100 transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 mb-1">{{ $subordinate->name ?? $subordinate->title->name ?? 'N/A' }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded text-xs font-semibold">
                                            {{ $subordinate->grade ?? 'N/A' }}
                                        </span>
                                        <span class="px-2 py-1 rounded text-xs font-semibold border {{ $subordinate->status === 'ACTIVE' ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-gray-300 text-gray-600 border-gray-400' }}">
                                            {{ $subordinate->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $subordinate->unit->name ?? 'N/A' }}</p>
                                </div>
                                <a href="{{ route('admin.positions.show', $subordinate) }}" class="ml-3 text-purple-600 hover:text-purple-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Advisory Bodies Card -->
            @if($position->advisoryBodies && $position->advisoryBodies->count() > 0)
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-800">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Advisory Bodies
                    </span>
                    <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded-full text-xs font-semibold">
                        {{ $position->advisoryBodies->count() }}
                    </span>
                </h3>
                <div class="space-y-3">
                    @foreach($position->advisoryBodies as $advisoryBody)
                        <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300">
                            <p class="font-semibold text-gray-800">{{ $advisoryBody->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Assignments -->
        <div class="lg:col-span-2">
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-500">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Position Assignments
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm btn-primary rounded-lg font-semibold" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                        Assign via Users
                    </a>
                </div>

                @if($position->assignments->count() > 0)
                    <!-- Active Assignments Section -->
                    @php
                        $activeAssignments = $position->assignments->where('status', 'Active');
                        $inactiveAssignments = $position->assignments->where('status', 'Ended');
                    @endphp

                    @if($activeAssignments->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                Active Assignments ({{ $activeAssignments->count() }})
                            </h4>
                            <div class="space-y-4">
                                @foreach($activeAssignments as $assignment)
                                    <div class="bg-white rounded-lg p-5 border-2 border-gray-300 hover:shadow-md transition-all">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center gap-4 flex-1">
                                                <div class="w-14 h-14 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center text-gray-700 font-bold text-lg">
                                                    {{ strtoupper(substr($assignment->user->full_name ?? $assignment->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-bold text-gray-800 text-lg mb-1">{{ $assignment->user->full_name ?? $assignment->user->name }}</h5>
                                                    <p class="text-sm text-gray-600 mb-2">{{ $assignment->user->email }}</p>
                                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Started: {{ $assignment->start_date->format('M d, Y') }}
                                                        </span>
                                                        @if($assignment->end_date)
                                                            <span class="flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Ends: {{ $assignment->end_date->format('M d, Y') }}
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 bg-gray-200 border border-gray-300 text-gray-700 rounded-full font-semibold">Ongoing</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                @if(!auth()->user()->hasRole('viewer'))
                                                    <a href="{{ route('admin.users.edit', $assignment->user) }}" class="text-blue-600 hover:text-blue-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.users.show', $assignment->user) }}" class="text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Inactive/Historical Assignments Section -->
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
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                                    {{ strtoupper(substr($assignment->user->full_name ?? $assignment->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700">{{ $assignment->user->full_name ?? $assignment->user->name }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $assignment->start_date->format('M d, Y') }} - 
                                                        {{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'N/A' }}
                                                    </p>
                                                </div>
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
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-500 font-semibold mb-2">No assignments for this position</p>
                        <p class="text-sm text-gray-400 mb-4">Assign an employee to this position through User Management</p>
                        <a href="{{ route('admin.users.index') }}" class="inline-block px-6 py-2 btn-primary rounded-lg font-semibold" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                            Go to User Management
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.positions.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition-colors shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Positions
        </a>
    </div>
</div>
@endsection
