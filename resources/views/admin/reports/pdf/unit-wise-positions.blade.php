@extends('admin.reports.pdf.layout')

@section('title', 'Unit-wise Positions Report')

@php
    $title = 'Unit-wise Positions Report';
    $subtitle = 'Positions organized by organizational units';
    $totalPositions = $units->sum(fn($unit) => $unit->positions->count());
    $totalUnits = $units->count();
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Units</div>
        <div class="value">{{ $totalUnits }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Positions</div>
        <div class="value">{{ $totalPositions }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Filled Positions</div>
        <div class="value">{{ $units->sum(fn($unit) => $unit->positions->filter(fn($p) => $p->activeAssignments->isNotEmpty())->count()) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Report Date</div>
        <div class="value" style="font-size: 12px;">{{ now()->format('M d, Y') }}</div>
    </div>
</div>

@forelse($units as $unit)
    <div style="margin-bottom: 25px; page-break-inside: avoid;">
        <div style="background-color: #374151; color: white; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
            <h3 style="font-size: 14px; font-weight: bold; margin: 0;">
                {{ $unit->name }}
            </h3>
            <div style="font-size: 9px; margin-top: 5px; opacity: 0.9;">
                <span style="background-color: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; margin-right: 8px;">
                    {{ $unit->unit_type }}
                </span>
                @if($unit->parent)
                    <span>Under: {{ $unit->parent->name }}</span>
                @endif
                <span style="margin-left: 8px;">{{ $unit->positions->count() }} position(s)</span>
            </div>
        </div>

        @if($unit->positions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unit->positions as $position)
                        <tr>
                            <td>
                                <strong>{{ $position->name }}</strong>
                                @if($position->abbreviation)
                                    <br><small>({{ $position->abbreviation }})</small>
                                @endif
                            </td>
                            <td>{{ $position->title->name ?? 'N/A' }}</td>
                            <td>
                                @if($position->is_head)
                                    <span class="badge badge-warning">Head</span>
                                @else
                                    <span class="badge badge-info">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($position->activeAssignments->count() > 0)
                                    <span class="badge badge-success">Filled</span>
                                @else
                                    <span class="badge badge-danger">Vacant</span>
                                @endif
                            </td>
                            <td>
                                @if($position->activeAssignments->count() > 0)
                                    {{ $position->activeAssignments->first()->user->full_name ?? 'N/A' }}
                                @else
                                    <span style="color: #9CA3AF;">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 15px; text-align: center; color: #6B7280; font-style: italic;">
                No positions assigned to this unit.
            </div>
        @endif
    </div>
@empty
    <div style="text-align: center; padding: 40px; color: #6B7280;">
        <p>No organizational units found.</p>
    </div>
@endforelse
@endsection
