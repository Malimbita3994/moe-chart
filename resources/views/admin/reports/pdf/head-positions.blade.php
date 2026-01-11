@extends('admin.reports.pdf.layout')

@section('title', 'Head Positions Report')

@php
    $title = 'Head Positions Report';
    $subtitle = 'All head positions across the organization';
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Head Positions</div>
        <div class="value">{{ $headPositions->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Filled</div>
        <div class="value">{{ $headPositions->filter(fn($p) => $p->activeAssignments->isNotEmpty())->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Vacant</div>
        <div class="value">{{ $headPositions->filter(fn($p) => $p->activeAssignments->isEmpty())->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Report Date</div>
        <div class="value" style="font-size: 12px;">{{ now()->format('M d, Y') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Position</th>
            <th>Unit</th>
            <th>Title</th>
            <th>Designation</th>
            <th>Status</th>
            <th>Assigned To</th>
        </tr>
    </thead>
    <tbody>
        @forelse($headPositions as $position)
            <tr>
                <td>
                    <strong>{{ $position->name }}</strong>
                    @if($position->abbreviation)
                        <br><small>({{ $position->abbreviation }})</small>
                    @endif
                </td>
                <td>
                    {{ $position->unit->name ?? 'N/A' }}
                    <br><small>{{ $position->unit->unit_type ?? '' }}</small>
                </td>
                <td>{{ $position->title->name ?? 'N/A' }}</td>
                <td>{{ $position->designation->name ?? 'N/A' }}</td>
                <td>
                    @if($position->activeAssignments->isNotEmpty())
                        <span class="badge badge-success">Filled</span>
                    @else
                        <span class="badge badge-danger">Vacant</span>
                    @endif
                </td>
                <td>
                    @if($position->activeAssignments->isNotEmpty())
                        {{ $position->activeAssignments->first()->user->full_name ?? 'N/A' }}
                    @else
                        <span style="color: #9CA3AF;">Not assigned</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">No head positions found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
