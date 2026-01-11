@extends('admin.reports.pdf.layout')

@section('title', 'Audit Trail Report')

@php
    $title = 'Audit Trail Report';
    $subtitle = 'System activities and changes log';
@endphp

@section('content')
<div class="stat-card" style="margin-bottom: 15px;">
    <div class="label">Total Audit Logs</div>
    <div class="value">{{ $auditLogs->count() }}</div>
</div>

@if(request('date_from') || request('date_to'))
<div class="meta-info" style="margin-bottom: 15px;">
    <div><strong>Date Range:</strong> 
        {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'All Time' }} 
        - 
        {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Today' }}
    </div>
    @if(request('action'))
        <div><strong>Action Filter:</strong> {{ request('action') }}</div>
    @endif
    @if(request('user_id'))
        <div><strong>User Filter:</strong> {{ \App\Models\User::find(request('user_id'))->name ?? 'N/A' }}</div>
    @endif
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Date/Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Model</th>
            <th>Description</th>
            <th>IP Address</th>
        </tr>
    </thead>
    <tbody>
        @forelse($auditLogs as $log)
            <tr>
                <td>
                    <strong>{{ $log->created_at->format('Y-m-d') }}</strong>
                    <br><small>{{ $log->created_at->format('H:i:s') }}</small>
                </td>
                <td>
                    @if($log->user)
                        <strong>{{ $log->user->name }}</strong>
                        <br><small>{{ $log->user->email }}</small>
                    @else
                        <span class="text-gray-500">System</span>
                    @endif
                </td>
                <td>
                    <span class="badge 
                        @if(in_array(strtoupper($log->action), ['CREATE', 'CREATED'])) badge-success
                        @elseif(in_array(strtoupper($log->action), ['UPDATE', 'UPDATED'])) badge-info
                        @elseif(in_array(strtoupper($log->action), ['DELETE', 'DELETED'])) badge-danger
                        @else badge-warning
                        @endif">
                        {{ strtoupper($log->action) }}
                    </span>
                </td>
                <td>
                    <strong>{{ $log->model_type_name ?? class_basename($log->model_type ?? 'N/A') }}</strong>
                    @if($log->model_name)
                        <br><small>{{ $log->model_name }}</small>
                    @endif
                </td>
                <td style="max-width: 200px;">
                    {{ Str::limit($log->description, 100) }}
                </td>
                <td>
                    {{ $log->ip_address ?? 'N/A' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">
                    <p>No audit logs found for the selected filters.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($auditLogs->count() > 0)
<div class="meta-info" style="margin-top: 15px;">
    <div><strong>Report Summary:</strong></div>
    <div style="margin-top: 10px;">
        <div><strong>Total Records:</strong> {{ $auditLogs->count() }}</div>
        <div><strong>Date Range:</strong> 
            {{ $auditLogs->min('created_at')?->format('Y-m-d') ?? 'N/A' }} 
            to 
            {{ $auditLogs->max('created_at')?->format('Y-m-d') ?? 'N/A' }}
        </div>
        <div><strong>Actions Breakdown:</strong>
            @php
                $actionCounts = $auditLogs->groupBy('action')->map->count();
            @endphp
            @foreach($actionCounts as $action => $count)
                {{ ucfirst($action) }}: {{ $count }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
