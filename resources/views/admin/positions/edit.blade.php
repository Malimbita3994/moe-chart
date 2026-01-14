@extends('layouts.admin')

@section('title', 'Edit Position')
@section('page-title', 'Edit Position')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Position</h2>
                <p class="text-sm text-gray-600">Update position details in the organization</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-8 border-2 border-gray-300 animate-delay-200">
        <form action="{{ route('admin.positions.update', $position) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Position Name and Abbreviation Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Position Name Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="name">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Position Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $position->name ?? $position->title->name ?? '') }}" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                        placeholder="e.g., Director of HRM, Head of ICT, Chief Accountant">
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enter the specific position name (e.g., "Director of HRM" not just "DIRECTOR")
                    </p>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Position Abbreviation Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="abbreviation">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        Position Abbreviation
                    </label>
                    <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation', $position->abbreviation) }}" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white uppercase"
                        placeholder="e.g., HICT, HRM, CA" maxlength="20">
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Short abbreviation for this position (e.g., HICT for Head of ICT Unit)
                    </p>
                    @error('abbreviation')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
            
            <!-- Title Type Field (Generic Title) -->
            <div class="form-group">
                <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="title_id">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Title Type <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                    <select name="title_id" id="title_id" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                        <option value="">Select Title Type</option>
                        @foreach($titles as $title)
                            <option value="{{ $title->id }}" {{ old('title_id', $position->title_id) == $title->id ? 'selected' : '' }}>{{ $title->name }} ({{ $title->key }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Select the generic title type (DIRECTOR, HOD, CA, etc.) from System Settings
                </p>
                @error('title_id')
                    <p class="text-red-500 text-xs mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- Unit and Reports To Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Primary Unit Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="unit_id">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Primary Organization Unit <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <select name="unit_id" id="unit_id" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $position->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Primary unit for this position (required)
                    </p>
                    @error('unit_id')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Reports To Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="reports_to_position_id">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Reports To
                    </label>
                    <div class="relative">
                        <select name="reports_to_position_id" id="reports_to_position_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                            <option value="">None (Top Level)</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('reports_to_position_id', $position->reports_to_position_id) == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name ?? $pos->title->name ?? 'N/A' }} - {{ $pos->unit->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Designation and Status Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Designation Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="grade_required">
                        <svg class="w-4 h-4 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Designation
                    </label>
                    <div class="relative">
                        <select name="grade_required" id="grade_required"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                            <option value="">-- Select Designation (Optional) --</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}" {{ old('grade_required', $position->grade_required) == $designation->id ? 'selected' : '' }}>
                                    {{ $designation->name }} @if($designation->salary_scale)({{ $designation->salary_scale }})@endif
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Select designation from System Settings (e.g., Senior Officer, Junior Officer, Principal)
                    </p>
                    @error('grade_required')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Status Field -->
                <div class="form-group">
                    <label class="block text-gray-700 text-sm font-semibold mb-2 flex items-center" for="status">
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                            <option value="ACTIVE" {{ old('status', $position->status) === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                            <option value="INACTIVE" {{ old('status', $position->status) === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
            
            <!-- Is Head Position Checkbox -->
            <div class="form-group">
                <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_head" value="1" {{ old('is_head', $position->is_head) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-2 border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                        <div class="ml-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">
                                Is Head Position
                            </span>
                        </div>
                    </label>
                    <p class="text-xs text-gray-500 mt-2 ml-8 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Check this if this position is the head of the organization unit. Only one head position is allowed per unit/division/section. Note: Finance Unit uses Chief Accountant, and Internal Audit Unit uses Chief Internal Auditor (not Director).
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-300">
                <a href="{{ route('admin.positions.index') }}" 
                    class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg transform hover:scale-105" 
                    style="background-color: #D4AF37; color: #1F2937;" 
                    onmouseover="this.style.backgroundColor='#C4A027'" 
                    onmouseout="this.style.backgroundColor='#D4AF37'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Position
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('unit_id');
    const titleSelect = document.getElementById('title_id');
    const assistantDirectorTitleId = @json($assistantDirectorTitle ? $assistantDirectorTitle->id : null);
    const chiefAccountantTitleId = @json($chiefAccountantTitle ? $chiefAccountantTitle->id : null);
    const chiefInternalAuditorTitleId = @json($chiefInternalAuditorTitle ? $chiefInternalAuditorTitle->id : null);
    
    // Store unit data with unit_type and name
    const unitsData = @json($units->mapWithKeys(function($unit) {
        return [$unit->id => [
            'unit_type' => $unit->unit_type,
            'name' => strtoupper($unit->name)
        ]];
    }));
    
    if (unitSelect && titleSelect) {
        unitSelect.addEventListener('change', function() {
            const selectedUnitId = this.value;
            const unitData = unitsData[selectedUnitId];
            
            if (!unitData) return;
            
            const unitType = unitData['unit_type'];
            const unitName = unitData['name'];
            
            let selectedTitleId = null;
            let notificationMessage = '';
            
            // Check for special units first (by name)
            if (unitName.includes('INTERNAL AUDIT') && chiefInternalAuditorTitleId) {
                selectedTitleId = chiefInternalAuditorTitleId;
                notificationMessage = 'Title Type automatically set to "Chief Internal Auditor" for Internal Audit Unit.';
            } else if ((unitName.includes('FINANCE') || unitName.includes('FINANCIAL')) && chiefAccountantTitleId) {
                selectedTitleId = chiefAccountantTitleId;
                notificationMessage = 'Title Type automatically set to "Chief Accountant" for Finance Unit.';
            } else if (unitType === 'SECTION' && assistantDirectorTitleId) {
                selectedTitleId = assistantDirectorTitleId;
                notificationMessage = 'Title Type automatically set to "Assistant Director" for Section units.';
            }
            
            // Apply auto-selection if a title was found
            if (selectedTitleId) {
                const titleOption = titleSelect.querySelector(`option[value="${selectedTitleId}"]`);
                if (titleOption) {
                    titleSelect.value = selectedTitleId;
                    
                    // Show a notification
                    const notification = document.createElement('div');
                    notification.className = 'mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700';
                    notification.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' + notificationMessage;
                    
                    // Remove existing notification if any
                    const existingNotification = titleSelect.parentElement.querySelector('.bg-blue-50');
                    if (existingNotification) {
                        existingNotification.remove();
                    }
                    
                    titleSelect.parentElement.appendChild(notification);
                }
            } else {
                // Remove notification if no auto-selection
                const existingNotification = titleSelect.parentElement.querySelector('.bg-blue-50');
                if (existingNotification) {
                    existingNotification.remove();
                }
            }
        });
        
        // Trigger on page load if unit is already selected
        if (unitSelect.value) {
            unitSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Handle primary unit change - sync with additional units checkboxes
    const primaryUnitSelect = document.getElementById('unit_id');
    const additionalUnitCheckboxes = document.querySelectorAll('input[name="unit_ids[]"]');
    
    if (primaryUnitSelect) {
        primaryUnitSelect.addEventListener('change', function() {
            const primaryUnitId = this.value;
            
            // Uncheck all checkboxes first
            additionalUnitCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = false;
            });
            
            // Check and disable the checkbox matching the primary unit
            additionalUnitCheckboxes.forEach(checkbox => {
                if (checkbox.value === primaryUnitId) {
                    checkbox.checked = true;
                    checkbox.disabled = true;
                }
            });
        });
        
        // Trigger on page load to set initial state
        if (primaryUnitSelect.value) {
            primaryUnitSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
