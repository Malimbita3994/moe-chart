@php
    $hasChildren = $unit->children && $unit->children->isNotEmpty();
    $headPosition = $unit->positions ? $unit->positions->where('is_head', true)->where('status', 'ACTIVE')->first() : null;
    $headAssignment = $headPosition && $headPosition->activeAssignments ? $headPosition->activeAssignments->where('status', 'Active')->first() : null;
    $headUser = $headAssignment && $headAssignment->user ? $headAssignment->user : null;
    $levelClass = 'level-' . min($level, 5);
@endphp

<div class="unit-wrapper flex flex-col items-center mb-6 md:mb-8">
    <!-- Unit Card -->
    <div class="org-node bg-white rounded-xl shadow-lg p-4 md:p-6 mb-4 min-w-[260px] md:min-w-[280px] max-w-[300px] md:max-w-[320px] border-2 border-gray-200 hover:border-blue-400">
        <!-- Unit Header -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $levelClass }} text-white">
                    {{ strtoupper($unit->unit_type) }}
                </span>
                @if($hasChildren)
                    <button class="toggle-children p-1 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="toggle-icon w-5 h-5 text-gray-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                @endif
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
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $headUser->full_name ?? $headUser->name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            @if($headPosition)
                                {{ $headPosition->name ?? ($headPosition->title && is_object($headPosition->title) ? $headPosition->title->name : 'Position') }}
                            @else
                                Position
                            @endif
                        </p>
                        @if($headUser->email)
                            <p class="text-xs text-gray-400 truncate">{{ $headUser->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($headPosition)
            <div class="border-t pt-4 mt-4">
                <div class="text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ $headPosition->name ?? ($headPosition->title && is_object($headPosition->title) ? $headPosition->title->name : 'Position') }}
                    </p>
                    <p class="text-xs text-gray-400">Position Vacant</p>
                </div>
            </div>
        @endif

        <!-- Other Positions -->
        @php
            $otherPositions = $unit->positions->where('is_head', false)->where('status', 'ACTIVE')->take(2);
        @endphp
        @if($otherPositions->isNotEmpty())
            <div class="border-t pt-3 mt-3">
                <p class="text-xs font-semibold text-gray-500 mb-2">Other Positions:</p>
                @foreach($otherPositions as $position)
                    @php
                        // Safely get position name/title
                        $positionName = 'Position';
                        if (is_object($position) && isset($position->id)) {
                            // Position has a 'name' property, and 'title' is a relationship
                            if (!empty($position->name)) {
                                $positionName = $position->name;
                            } elseif ($position->title && is_object($position->title) && isset($position->title->name)) {
                                // If title relationship is loaded, use it
                                $positionName = $position->title->name;
                            } else {
                                $positionName = 'Position #' . $position->id;
                            }
                        }
                        
                        // Check if position is filled
                        $isFilled = false;
                        if (is_object($position) && isset($position->activeAssignments)) {
                            try {
                                $isFilled = $position->activeAssignments->where('status', 'Active')->isNotEmpty();
                            } catch (\Exception $e) {
                                $isFilled = false;
                            }
                        }
                    @endphp
                    <div class="text-xs text-gray-600 mb-1">
                        • {{ $positionName }}
                        @if($isFilled)
                            <span class="text-green-600">(Filled)</span>
                        @else
                            <span class="text-gray-400">(Vacant)</span>
                        @endif
                    </div>
                @endforeach
                @if($unit->positions->where('is_head', false)->where('status', 'ACTIVE')->count() > 2)
                    <p class="text-xs text-gray-400 mt-1">
                        +{{ $unit->positions->where('is_head', false)->where('status', 'ACTIVE')->count() - 2 }} more
                    </p>
                @endif
            </div>
        @endif

        <!-- View Details Button -->
        <div class="border-t pt-3 mt-3">
            <button onclick="showUnitDetails({{ $unit->id }})" 
                    class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                View Details
            </button>
        </div>
    </div>

    <!-- Children Units -->
    @if($hasChildren)
        @php
            $activeChildren = $unit->children->where('status', 'ACTIVE');
            $childrenCount = $activeChildren->count();
        @endphp
        <div class="children-container {{ $level === 1 ? 'root-children' : 'hidden' }} flex flex-wrap justify-center gap-4 md:gap-6 mt-4 relative w-full">
            @if($childrenCount > 1)
                <!-- Horizontal connector line above children -->
                <div class="absolute top-0 left-1/2 right-1/2 h-0.5 bg-gray-300 transform -translate-x-1/2" style="top: -24px; width: calc(100% - 2rem);"></div>
            @endif
            @foreach($activeChildren as $index => $child)
                <div class="flex flex-col items-center relative">
                    <!-- Vertical connector line -->
                    <div class="w-0.5 h-6 bg-gray-300 mb-2"></div>
                    @include('org-chart.partials.unit-node', ['unit' => $child, 'level' => $level + 1, 'allUnits' => $allUnits])
                </div>
            @endforeach
        </div>
    @endif
</div>
