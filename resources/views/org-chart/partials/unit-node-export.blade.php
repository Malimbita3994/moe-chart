@php
    $hasChildren = $unit->children && $unit->children->where('status', 'ACTIVE')->isNotEmpty();
    $activeChildren = $unit->children ? $unit->children->where('status', 'ACTIVE') : collect();
    $headPosition = $unit->positions ? $unit->positions->where('is_head', true)->where('status', 'ACTIVE')->first() : null;
    $headAssignment = $headPosition && $headPosition->activeAssignments ? $headPosition->activeAssignments->where('status', 'Active')->first() : null;
    $headUser = $headAssignment && $headAssignment->user ? $headAssignment->user : null;
    $levelClass = 'level-' . min($level, 5);
@endphp

<div class="unit-wrapper flex flex-col items-center mb-6 md:mb-8">
    <!-- Unit Card -->
    <div class="org-node bg-white rounded-xl shadow-lg p-4 md:p-6 mb-4 min-w-[260px] md:min-w-[280px] max-w-[300px] md:max-w-[320px] border-2 border-gray-200">
        <!-- Unit Header -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $levelClass }} text-white">
                    {{ strtoupper($unit->unit_type) }}
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $unit->name }}</h3>
            @if($unit->code)
                <p class="text-sm text-gray-500">Code: {{ $unit->code }}</p>
            @endif
        </div>

        <!-- Head of Unit -->
        @if($headUser)
            <div class="border-t pt-4 mt-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($headUser->full_name ?? $headUser->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $headUser->full_name ?? $headUser->name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $headPosition->title }}
                        </p>
                        @if($headUser->email)
                            <p class="text-xs text-gray-400">{{ $headUser->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Additional Positions Count -->
        @php
            $otherPositions = $unit->positions ? $unit->positions->where('status', 'ACTIVE')->where('is_head', false)->count() : 0;
        @endphp
        @if($otherPositions > 0)
            <div class="border-t pt-3 mt-3">
                <p class="text-xs text-gray-500">
                    + {{ $otherPositions }} other position{{ $otherPositions > 1 ? 's' : '' }}
                </p>
            </div>
        @endif
    </div>

    <!-- Children Units (Always Expanded in Export) -->
    @if($hasChildren)
        <div class="children-container flex flex-wrap justify-center gap-4 md:gap-6 mt-4 relative w-full">
            @if($activeChildren->count() > 1)
                <!-- Horizontal connector line above children -->
                <div class="absolute top-0 left-1/2 right-1/2 h-0.5 bg-gray-300 transform -translate-x-1/2" style="top: -24px; width: calc(100% - 2rem);"></div>
            @endif
            @foreach($activeChildren as $index => $child)
                <div class="flex flex-col items-center relative">
                    <!-- Vertical connector line -->
                    <div class="w-0.5 h-6 bg-gray-300 mb-2"></div>
                    @include('org-chart.partials.unit-node-export', ['unit' => $child, 'level' => $level + 1, 'allUnits' => $allUnits])
                </div>
            @endforeach
        </div>
    @endif
</div>
