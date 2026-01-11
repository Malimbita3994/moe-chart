@extends('layouts.admin')

@section('title', 'Role Details')
@section('page-title', 'Role Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section with Hero Card -->
    <div class="animated-card card-hover bg-white rounded-2xl shadow-2xl p-8 mb-6 border-2 border-gray-300 relative overflow-hidden animate-delay-100">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-yellow-200 to-yellow-300 border-2 border-gray-300 flex items-center justify-center">
                            <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1 text-gray-800">{{ $role->name }}</h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-sm font-semibold text-gray-700">
                                    {{ $role->slug }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $role->status === 'ACTIVE' ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-gray-300 text-gray-600 border-gray-400' }}">
                                    {{ $role->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if(!auth()->user()->hasRole('viewer'))
                        <a href="{{ route('admin.users.roles.edit', $role) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.users.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return handleDeleteSubmit(event, '{{ $role->name }}', 'role')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-all">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Permissions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $role->permissions->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Users Assigned</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $role->users->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="animated-card card-hover bg-white rounded-xl p-5 border-2 border-gray-300 shadow-sm animate-delay-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Status</p>
                    <p class="text-lg font-bold {{ $role->status === 'ACTIVE' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $role->status }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full {{ $role->status === 'ACTIVE' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $role->status === 'ACTIVE' ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Role Details Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Role Information
            </h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Name</p>
                    <p class="text-base text-gray-800">{{ $role->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Slug</p>
                    <p class="text-base text-gray-800 font-mono">{{ $role->slug }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Description</p>
                    <p class="text-base text-gray-800">{{ $role->description ?? 'No description provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-300">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Permissions ({{ $role->permissions->count() }})
            </h3>
            @if($role->permissions->count() > 0)
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($role->permissions->groupBy('group') as $group => $groupPermissions)
                        <div class="border-l-4 border-indigo-400 pl-3">
                            <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase">{{ $group ?: 'General' }}</h4>
                            <div class="space-y-1">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $permission->name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">No permissions assigned to this role.</p>
            @endif
        </div>
    </div>

    <!-- Users with this Role -->
    @if($role->users->count() > 0)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg overflow-hidden border-2 border-gray-300 animate-delay-400">
            <div class="p-6 border-b border-gray-300">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Users with this Role ({{ $role->users->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($role->users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center text-gray-700 font-bold text-sm mr-3 flex-shrink-0">
                                            {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 break-words">{{ $user->full_name ?? $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-8 border-2 border-gray-300 animate-delay-400 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-gray-500 font-semibold mb-2">No users assigned to this role</p>
            <p class="text-sm text-gray-400">Users can be assigned to this role through the User Management page</p>
        </div>
    @endif

    <!-- Back Link -->
    <div class="mt-6">
        <a href="{{ route('admin.users.roles.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Roles List
        </a>
    </div>
</div>
@endsection
