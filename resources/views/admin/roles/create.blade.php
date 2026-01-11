@extends('layouts.admin')

@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
    <form action="{{ route('admin.users.roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Role Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., Administrator, Manager, Editor">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
            <textarea name="description" id="description" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Describe the role's purpose and responsibilities">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status *</label>
            <select name="status" id="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                <option value="INACTIVE" {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Permissions Selection -->
        <div class="mb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assign Permissions</h3>
            
            @if($permissions->isEmpty())
                <p class="text-gray-500 mb-4">No permissions available. <a href="{{ route('admin.users.permissions.create') }}" class="text-blue-600 hover:text-blue-800">Create permissions first</a>.</p>
            @else
                @foreach($permissions as $group => $groupPermissions)
                    <div class="mb-6 permission-group" data-group="{{ $group }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-700 capitalize text-lg">{{ $group ?: 'General' }}</h4>
                            <div class="flex gap-2">
                                <button type="button" onclick="selectAllInGroup('{{ $group }}')" 
                                    class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                    Select All
                                </button>
                                <button type="button" onclick="deselectAllInGroup('{{ $group }}')" 
                                    class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Custom Multi-select with checkboxes -->
                        <div class="border-2 border-gray-300 rounded-lg bg-white shadow-sm" style="max-height: 200px; overflow-y: auto;">
                            <div class="p-3 space-y-2">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-start p-2 rounded hover:bg-blue-50 cursor-pointer transition-colors">
                                        <input type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->id }}"
                                            id="perm-{{ $group }}-{{ $permission->id }}"
                                            class="permission-checkbox-{{ $group }} mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                            onchange="updateSelectedCount('{{ $group }}')">
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                            @if($permission->description)
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <span id="selected-count-{{ $group }}" class="font-semibold text-blue-600">0</span> of {{ $groupPermissions->count() }} selected
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.users.roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 btn-primary rounded-lg font-semibold">
                Create Role
            </button>
        </div>
    </form>
</div>

<script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($permissions as $group => $groupPermissions)
            updateSelectedCount('{{ $group }}');
        @endforeach
    });

    function selectAllInGroup(group) {
        const checkboxes = document.querySelectorAll('.permission-checkbox-' + group);
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount(group);
    }

    function deselectAllInGroup(group) {
        const checkboxes = document.querySelectorAll('.permission-checkbox-' + group);
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount(group);
    }

    function updateSelectedCount(group) {
        const checkboxes = document.querySelectorAll('.permission-checkbox-' + group);
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const countElement = document.getElementById('selected-count-' + group);
        if (countElement) {
            countElement.textContent = checkedCount;
        }
    }
</script>
@endsection
