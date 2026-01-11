@extends('layouts.admin')

@section('title', 'Organization Unit Details')
@section('page-title', 'Organization Unit Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section with Hero Card -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300 relative overflow-hidden animate-delay-100">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center text-2xl font-bold text-gray-700">
                            {{ strtoupper(substr($organizationUnit->name, 0, 2)) }}
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1 text-gray-800">{{ $organizationUnit->name }}</h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-sm font-semibold text-gray-700">
                                    {{ strtoupper($organizationUnit->unit_type) }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $organizationUnit->status === 'ACTIVE' ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-gray-300 text-gray-600 border-gray-400' }}">
                                    {{ $organizationUnit->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.organization-units.edit', $organizationUnit) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('admin.organization-units.destroy', $organizationUnit) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $organizationUnit->name }}', 'organization unit')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-all">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="animated-card card-hover bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 shadow-sm animate-delay-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Unit Code</p>
                    <p class="text-xl font-bold text-gray-800">{{ $organizationUnit->code ?? 'N/A' }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200 shadow-sm animate-delay-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Level</p>
                    <p class="text-xl font-bold text-gray-800">Level {{ $organizationUnit->level }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-green-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 shadow-sm animate-delay-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Child Units</p>
                    <p class="text-xl font-bold text-gray-800">{{ $organizationUnit->children->count() }}</p>
                </div>
                <div class="icon-container w-12 h-12 rounded-full bg-purple-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5 border border-orange-200 shadow-sm animate-delay-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Positions</p>
                    <p class="text-xl font-bold text-gray-800">{{ $organizationUnit->positions->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Hierarchy & Parent Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Parent Unit Card -->
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border border-gray-200 animate-delay-500">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Parent Unit
                </h3>
                @if($organizationUnit->parent)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-200">
                        <p class="font-semibold text-gray-800 mb-1">{{ $organizationUnit->parent->name }}</p>
                        <p class="text-sm text-gray-600 mb-3">{{ strtoupper($organizationUnit->parent->unit_type) }}</p>
                        <a href="{{ route('admin.organization-units.show', $organizationUnit->parent) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center">
                            View Details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-500 italic">This is a root unit (no parent)</p>
                    </div>
                @endif
            </div>

            <!-- Child Units Card -->
            @if($organizationUnit->children->count() > 0)
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border border-gray-200 animate-delay-600">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Child Units
                    </span>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                        {{ $organizationUnit->children->count() }}
                    </span>
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($organizationUnit->children as $child)
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 mb-1">{{ $child->name }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-semibold">
                                            {{ strtoupper($child->unit_type) }}
                                        </span>
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $child->status === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $child->status }}
                                        </span>
                                    </div>
                                    @if($child->code)
                                        <p class="text-xs text-gray-500">Code: {{ $child->code }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('admin.organization-units.show', $child) }}" class="ml-3 text-purple-600 hover:text-purple-800">
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
        </div>

        <!-- Right Column: Positions -->
        <div class="lg:col-span-2">
            <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border border-gray-200 animate-delay-500">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Positions
                    </h3>
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold">
                        {{ $organizationUnit->positions->count() }} Total
                    </span>
                </div>

                @if($organizationUnit->positions->count() > 0)
                    <div class="space-y-4">
                        @foreach($organizationUnit->positions as $position)
                            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-lg p-5 border border-gray-200 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-bold text-gray-800 text-lg">{{ $position->name ?? $position->title }}</h4>
                                            @if($position->is_head)
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Head
                                                </span>
                                            @endif
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $position->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $position->status }}
                                            </span>
                                        </div>
                                        @if($position->grade)
                                            <p class="text-sm text-gray-600 mb-2">
                                                <span class="font-semibold">Designation:</span> {{ $position->grade }}
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.positions.show', $position) }}" class="ml-4 text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>

                                <!-- Active Assignments -->
                                @php
                                    $activeAssignments = $position->activeAssignments;
                                @endphp
                                @if($activeAssignments->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-600 mb-2">Assigned Employees:</p>
                                        <div class="space-y-2">
                                            @foreach($activeAssignments as $assignment)
                                                <div class="flex items-center gap-3 bg-white rounded-lg p-3 border border-gray-100">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                        {{ strtoupper(substr($assignment->user->full_name ?? $assignment->user->name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-800 text-sm">{{ $assignment->user->full_name ?? $assignment->user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $assignment->user->email }}</p>
                                                    </div>
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                                        Active
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <p class="text-sm text-yellow-800 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Position is vacant
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-semibold">No positions assigned to this unit</p>
                        <a href="{{ route('admin.positions.create') }}" class="mt-4 inline-block px-4 py-2 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                            Create a Position
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.organization-units.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition-colors shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Organization Units
        </a>
    </div>
</div>
@endsection
