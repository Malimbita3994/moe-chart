@extends('layouts.admin')

@section('title', 'Advisory Body Details')
@section('page-title', 'Advisory Body Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="animated-card card-hover bg-white rounded-lg shadow p-6 animate-delay-100">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800">Advisory Body Information</h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.advisory-bodies.edit', $advisoryBody) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                    Edit
                </a>
                <form action="{{ route('admin.advisory-bodies.destroy', $advisoryBody) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $advisoryBody->name }}', 'advisory body')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="animated-card card-hover bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 animate-delay-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Advisory Body Name</h4>
                <p class="font-bold text-gray-800 text-xl">{{ $advisoryBody->name }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Reports To</h4>
                @if($advisoryBody->reportsTo)
                    <p class="font-bold text-gray-800 text-lg">{{ $advisoryBody->reportsTo->name ?? 'N/A' }}</p>
                    @if($advisoryBody->reportsTo->title)
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-semibold">Title:</span> {{ $advisoryBody->reportsTo->title->name }}
                        </p>
                    @endif
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-semibold">Unit:</span> {{ $advisoryBody->reportsTo->unit->name ?? 'N/A' }}
                    </p>
                @else
                    <p class="text-gray-500 italic">No reporting position assigned</p>
                @endif
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t">
            <a href="{{ route('admin.advisory-bodies.index') }}" class="inline-block px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
