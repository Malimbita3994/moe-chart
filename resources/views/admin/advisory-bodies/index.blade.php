@extends('layouts.admin')

@section('title', 'Advisory Bodies')
@section('page-title', 'Advisory Bodies')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">All Advisory Bodies</h2>
                    <p class="text-sm text-gray-600">Manage advisory bodies and committees</p>
                </div>
            </div>
            @if(!auth()->user()->hasRole('viewer'))
                <a href="{{ route('admin.advisory-bodies.create') }}" class="px-6 py-2 rounded-lg font-semibold transition-all" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                    + Add New Advisory Body
                </a>
            @endif
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-200">
        <form method="GET" action="{{ route('admin.advisory-bodies.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name or reporting position..."
                               class="w-full px-4 py-2 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Reporting Position Filter -->
                <div>
                    <label for="reports_to_position_id" class="block text-sm font-medium text-gray-700 mb-2">Reports To</label>
                    <select name="reports_to_position_id" id="reports_to_position_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Positions</option>
                        @foreach($positions ?? [] as $position)
                            <option value="{{ $position->id }}" {{ request('reports_to_position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->name ?? ($position->title->name ?? 'N/A') }} - {{ $position->unit->name ?? 'N/A' }}
                            </option>
                        @endforeach
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
                    <a href="{{ route('admin.advisory-bodies.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 font-medium">
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/3">Reports To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($advisoryBodies as $advisoryBody)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $advisoryBody->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($advisoryBody->reportsTo)
                            <div class="min-w-0 flex-1 break-words">
                                <div class="font-medium text-gray-900">{{ $advisoryBody->reportsTo->name ?? 'N/A' }}</div>
                                @if($advisoryBody->reportsTo->unit)
                                    <div class="text-xs text-gray-400">{{ $advisoryBody->reportsTo->unit->name }}</div>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400 italic">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.advisory-bodies.show', $advisoryBody) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if(!auth()->user()->hasRole('viewer'))
                                <a href="{{ route('admin.advisory-bodies.edit', $advisoryBody) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.advisory-bodies.destroy', $advisoryBody) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $advisoryBody->name }}', 'advisory body')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No advisory bodies found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    
        <div class="px-6 py-4 border-t border-gray-300 bg-gray-50">
            {{ $advisoryBodies->links() }}
        </div>
    </div>
</div>
@endsection
