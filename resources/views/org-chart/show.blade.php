<!-- Modal Header -->
<div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $unit->name }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                {{ strtoupper($unit->unit_type) }}
            </span>
        </p>
    </div>
    <button onclick="closeUnitDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<!-- Modal Content -->
<div class="p-6">
    <!-- Hierarchy Path -->
    @if(count($hierarchy) > 1)
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <p class="text-xs font-semibold text-gray-500 mb-2">ORGANIZATIONAL HIERARCHY</p>
        <div class="flex items-center flex-wrap gap-2">
            @foreach($hierarchy as $index => $hierUnit)
                <span class="text-sm text-gray-700">{{ $hierUnit->name }}</span>
                @if($index < count($hierarchy) - 1)
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Unit Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Unit Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500">Name</p>
                    <p class="text-sm text-gray-800">{{ $unit->name }}</p>
                </div>
                @if($unit->code)
                <div>
                    <p class="text-xs font-semibold text-gray-500">Code</p>
                    <p class="text-sm text-gray-800">{{ $unit->code }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs font-semibold text-gray-500">Type</p>
                    <p class="text-sm text-gray-800">{{ strtoupper($unit->unit_type) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Level</p>
                    <p class="text-sm text-gray-800">Level {{ $unit->level }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Status</p>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $unit->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $unit->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Parent Unit -->
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Parent Unit</h3>
            @if($unit->parent)
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-gray-800">{{ $unit->parent->name }}</p>
                    <p class="text-xs text-gray-500">{{ strtoupper($unit->parent->unit_type) }}</p>
                    <button onclick="showUnitDetails({{ $unit->parent->id }})" class="text-xs text-blue-600 hover:text-blue-800 mt-2">
                        View Parent Details →
                    </button>
                </div>
            @else
                <p class="text-sm text-gray-400">This is a root unit (no parent)</p>
            @endif
        </div>
    </div>

    <!-- Child Units -->
    @if($unit->children->isNotEmpty())
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Sub-Units ({{ $unit->children->count() }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($unit->children as $child)
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="showUnitDetails({{ $child->id }})">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $child->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ strtoupper($child->unit_type) }}</p>
                        @if($child->code)
                            <p class="text-xs text-gray-400 mt-1">Code: {{ $child->code }}</p>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Positions -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Positions ({{ $unit->positions->count() }})</h3>
        @if($unit->positions->isNotEmpty())
            <div class="space-y-4">
                @foreach($unit->positions as $position)
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="text-base font-semibold text-gray-800">{{ $position->name ?? $position->title }}</h4>
                                @if($position->is_head)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">HEAD</span>
                                @endif
                                @if($position->grade)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ $position->grade }}</span>
                                @endif
                            </div>
                            @if($position->reportsTo)
                                <p class="text-xs text-gray-500">Reports to: {{ $position->reportsTo->name ?? 'N/A' }}@if($position->reportsTo->unit) ({{ $position->reportsTo->unit->name }})@endif</p>
                            @endif
                        </div>
                    </div>

                    <!-- Assigned Employee -->
                    @php
                        $activeAssignment = $position->activeAssignments->where('status', 'Active')->first();
                        $assignedUser = $activeAssignment ? $activeAssignment->user : null;
                    @endphp
                    @if($assignedUser)
                        <div class="border-t pt-3 mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">ASSIGNED EMPLOYEE</p>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($assignedUser->full_name ?? $assignedUser->name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">{{ $assignedUser->full_name ?? $assignedUser->name }}</p>
                                    @if($assignedUser->email)
                                        <p class="text-xs text-gray-500">{{ $assignedUser->email }}</p>
                                    @endif
                                    @if($assignedUser->phone)
                                        <p class="text-xs text-gray-500">{{ $assignedUser->phone }}</p>
                                    @endif
                                    @if($assignedUser->employee_number)
                                        <p class="text-xs text-gray-400">Emp. #: {{ $assignedUser->employee_number }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($activeAssignment->start_date)
                                <p class="text-xs text-gray-400 mt-2">
                                    Assigned since: {{ \Carbon\Carbon::parse($activeAssignment->start_date)->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="border-t pt-3 mt-3">
                            <p class="text-xs text-gray-400 italic">Position is currently vacant</p>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <p class="text-gray-500">No positions defined for this unit</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Footer -->
<div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
    <button onclick="closeUnitDetailsModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
        Close
    </button>
</div>
