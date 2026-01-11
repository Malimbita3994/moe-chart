@extends('layouts.admin')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Audit Log Details</h2>
                    <p class="text-sm text-gray-600">Detailed information about this activity</p>
                </div>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="px-6 py-2 rounded-lg font-semibold transition-all bg-gray-200 text-gray-700 hover:bg-gray-300">
                ← Back to Audit Trail
            </a>
        </div>
    </div>

    <!-- Main Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $auditLog->created_at->format('F d, Y \a\t H:i:s') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($auditLog->user)
                            <div class="font-medium">{{ $auditLog->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $auditLog->user->email }}</div>
                        @else
                            <span class="text-gray-400">System</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd class="mt-1">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $auditLog->action_badge_class }}">
                            {{ $auditLog->action }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Model Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->model_type_name }}</dd>
                </div>
                @if($auditLog->model_name)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Model Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->model_name }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Request Information -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Request Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                    <dd class="mt-1 text-sm text-gray-900 break-words">{{ $auditLog->user_agent ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">HTTP Method</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->method ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">URL</dt>
                    <dd class="mt-1 text-sm text-gray-900 break-words">{{ $auditLog->url ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Changes Information (for UPDATE actions) -->
    @if($auditLog->action === 'UPDATE' && $auditLog->changes)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Changes Made</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($auditLog->changes as $field => $change)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 break-words max-w-md">
                                    <div class="bg-red-50 p-2 rounded">
                                        {{ is_array($change['old'] ?? null) ? json_encode($change['old']) : ($change['old'] ?? 'N/A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 break-words max-w-md">
                                    <div class="bg-green-50 p-2 rounded">
                                        {{ is_array($change['new'] ?? null) ? json_encode($change['new']) : ($change['new'] ?? 'N/A') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Old Values (for DELETE actions) -->
    @if($auditLog->action === 'DELETE' && $auditLog->old_values)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Deleted Data</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    <!-- New Values (for CREATE actions) -->
    @if($auditLog->action === 'CREATE' && $auditLog->new_values)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Created Data</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection
