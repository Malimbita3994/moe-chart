@extends('layouts.admin')

@section('title', 'Organizational Structure Report')
@section('page-title', 'Organizational Structure Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-green-100 border-2 border-green-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Organizational Structure</h2>
                    <p class="text-sm text-gray-600">Complete organizational hierarchy</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    < Back to Reports
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.organizational-structure.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2" title="Export list as PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export List PDF
                    </a>
                    <a href="{{ route('admin.reports.organizational-structure.chart.pdf', request()->query()) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2" title="Export chart diagram as PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Chart PDF
                    </a>
                    <a href="{{ route('admin.reports.organizational-structure.chart.image', request()->query()) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2" title="Export chart diagram as Image">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Export Chart Image
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <form method="GET" action="{{ route('admin.reports.organizational-structure') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Type</label>
                <select name="unit_type" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Types</option>
                    @foreach($unitTypes as $type)
                        <option value="{{ $type }}" {{ request('unit_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Parent Unit</label>
                <select name="parent_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Units</option>
                    @foreach($parentUnits as $unit)
                        <option value="{{ $unit->id }}" {{ request('parent_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Structure Tree -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-300 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Organizational Units ({{ $units->count() }})</h3>
        </div>
        <div class="p-6 space-y-4">
            @forelse($units as $unit)
                <div class="border-l-4 border-green-500 pl-4 py-3 bg-gray-50 rounded-r-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $unit->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $unit->unit_type }}</span>
                                @if($unit->parent)
                                    <span class="text-xs text-gray-500">Under: {{ $unit->parent->name }}</span>
                                @endif
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">{{ $unit->positions->count() }}</span> position(s)
                                @if($unit->positions->where('is_head', true)->count() > 0)
                                    • <span class="font-medium">{{ $unit->positions->where('is_head', true)->count() }}</span> head position(s)
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.organization-units.show', $unit) }}" class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">No organizational units found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
