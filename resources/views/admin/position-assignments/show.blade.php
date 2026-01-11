@extends('layouts.admin')

@section('title', 'Position Assignment Details')
@section('page-title', 'Position Assignment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="animated-card card-hover bg-white rounded-lg shadow p-6 animate-delay-100">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Assignment Information</h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.position-assignments.edit', $positionAssignment) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                    Edit
                </a>
                <form action="{{ route('admin.position-assignments.destroy', $positionAssignment) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '', 'position assignment')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="animated-card card-hover bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200 animate-delay-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Employee</h4>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($positionAssignment->user->full_name ?? $positionAssignment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">{{ $positionAssignment->user->full_name ?? $positionAssignment->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $positionAssignment->user->email }}</p>
                    </div>
                </div>
            </div>
            
            <div class="animated-card card-hover bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200 animate-delay-300">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Position</h4>
                <p class="font-bold text-gray-800 text-lg">{{ $positionAssignment->position->title->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-semibold">Unit:</span> {{ $positionAssignment->position->unit->name ?? 'N/A' }}
                </p>
            </div>
            
            <div class="animated-card card-hover bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg p-4 border border-yellow-200 animate-delay-400">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Start Date</h4>
                <p class="font-bold text-gray-800 text-lg">{{ $positionAssignment->start_date->format('F d, Y') }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">End Date</h4>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $positionAssignment->end_date ? $positionAssignment->end_date->format('F d, Y') : 'Ongoing' }}
                </p>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Assignment Type</h4>
                <p class="font-bold text-gray-800">{{ $positionAssignment->assignment_type ?? 'N/A' }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Allowance Applicable</h4>
                <p class="font-bold text-gray-800">{{ $positionAssignment->allowance_applicable ?? 'No' }}</p>
            </div>
            
            @if($positionAssignment->authority_reference)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 md:col-span-2">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Authority Reference</h4>
                <p class="font-bold text-gray-800">{{ $positionAssignment->authority_reference }}</p>
            </div>
            @endif
        </div>
        
        <div class="mt-6 pt-6 border-t">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-600">Status:</span>
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $positionAssignment->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $positionAssignment->status }}
                </span>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t">
            <a href="{{ route('admin.position-assignments.index') }}" class="inline-block px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
