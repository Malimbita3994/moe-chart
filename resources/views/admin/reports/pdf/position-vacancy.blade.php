@extends('admin.reports.pdf.layout')

@section('title', 'Position Vacancy Report')

@php
    $title = 'Position Vacancy Report';
    $subtitle = 'All vacant positions in the organization';
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Vacant</div>
        <div class="value">{{ $vacantPositions->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Head Positions</div>
        <div class="value">{{ $vacantPositions->where('is_head', true)->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Staff Positions</div>
        <div class="value">{{ $vacantPositions->where('is_head', false)->count() }}</div>
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
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vacantPositions as $position)
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
                    @if($position->is_head)
                        <span class="badge badge-warning">Head</span>
                    @else
                        <span class="badge badge-info">Staff</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">No vacant positions found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
