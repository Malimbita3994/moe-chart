@extends('admin.reports.pdf.layout')

@section('title', 'Assignment History Report')

@php
    $title = 'Assignment History Report';
    $subtitle = 'Employee position assignment history';
@endphp

@section('content')
<div class="stat-card" style="margin-bottom: 15px;">
    <div class="label">Total Assignments</div>
    <div class="value">{{ $assignments->count() }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Employee</th>
            <th>Position</th>
            <th>Unit</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($assignments as $assignment)
            <tr>
                <td>
                    <strong>{{ $assignment->user->full_name ?? 'N/A' }}</strong>
                    <br><small>{{ $assignment->user->email ?? '' }}</small>
                </td>
                <td>
                    {{ $assignment->position->name ?? 'N/A' }}
                    <br><small>{{ $assignment->position->title->name ?? '' }}</small>
                </td>
                <td>{{ $assignment->position->unit->name ?? 'N/A' }}</td>
                <td>{{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') : 'Ongoing' }}</td>
                <td>
                    @if($assignment->status === 'Active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Ended</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">No assignment history found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
