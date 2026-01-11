@extends('admin.reports.pdf.layout')

@section('title', 'Organizational Structure Report')

@php
    $title = 'Organizational Structure Report';
    $subtitle = 'Complete organizational hierarchy';
@endphp

@section('content')
<div class="stat-card" style="margin-bottom: 15px;">
    <div class="label">Total Units</div>
    <div class="value">{{ $units->count() }}</div>
</div>

<div style="margin-bottom: 20px;">
    <h3 style="font-size: 12px; margin-bottom: 10px; border-bottom: 2px solid #D4AF37; padding-bottom: 5px;">Organizational Units</h3>
    
    @forelse($units as $unit)
        <div style="border-left: 4px solid #10b981; padding-left: 12px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #E5E7EB;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                <div style="flex: 1;">
                    <h4 style="font-size: 13px; font-weight: bold; color: #1F2937; margin-bottom: 4px;">{{ $unit->name }}</h4>
                    <div style="display: flex; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                        <span style="display: inline-block; padding: 2px 8px; background-color: #D1FAE5; color: #065F46; border-radius: 12px; font-size: 8px; font-weight: 600;">{{ $unit->unit_type }}</span>
                        @if($unit->parent)
                            <span style="font-size: 9px; color: #6B7280;">Under: {{ $unit->parent->name }}</span>
                        @endif
                    </div>
                    <div style="font-size: 9px; color: #6B7280;">
                        <span style="font-weight: 600;">{{ $unit->positions->count() }}</span> position(s)
                        @if($unit->positions->where('is_head', true)->count() > 0)
                            • <span style="font-weight: 600;">{{ $unit->positions->where('is_head', true)->count() }}</span> head position(s)
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 20px; color: #6B7280;">
            No organizational units found.
        </div>
    @endforelse
</div>

@if($units->count() > 0)
<div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #E5E7EB;">
    <h3 style="font-size: 11px; margin-bottom: 8px; font-weight: 600; color: #374151;">Summary by Unit Type</h3>
    <table style="width: 100%; font-size: 9px;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 6px; background-color: #F3F4F6;">Unit Type</th>
                <th style="text-align: center; padding: 6px; background-color: #F3F4F6;">Count</th>
            </tr>
        </thead>
        <tbody>
            @php
                $typeCounts = $units->groupBy('unit_type')->map->count();
            @endphp
            @foreach($typeCounts as $type => $count)
                <tr>
                    <td style="padding: 6px; border-bottom: 1px solid #E5E7EB;">{{ $type }}</td>
                    <td style="text-align: center; padding: 6px; border-bottom: 1px solid #E5E7EB; font-weight: 600;">{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
