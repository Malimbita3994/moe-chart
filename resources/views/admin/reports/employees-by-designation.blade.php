@extends('layouts.admin')

@section('title', 'Employees by Designation Report')
@section('page-title', 'Employees by Designation Report')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-indigo-100 border-2 border-indigo-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Employees by Designation</h2>
                    <p class="text-sm text-gray-600">Employee distribution by designation/grades</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                < Back to Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <form method="GET" action="{{ route('admin.reports.employees-by-designation') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                <select name="designation_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Designations</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>
                            {{ $designation->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Position Assigned</label>
                <select name="position_assigned" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees</option>
                    <option value="1" {{ request('position_assigned') == '1' ? 'selected' : '' }}>With Position</option>
                    <option value="0" {{ request('position_assigned') == '0' ? 'selected' : '' }}>Without Position</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary by Designation -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary by Designation</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($byDesignation as $designation => $stats)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">{{ $designation }}</h4>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-medium text-gray-800">{{ $stats['total'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">With Position:</span>
                            <span class="font-medium text-green-600">{{ $stats['with_position'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Without Position:</span>
                            <span class="font-medium text-red-600">{{ $stats['without_position'] }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No data available</p>
            @endforelse
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-300 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Employees ({{ $users->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $user->designation->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($user->activePositionAssignments->isNotEmpty())
                                    {{ $user->activePositionAssignments->first()->position->name ?? 'N/A' }}
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($user->activePositionAssignments->isNotEmpty() && $user->activePositionAssignments->first()->position->unit)
                                    {{ $user->activePositionAssignments->first()->position->unit->name }}
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
