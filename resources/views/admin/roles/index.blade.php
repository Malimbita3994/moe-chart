@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">All Roles</h2>
                    <p class="text-sm text-gray-600">Manage user roles and permissions</p>
                </div>
            </div>
            @if(!auth()->user()->hasRole('viewer'))
                <a href="{{ route('admin.users.roles.create') }}" class="px-6 py-2 rounded-lg font-semibold transition-all" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                    + Add New Role
                </a>
            @endif
        </div>
    </div>

    <div class="animated-card card-hover bg-white rounded-xl shadow-lg overflow-hidden border-2 border-gray-300 animate-delay-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($roles as $role)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $role->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $role->description ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $role->permissions->count() }} permissions
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $role->users->count() }} users
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $role->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $role->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.users.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        @if(!auth()->user()->hasRole('viewer'))
                            <a href="{{ route('admin.users.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.users.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $role->name }}', 'role')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No roles found.</td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-300 bg-gray-50">
        {{ $roles->links() }}
    </div>
</div>
</div>
@endsection
