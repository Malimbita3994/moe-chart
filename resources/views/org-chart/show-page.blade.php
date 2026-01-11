<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $unit->name }} - {{ config('app.name', 'MOE') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {}
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Bar (White Background) -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center py-3 gap-4">
                <!-- Search Bar on Left -->
                <div class="flex-1 max-w-md w-full">
                    <div class="relative">
                        <input type="text" placeholder="Tafuta" class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Menu Items on Right -->
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-700">🇹🇿</span>
                        <span class="text-gray-700">Kiswahili</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-700">🇬🇧</span>
                        <span class="text-gray-700">English</span>
                    </div>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Banner (Tanzanian Flag Background) -->
    <div class="tanzania-flag relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <!-- Logo/Coat of Arms on Left -->
                <div class="flex-shrink-0">
                    <a href="{{ route('org-chart.index') }}" class="inline-block cursor-pointer hover:opacity-90 transition-opacity">
                        <img src="{{ asset('image/logo.png') }}" alt="Ministry Logo" class="h-20 md:h-24 w-auto object-contain drop-shadow-lg">
                    </a>
                </div>
                
                <!-- Ministry Name in Center -->
                <div class="text-center text-white max-w-md mx-auto">
                    <h2 class="text-xl md:text-2xl font-semibold mb-2 uppercase tracking-wide whitespace-nowrap">
                        The United Republic of Tanzania
                    </h2>
                    <h1 class="text-lg md:text-xl font-bold mb-2 whitespace-nowrap">
                        Ministry of Education, Science and Technology
                    </h1>
                </div>
                
                <!-- Background Image on Right (Optional decorative element) -->
                <div class="hidden lg:block flex-shrink-0 opacity-20">
                    <div class="w-32 h-32 bg-white rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Bar (Dark Blue) -->
    <div class="bg-blue-900 shadow-lg">
        <div class="container mx-auto px-4">
            <nav class="flex flex-wrap items-center gap-4 md:gap-6 py-3">
                <a href="{{ route('org-chart.index') }}" class="flex items-center gap-2 text-white hover:text-yellow-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Mwanzo</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $unit->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                            {{ strtoupper($unit->unit_type) }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('org-chart.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>

            <!-- Page Content -->
            <div class="p-6">
                <!-- Hierarchy Path -->
                @if(count($hierarchy) > 1)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-500 mb-2">ORGANIZATIONAL HIERARCHY</p>
                    <div class="flex items-center flex-wrap gap-2">
                        @foreach($hierarchy as $index => $hierUnit)
                            <a href="{{ route('org-chart.unit.show', $hierUnit->id) }}" class="text-sm text-gray-700 hover:text-blue-600 transition-colors">
                                {{ $hierUnit->name }}
                            </a>
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
                                <a href="{{ route('org-chart.unit.show', $unit->parent->id) }}" class="inline-block text-xs text-blue-600 hover:text-blue-800 mt-2">
                                    View Parent Details →
                                </a>
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
                        <a href="{{ route('org-chart.unit.show', $child->id) }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer block">
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
                        </a>
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

            <!-- Page Footer -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
                <a href="{{ route('org-chart.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Chart
                </a>
            </div>
        </div>
    </div>

    <style>
        .tanzania-flag {
            background: 
                linear-gradient(135deg, 
                    #1EB53A 0%, 
                    #1EB53A 20%, 
                    rgba(252, 209, 22, 0.4) 28%, 
                    rgba(0, 0, 0, 0.9) 32%, 
                    rgba(0, 0, 0, 0.85) 38%, 
                    rgba(252, 209, 22, 0.3) 42%, 
                    #00A3DD 48%, 
                    #00A3DD 100%);
            background-size: 200% 200%;
            background-position: center;
            position: relative;
        }
        .tanzania-flag::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(ellipse at 30% 30%, rgba(30, 181, 58, 0.4) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 70%, rgba(0, 163, 221, 0.4) 0%, transparent 50%),
                linear-gradient(135deg, 
                    transparent 0%, 
                    rgba(0, 0, 0, 0.3) 35%, 
                    rgba(252, 209, 22, 0.2) 40%, 
                    transparent 100%);
            z-index: 0;
        }
        .tanzania-flag > * {
            position: relative;
            z-index: 1;
        }
    </style>
</body>
</html>
