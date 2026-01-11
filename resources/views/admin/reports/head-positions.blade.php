@extends('layouts.admin')

@section('title', 'Head Positions Report')
@section('page-title', 'Head Positions Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-red-100 border-2 border-red-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Head Positions</h2>
                    <p class="text-sm text-gray-600">All head positions across the organization</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    < Back to Reports
                </a>
                <a href="{{ route('admin.reports.head-positions.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <form method="GET" action="{{ route('admin.reports.head-positions') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Type</label>
                <select name="unit_type" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Types</option>
                    @foreach($unitTypes as $type)
                        <option value="{{ $type }}" {{ request('unit_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Organization Unit</label>
                <select name="unit_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Total Head Positions</p>
            <p class="text-3xl font-bold text-gray-800">{{ $headPositions->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300">
            <p class="text-sm text-gray-600">Filled Head Positions</p>
            <p class="text-3xl font-bold text-green-600">{{ $headPositions->filter(fn($p) => $p->activeAssignments->isNotEmpty())->count() }}</p>
        </div>
    </div>

    <!-- Head Positions Table -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-300 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Head Positions ({{ $headPositions->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($headPositions as $position)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $position->name }}</div>
                                @if($position->abbreviation)
                                    <div class="text-sm text-gray-500">{{ $position->abbreviation }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $position->unit->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $position->unit->unit_type ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $position->title->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $position->designation->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($position->activeAssignments->isNotEmpty())
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Filled</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Vacant</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($position->activeAssignments->isNotEmpty())
                                    {{ $position->activeAssignments->first()->user->full_name ?? 'N/A' }}
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No head positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
