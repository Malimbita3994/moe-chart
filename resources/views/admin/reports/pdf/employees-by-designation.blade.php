@extends('admin.reports.pdf.layout')

@section('title', 'Employees by Designation Report')

@php
    $title = 'Employees by Designation Report';
    $subtitle = 'Employee distribution by designation/grades';
    $totalEmployees = $users->count();
    $totalDesignations = $byDesignation->count();
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Employees</div>
        <div class="value">{{ $totalEmployees }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Designations Covered</div>
        <div class="value">{{ $totalDesignations }}</div>
    </div>
    <div class="stat-card">
        <div class="label">With Position</div>
        <div class="value">
            {{ $users->filter(fn($u) => $u->activePositionAssignments->isNotEmpty())->count() }}
        </div>
    </div>
    <div class="stat-card">
        <div class="label">Without Position</div>
        <div class="value">
            {{ $users->filter(fn($u) => $u->activePositionAssignments->isEmpty())->count() }}
        </div>
    </div>
</div>

@if($byDesignation->isNotEmpty())
    <h3 class="section-title" style="margin-top: 25px;">Summary by Designation</h3>
    <table>
        <thead>
            <tr>
                <th>Designation</th>
                <th style="width: 80px;">Total</th>
                <th style="width: 90px;">With Position</th>
                <th style="width: 110px;">Without Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byDesignation as $designation => $stats)
                <tr>
                    <td><strong>{{ $designation }}</strong></td>
                    <td>{{ $stats['total'] }}</td>
                    <td>{{ $stats['with_position'] }}</td>
                    <td>{{ $stats['without_position'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<h3 class="section-title" style="margin-top: 25px;">Employee Details</h3>
@if($users->isEmpty())
    <div style="text-align: center; padding: 30px; color: #6B7280;">
        <p>No employees found for the selected filters.</p>
    </div>
@else
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Designation</th>
                <th>Position</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->full_name }}</strong><br>
                        <small>{{ $user->email }}</small>
                    </td>
                    <td>{{ $user->designation->name ?? 'N/A' }}</td>
                    <td>
                        @if($user->activePositionAssignments->isNotEmpty())
                            {{ $user->activePositionAssignments->first()->position->name ?? 'N/A' }}
                        @else
                            <span style="color: #9CA3AF;">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        @if($user->activePositionAssignments->isNotEmpty() && $user->activePositionAssignments->first()->position->unit)
                            {{ $user->activePositionAssignments->first()->position->unit->name }}
                        @else
                            <span style="color: #9CA3AF;">N/A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection

