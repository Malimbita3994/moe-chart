@extends('layouts.admin')

@section('title', 'Summary Statistics Report')
@section('page-title', 'Summary Statistics Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-yellow-100 border-2 border-yellow-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Summary Statistics</h2>
                    <p class="text-sm text-gray-600">Comprehensive organizational overview</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    < Back to Reports
                </a>
                <a href="{{ route('admin.reports.summary-statistics.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Main Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Units</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_units'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Positions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_positions'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_employees'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Filled Positions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['filled_positions'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Units by Type</h3>
            <div class="space-y-3">
                @forelse($stats['units_by_type'] as $type => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $type }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Positions by Title</h3>
            <div class="space-y-3">
                @forelse($stats['positions_by_title'] as $title => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $title }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Employees by Designation -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Employees by Designation</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($stats['employees_by_designation'] as $designation => $count)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">{{ $designation }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $count }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No data available</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
