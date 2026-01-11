@extends('layouts.admin')

@section('title', 'Designations')
@section('page-title', 'Designations')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-teal-100 border-2 border-teal-200 flex items-center justify-center">
                    <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-1">Designations</h1>
                    <p class="text-gray-600">Manage designations with salary scales</p>
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

    <!-- Designations Form -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-300">
        <form method="POST" action="{{ route('admin.system-settings.designations.update') }}" id="designationsForm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Available Designations</h2>
                <p class="text-sm text-gray-600 mb-4">Configure designations with their salary scales that can be used when creating positions and assigning users.</p>
            </div>

            <div id="designationsContainer" class="space-y-4">
                @foreach($designations as $key => $designation)
                    <div class="designation-row flex items-center gap-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Key</label>
                            <input type="text" 
                                   name="designations[{{ $loop->index }}][key]" 
                                   value="{{ $key }}"
                                   required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                                   placeholder="e.g., SENIOR_OFFICER">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                            <input type="text" 
                                   name="designations[{{ $loop->index }}][name]" 
                                   value="{{ $designation['name'] ?? '' }}"
                                   required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                                   placeholder="e.g., Senior Officer">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Salary Scale</label>
                            <input type="text" 
                                   name="designations[{{ $loop->index }}][salary_scale]" 
                                   value="{{ $designation['salary_scale'] ?? '' }}"
                                   required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                                   placeholder="e.g., TGSS G">
                        </div>
                        <div class="flex items-end">
                            <button type="button" 
                                    onclick="removeDesignation(this)"
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
                        onclick="addDesignation()"
                        class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Designation
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white font-semibold rounded-lg hover:from-teal-700 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl">
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
let designationIndex = {{ count($designations) }};

function addDesignation() {
    const container = document.getElementById('designationsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'designation-row flex items-center gap-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200';
    newRow.innerHTML = `
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Key</label>
            <input type="text" 
                   name="designations[${designationIndex}][key]" 
                   required
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                   placeholder="e.g., SENIOR_OFFICER">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
            <input type="text" 
                   name="designations[${designationIndex}][name]" 
                   required
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                   placeholder="e.g., Senior Officer">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Salary Scale</label>
            <input type="text" 
                   name="designations[${designationIndex}][salary_scale]" 
                   required
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white"
                   placeholder="e.g., TGSS G">
        </div>
        <div class="flex items-end">
            <button type="button" 
                    onclick="removeDesignation(this)"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    designationIndex++;
}

function removeDesignation(button) {
    if (document.querySelectorAll('.designation-row').length > 1) {
        button.closest('.designation-row').remove();
    } else {
        showWarning('Cannot Remove Designation', 'You must have at least one designation.');
    }
}
</script>
@endsection
