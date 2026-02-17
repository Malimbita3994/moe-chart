@extends('layouts.admin')

@section('title', 'Search')
@section('page-title', 'Search')

@section('content')
<div class="min-w-0 max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <form method="GET" action="{{ route('admin.search') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center">
            <div class="relative flex-1">
                <span class="absolute -translate-y-1/2 pointer-events-none left-3 top-1/2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Search units, positions, or employees..."
                       class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-gray-800" />
            </div>
            <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                Search
            </button>
        </form>
    </div>

    @if($query === '')
        <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-300 text-center text-gray-500">
            Type a keyword above to search Organization Units, Positions, and Employees.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Units -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Organization Units</h2>
                    <span class="text-xs text-gray-500">{{ $units->count() }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($units as $unit)
                        <a href="{{ route('admin.organization-units.show', $unit) }}" class="block p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $unit->name }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $unit->code ?? 'No code' }} • {{ $unit->unit_type ?? 'Unit' }}
                            </p>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500">No units found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Positions -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Positions</h2>
                    <span class="text-xs text-gray-500">{{ $positions->count() }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($positions as $position)
                        <a href="{{ route('admin.positions.show', $position) }}" class="block p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <p class="font-semibold text-gray-800 text-sm truncate">
                                {{ $position->name ?? 'N/A' }}
                                @if($position->abbreviation)
                                    <span class="text-gray-500">({{ $position->abbreviation }})</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $position->unit->name ?? 'No unit' }}
                            </p>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500">No positions found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Employees -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Employees</h2>
                    <span class="text-xs text-gray-500">{{ $users->count() }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($users as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-700">
                                {{ strtoupper(substr($user->full_name ?? $user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $user->full_name ?? $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500">No employees found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

