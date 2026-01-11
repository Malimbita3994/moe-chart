@extends('layouts.admin')

@section('title', 'Positions')
@section('page-title', 'Positions')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">All Positions</h2>
                    <p class="text-sm text-gray-600">
                        Manage organizational positions 
                        <span class="font-semibold text-gray-800">({{ $positions->total() }} unique position{{ $positions->total() != 1 ? 's' : '' }})</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.positions.create') }}" class="px-6 py-2 rounded-lg font-semibold transition-all" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                + Add New Position
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-200">
        <form method="GET" action="{{ route('admin.positions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search by position name, abbreviation, title, or unit..."
                               class="w-full px-4 py-2 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Quick Filter -->
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-2">Quick Filter</label>
                    <select name="filter" id="filter" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Positions</option>
                        <option value="filled" {{ request('filter') === 'filled' ? 'selected' : '' }}>Filled</option>
                        <option value="vacant" {{ request('filter') === 'vacant' ? 'selected' : '' }}>Vacant</option>
                    </select>
                </div>
                
                <!-- Unit Filter -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Organization Unit</label>
                    <select name="unit_id" id="unit_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Units</option>
                        @foreach($units ?? [] as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Title Filter -->
                <div>
                    <label for="title_id" class="block text-sm font-medium text-gray-700 mb-2">Title Type</label>
                    <select name="title_id" id="title_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Titles</option>
                        @foreach($titles ?? [] as $title)
                            <option value="{{ $title->id }}" {{ request('title_id') == $title->id ? 'selected' : '' }}>
                                {{ $title->name }}
                            </option>
                        @endforeach
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
                
                <!-- Head Position Filter -->
                <div>
                    <label for="is_head" class="block text-sm font-medium text-gray-700 mb-2">Head Position</label>
                    <select name="is_head" id="is_head" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Positions</option>
                        <option value="1" {{ request('is_head') === '1' ? 'selected' : '' }}>Head Positions Only</option>
                        <option value="0" {{ request('is_head') === '0' ? 'selected' : '' }}>Non-Head Positions</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 font-medium">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Apply Filters
                        </span>
                    </button>
                    <a href="{{ route('admin.positions.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 font-medium">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Reports To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($positions as $position)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $position->name ?? $position->title->name ?? 'N/A' }}</div>
                                @if($position->abbreviation)
                                    <div class="text-xs text-gray-500">({{ $position->abbreviation }})</div>
                                @endif
                                @if($position->is_head)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Head</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($position->units && $position->units->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($position->units as $unit)
                                            @php
                                                $badgeClass = '';
                                                switch($unit->unit_type) {
                                                    case 'DIVISION': $badgeClass = 'bg-blue-100 text-blue-800'; break;
                                                    case 'SECTION': $badgeClass = 'bg-purple-100 text-purple-800'; break;
                                                    case 'UNIT': $badgeClass = 'bg-green-100 text-green-800'; break;
                                                    default: $badgeClass = 'bg-gray-100 text-gray-800'; break;
                                                }
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                                    {{ $unit->unit_type }}
                                                </span>
                                                <span class="text-gray-700">{{ $unit->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($position->reportsTo)
                                    <div class="min-w-0 flex-1 break-words">
                                        <div class="font-medium text-gray-900">{{ $position->reportsTo->name ?? 'N/A' }}</div>
                                        @if($position->reportsTo->unit)
                                            <div class="text-xs text-gray-400">{{ $position->reportsTo->unit->name }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $position->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $position->status }}
                                    </span>
                                    @if($position->is_filled)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Filled</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Vacant</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    @if($position->first_position)
                                        <a href="{{ route('admin.positions.show', $position->first_position) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.positions.edit', $position->first_position) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-300 bg-gray-50">
            {{ $positions->links() }}
        </div>
    </div>
</div>
@endsection
