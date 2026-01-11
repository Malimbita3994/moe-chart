@extends('layouts.admin')

@section('title', 'Unit Types')
@section('page-title', 'Unit Types')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-100 border-2 border-blue-200 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-1">Unit Types</h1>
                    <p class="text-gray-600">Manage available organization unit types</p>
                </div>
            </div>
            <a href="{{ route('admin.system-settings.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-all">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Unit Types Form -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-300">
        <form method="POST" action="{{ route('admin.system-settings.unit-types.update') }}" id="unitTypesForm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Available Unit Types</h2>
                <p class="text-sm text-gray-600 mb-4">Configure the unit types that can be used when creating organization units.</p>
            </div>

            <div id="unitTypesContainer" class="space-y-4">
                @foreach($unitTypes as $key => $label)
                    <div class="unit-type-row flex items-center gap-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Key</label>
                            <input type="text" 
                                   name="unit_types[{{ $loop->index }}][key]" 
                                   value="{{ $key }}"
                                   required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                   placeholder="e.g., MINISTRY">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Label</label>
                            <input type="text" 
                                   name="unit_types[{{ $loop->index }}][label]" 
                                   value="{{ $label }}"
                                   required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                   placeholder="e.g., Ministry">
                        </div>
                        <div class="flex items-end">
                            <button type="button" 
                                    onclick="removeUnitType(this)"
                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex gap-4">
                <button type="button" 
                        onclick="addUnitType()"
                        class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Unit Type
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let unitTypeIndex = {{ count($unitTypes) }};

function addUnitType() {
    const container = document.getElementById('unitTypesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'unit-type-row flex items-center gap-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200';
    newRow.innerHTML = `
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Key</label>
            <input type="text" 
                   name="unit_types[${unitTypeIndex}][key]" 
                   required
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                   placeholder="e.g., MINISTRY">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Label</label>
            <input type="text" 
                   name="unit_types[${unitTypeIndex}][label]" 
                   required
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                   placeholder="e.g., Ministry">
        </div>
        <div class="flex items-end">
            <button type="button" 
                    onclick="removeUnitType(this)"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    unitTypeIndex++;
}

function removeUnitType(button) {
    if (document.querySelectorAll('.unit-type-row').length > 1) {
        button.closest('.unit-type-row').remove();
    } else {
        showWarning('Cannot Remove Unit Type', 'You must have at least one unit type.');
    }
}
</script>
@endsection
