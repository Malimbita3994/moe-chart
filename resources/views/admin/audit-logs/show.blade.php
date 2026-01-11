@extends('layouts.admin')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300 animate-delay-100">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl {{ $auditLog->action_badge_class }} bg-opacity-20 border-2 flex items-center justify-center" style="border-color: currentColor;">
                    @if($auditLog->action === 'CREATE')
                        <svg class="w-8 h-8 {{ $auditLog->action_badge_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    @elseif($auditLog->action === 'UPDATE')
                        <svg class="w-8 h-8 {{ $auditLog->action_badge_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    @elseif($auditLog->action === 'DELETE')
                        <svg class="w-8 h-8 {{ $auditLog->action_badge_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    @else
                        <svg class="w-8 h-8 {{ $auditLog->action_badge_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Audit Log Details</h2>
                    <p class="text-sm text-gray-600 mt-1">Activity ID: #{{ $auditLog->id }} • {{ $auditLog->created_at->format('F d, Y \a\t H:i:s') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-6 py-3 rounded-lg font-semibold transition-all bg-gray-100 text-gray-700 hover:bg-gray-200 border-2 border-gray-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Audit Trail
            </a>
        </div>
    </div>

    <!-- Main Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-200">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Basic Information
            </h3>
            <dl class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Date & Time
                    </dt>
                    <dd class="text-sm font-bold text-gray-900 mt-1">
                        {{ $auditLog->created_at->format('F d, Y') }}
                        <span class="text-gray-600 font-normal">at {{ $auditLog->created_at->format('H:i:s') }}</span>
                    </dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        User
                    </dt>
                    <dd class="mt-1">
                        @if($auditLog->user)
                            <div class="font-bold text-gray-900">{{ $auditLog->user->name }}</div>
                            <div class="text-xs text-gray-600 mt-1">{{ $auditLog->user->email }}</div>
                        @else
                            <span class="text-gray-500 italic">System</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Action
                    </dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-bold rounded-full {{ $auditLog->action_badge_class }}">
                            {{ $auditLog->action }}
                        </span>
                    </dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                        Model Type
                    </dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900">{{ $auditLog->model_type_name }}</dd>
                </div>
                @if($auditLog->model_name)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Model Name
                    </dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900">{{ $auditLog->model_name }}</dd>
                </div>
                @endif
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 leading-relaxed">{{ $auditLog->description ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Request Information -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-300">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                Request Information
            </h3>
            <dl class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        IP Address
                    </dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900 font-mono">{{ $auditLog->ip_address ?? 'N/A' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        User Agent
                    </dt>
                    <dd class="mt-1 text-xs text-gray-700 break-words leading-relaxed">{{ $auditLog->user_agent ?? 'N/A' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        HTTP Method
                    </dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                            {{ $auditLog->method ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-xs font-semibold text-gray-600 mb-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        URL
                    </dt>
                    <dd class="mt-1 text-xs text-gray-700 break-all font-mono leading-relaxed">{{ $auditLog->url ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Changes Information (for UPDATE actions) -->
    @if($auditLog->action === 'UPDATE' && $auditLog->changes)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Changes Made
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Field</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Old Value</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($auditLog->changes as $field => $change)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm break-words" style="max-width: 400px;">
                                    <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded-r">
                                        <code class="text-xs text-red-800">
                                            {{ is_array($change['old'] ?? null) ? json_encode($change['old'], JSON_PRETTY_PRINT) : ($change['old'] ?? 'N/A') }}
                                        </code>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm break-words" style="max-width: 400px;">
                                    <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded-r">
                                        <code class="text-xs text-green-800">
                                            {{ is_array($change['new'] ?? null) ? json_encode($change['new'], JSON_PRETTY_PRINT) : ($change['new'] ?? 'N/A') }}
                                        </code>
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
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-red-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Deleted Data
            </h3>
            <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-r overflow-x-auto">
                <pre class="text-xs text-red-900 font-mono whitespace-pre-wrap leading-relaxed">{{ e(json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}</pre>
            </div>
        </div>
    @endif

    <!-- New Values (for CREATE actions) -->
    @if($auditLog->action === 'CREATE' && $auditLog->new_values)
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 border-2 border-green-300 animate-delay-400 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Created Data
            </h3>
            <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-r overflow-x-auto">
                <pre class="text-xs text-green-900 font-mono whitespace-pre-wrap leading-relaxed">{{ e(json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection
