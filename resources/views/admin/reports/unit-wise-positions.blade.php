@extends('layouts.admin')

@section('title', 'Unit-wise Positions Report')
@section('page-title', 'Unit-wise Positions Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-cyan-100 border-2 border-cyan-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Unit-wise Positions</h2>
                    <p class="text-sm text-gray-600">Positions organized by organizational units</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                < Back to Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <form method="GET" action="{{ route('admin.reports.unit-wise-positions') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Type</label>
                <select name="unit_type" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">All Types</option>
                    @foreach($unitTypes as $type)
                        <option value="{{ $type }}" {{ request('unit_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Parent Unit</label>
                <select name="parent_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">All Units</option>
                    @foreach($parentUnits as $unit)
                        <option value="{{ $unit->id }}" {{ request('parent_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Units and Positions -->
    <div class="space-y-6">
        @forelse($units as $unit)
            <div class="bg-white rounded-xl shadow-lg border-2 border-gray-300 overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $unit->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800">{{ $unit->unit_type }}</span>
                                @if($unit->parent)
                                    <span class="text-xs text-gray-500">Under: {{ $unit->parent->name }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold bg-gray-200 text-gray-800 rounded-lg">
                            {{ $unit->positions->count() }} position(s)
                        </span>
                    </div>
                </div>
                @if($unit->positions->count() > 0)
                    <div class="p-6">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assigned</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($unit->positions as $position)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $position->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $position->title->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            @if($position->is_head)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Head</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Staff</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($position->activeAssignments->count() > 0)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        No positions assigned to this unit.
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-lg p-12 border-2 border-gray-300 text-center">
                <p class="text-gray-500">No organizational units found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
