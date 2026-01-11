@extends('layouts.admin')

@section('title', 'Position Fill Rate Report')
@section('page-title', 'Position Fill Rate Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-orange-100 border-2 border-orange-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Position Fill Rate</h2>
                    <p class="text-sm text-gray-600">Analyze position filling rates</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                < Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Total Positions</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalPositions }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Filled Positions</p>
            <p class="text-3xl font-bold text-green-600">{{ $filledPositions }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Vacant Positions</p>
            <p class="text-3xl font-bold text-red-600">{{ $vacantPositions }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Fill Rate</p>
            <p class="text-3xl font-bold text-blue-600">{{ $fillRate }}%</p>
        </div>
    </div>

    <!-- Fill Rate by Unit -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Fill Rate by Unit</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fill Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($byUnit as $unitName => $stats)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $unitName }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stats['total'] }}</td>
                            <td class="px-6 py-4 text-sm text-green-600">{{ $stats['filled'] }}</td>
                            <td class="px-6 py-4 text-sm text-red-600">{{ $stats['vacant'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['fill_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['fill_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
