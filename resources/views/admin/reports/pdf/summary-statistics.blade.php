@extends('admin.reports.pdf.layout')

@section('title', 'Summary Statistics Report')

@php
    $title = 'Summary Statistics Report';
    $subtitle = 'Comprehensive organizational overview';
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Units</div>
        <div class="value">{{ $stats['total_units'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Positions</div>
        <div class="value">{{ $stats['total_positions'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Employees</div>
        <div class="value">{{ $stats['total_employees'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Filled Positions</div>
        <div class="value">{{ $stats['filled_positions'] }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
    <div>
        <h3 style="font-size: 12px; margin-bottom: 10px; border-bottom: 2px solid #D4AF37; padding-bottom: 5px;">Units by Type</h3>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['units_by_type'] as $type => $count)
                    <tr>
                        <td>{{ $type }}</td>
                        <td style="text-align: center;">{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        <h3 style="font-size: 12px; margin-bottom: 10px; border-bottom: 2px solid #D4AF37; padding-bottom: 5px;">Positions by Title</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['positions_by_title'] as $title => $count)
                    <tr>
                        <td>{{ $title }}</td>
                        <td style="text-align: center;">{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div>
    <h3 style="font-size: 12px; margin-bottom: 10px; border-bottom: 2px solid #D4AF37; padding-bottom: 5px;">Employees by Designation</h3>
    <table>
        <thead>
            <tr>
                <th>Designation</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['employees_by_designation'] as $designation => $count)
                <tr>
                    <td>{{ $designation }}</td>
                    <td style="text-align: center;">{{ $count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
