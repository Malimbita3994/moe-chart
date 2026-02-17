@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="min-w-0 max-w-7xl mx-auto relative">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                    <p class="text-sm text-gray-600">Overview of your organization</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Last updated</p>
                <p class="text-sm font-semibold text-gray-700">{{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Units Card -->
        <div onclick="openModal('units')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-100 hover:border-blue-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center">
                    <div class="icon-container p-3 bg-blue-100 rounded-lg border border-blue-300 transition-all duration-300 hover:scale-110 hover:rotate-3">
                        <svg class="w-8 h-8 text-blue-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Total Units</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['total_units'] }}</p>
                        @if(isset($stats['units_by_type']) && !empty($stats['units_by_type']))
                            <p class="text-xs text-gray-500 mt-1">
                                @if(isset($stats['units_by_type']['DIVISION']))
                                    {{ $stats['units_by_type']['DIVISION'] }} Division{{ $stats['units_by_type']['DIVISION'] != 1 ? 's' : '' }}
                                @endif
                                @if(isset($stats['units_by_type']['SECTION']))
                                    • {{ $stats['units_by_type']['SECTION'] }} Section{{ $stats['units_by_type']['SECTION'] != 1 ? 's' : '' }}
                                @endif
                                @if(isset($stats['units_by_type']['UNIT']))
                                    • {{ $stats['units_by_type']['UNIT'] }} Unit{{ $stats['units_by_type']['UNIT'] != 1 ? 's' : '' }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Positions Card -->
        <div onclick="openModal('positions')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-200 hover:border-green-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center">
                    <div class="icon-container p-3 bg-green-100 rounded-lg border border-green-300 transition-all duration-300 hover:scale-110 hover:rotate-3">
                        <svg class="w-8 h-8 text-green-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Total Positions</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['total_positions'] }}</p>
                        @if(isset($stats['head_positions']))
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['head_positions'] }} Head Position{{ $stats['head_positions'] != 1 ? 's' : '' }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Employees Card -->
        <div onclick="openModal('employees')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-300 hover:border-purple-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center">
                    <div class="icon-container p-3 bg-purple-100 rounded-lg border border-purple-300 transition-all duration-300 hover:scale-110 hover:rotate-3">
                        <svg class="w-8 h-8 text-purple-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Total Employees</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['total_employees'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Active users</p>
                    </div>
                </div>
                <div class="text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Filled Positions Card -->
        <div onclick="openModal('filled-positions')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-400 hover:border-yellow-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center flex-1">
                    <div class="icon-container p-3 bg-yellow-100 rounded-lg border border-yellow-300 transition-all duration-300 hover:scale-110 hover:rotate-3 flex-shrink-0">
                        <svg class="w-8 h-8 text-yellow-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Filled Positions</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['filled_positions'] }}</p>
                        @if(isset($stats['positions_fill_rate']))
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['positions_fill_rate'] }}% fill rate</p>
                        @endif
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-4">
                    @if(isset($stats['positions_fill_rate']))
                        <div class="w-16 h-16 relative">
                            <svg class="transform -rotate-90 w-16 h-16">
                                <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-gray-200"></circle>
                                <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" 
                                    stroke-dasharray="{{ 2 * 3.14159 * 28 }}" 
                                    stroke-dashoffset="{{ 2 * 3.14159 * 28 * (1 - $stats['positions_fill_rate'] / 100) }}"
                                    class="text-yellow-600 transition-all duration-500"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-700">{{ $stats['positions_fill_rate'] }}%</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Vacant Positions Card -->
        <div onclick="openModal('vacant-positions')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-500 hover:border-red-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center">
                    <div class="icon-container p-3 bg-red-100 rounded-lg border border-red-300 transition-all duration-300 hover:scale-110 hover:rotate-3">
                        <svg class="w-8 h-8 text-red-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Vacant Positions</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['vacant_positions'] }}</p>
                        @if($stats['total_positions'] > 0)
                            @php
                                $vacantRate = round(($stats['vacant_positions'] / $stats['total_positions']) * 100, 1);
                            @endphp
                            <p class="text-xs text-gray-500 mt-1">{{ $vacantRate }}% of total positions</p>
                        @endif
                    </div>
                </div>
                <div class="text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Advisory Bodies Card -->
        <div onclick="openModal('advisory-bodies')" class="animated-card card-hover-enhanced bg-white rounded-xl shadow-lg p-6 border-2 border-gray-300 cursor-pointer animate-delay-600 hover:border-indigo-500 hover:shadow-xl transition-all duration-300 h-full flex flex-col min-h-[140px]">
            <div class="flex items-center justify-between flex-1">
                <div class="flex items-center">
                    <div class="icon-container p-3 bg-indigo-100 rounded-lg border border-indigo-300 transition-all duration-300 hover:scale-110 hover:rotate-3">
                        <svg class="w-8 h-8 text-indigo-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm transition-colors duration-300">Advisory Bodies</p>
                        <p class="text-3xl font-bold text-gray-800 transition-all duration-300">{{ $stats['advisory_bodies'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Registered bodies</p>
                    </div>
                </div>
                <div class="text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section (graphs focused) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Units by Type (Donut Chart) -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg border-2 border-gray-300 animate-delay-700">
            <div class="p-6 border-b border-gray-300 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Units by Type</h3>
                <span class="text-xs text-gray-500">DIVISION / SECTION / UNIT</span>
            </div>
            <div class="p-4 md:p-6">
                <div id="unitsByTypeChart" class="w-full h-64"></div>
            </div>
        </div>

        <!-- Position Status (Filled vs Vacant) -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg border-2 border-gray-300 animate-delay-800">
            <div class="p-6 border-b border-gray-300 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Position Status</h3>
                <span class="text-xs text-gray-500">Filled vs Vacant</span>
            </div>
            <div class="p-4 md:p-6">
                <div id="positionStatusChart" class="w-full h-64"></div>
            </div>
        </div>

        <!-- Organization Snapshot (Bar Chart) -->
        <div class="animated-card card-hover bg-white rounded-xl shadow-lg border-2 border-gray-300 animate-delay-900">
            <div class="p-6 border-b border-gray-300 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Organization Snapshot</h3>
                <span class="text-xs text-gray-500">Key counts</span>
            </div>
            <div class="p-4 md:p-6">
                <div id="orgSnapshotChart" class="w-full h-64"></div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section (removed cards per request) -->

    <!-- Dashboard Modal (scoped to content area only) -->
    <div id="dashboardModal"
         class="absolute inset-0 bg-gray-900/10 z-20 hidden flex items-start justify-center p-4"
         onclick="closeModalOnBackdrop(event)">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[85vh] overflow-hidden flex flex-col border-2 border-gray-300"
         onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="p-6 border-b-2 border-gray-300 bg-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 id="modalTitle" class="text-2xl font-bold text-gray-800">Loading...</h2>
                        <p id="modalSubtitle" class="text-sm text-gray-600 mt-1">Please wait</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Search Input -->
            <div class="relative">
                <input type="text" 
                       id="modalSearch" 
                       placeholder="Search..." 
                       onkeyup="handleModalSearch(event)"
                       class="w-full px-4 py-3 pl-10 pr-4 bg-gray-50 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 text-gray-800 transition-all duration-200">
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div id="modalContent" class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                <p class="mt-4 text-gray-600">Loading data...</p>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-6 border-t-2 border-gray-300 bg-white flex items-center justify-between">
            <div class="text-sm text-gray-600 font-medium">
                <span id="modalCount">0</span> items
            </div>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 font-medium border-2 border-gray-300">
                    Close
                </button>
                <a id="modalViewAllLink" href="#" class="px-6 py-2 rounded-lg hover:opacity-90 transition-all duration-200 font-medium border-2" style="background-color: #D4AF37; color: #1F2937; border-color: #C4A027;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                    View All
                </a>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    let currentModalType = null;

    function openModal(type) {
        currentModalType = type;
        const modal = document.getElementById('dashboardModal');
        const modalContent = document.getElementById('modalContent');
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const modalViewAllLink = document.getElementById('modalViewAllLink');
        
        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Set loading state
        modalContent.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                <p class="mt-4 text-gray-600">Loading data...</p>
            </div>
        `;
        
        // Set titles based on type
        const titles = {
            'units': { title: 'Organization Units', subtitle: 'All active units in the organization' },
            'positions': { title: 'All Positions', subtitle: 'All active positions' },
            'employees': { title: 'Employees', subtitle: 'All active employees' },
            'filled-positions': { title: 'Filled Positions', subtitle: 'Positions with active assignments' },
            'vacant-positions': { title: 'Vacant Positions', subtitle: 'Positions without active assignments' },
            'advisory-bodies': { title: 'Advisory Bodies', subtitle: 'All registered advisory bodies' }
        };
        
        const titleData = titles[type] || { title: 'Data', subtitle: 'Viewing data' };
        modalTitle.textContent = titleData.title;
        modalSubtitle.textContent = titleData.subtitle;
        
        // Set view all link
        const viewAllLinks = {
            'units': '{{ route("admin.organization-units.index") }}',
            'positions': '{{ route("admin.positions.index") }}',
            'employees': '{{ route("admin.users.index") }}',
            'filled-positions': '{{ route("admin.positions.index") }}?filter=filled',
            'vacant-positions': '{{ route("admin.positions.index") }}?filter=vacant',
            'advisory-bodies': '{{ route("admin.advisory-bodies.index") }}'
        };
        modalViewAllLink.href = viewAllLinks[type] || '#';
        
        // Clear search input
        document.getElementById('modalSearch').value = '';
        
        // Fetch data
        fetchModalData(type, '');
    }
    
    function handleModalSearch(event) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            event.preventDefault();
        }
        const search = event.target.value;
        const debounceTimeout = setTimeout(() => {
            fetchModalData(currentModalType, search);
        }, 300);
        
        // Clear previous timeout
        if (window.modalSearchTimeout) {
            clearTimeout(window.modalSearchTimeout);
        }
        window.modalSearchTimeout = debounceTimeout;
    }
    
    function fetchModalData(type, search) {
        const modalContent = document.getElementById('modalContent');
        
        // Show loading state
        modalContent.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                <p class="mt-4 text-gray-600">Loading data...</p>
            </div>
        `;
        
        // Build URL with search parameter
        let url = `/admin/dashboard/modal-data?type=${type}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }
        
        // Fetch data
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderModalContent(type, data);
        })
        .catch(error => {
            console.error('Error loading data:', error);
            modalContent.innerHTML = `
                <div class="text-center py-12">
                    <div class="text-red-500 text-4xl mb-4">⚠️</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Data</h3>
                    <p class="text-gray-600">Unable to load data. Please try again.</p>
                </div>
            `;
        });
    }
    
    function renderModalContent(type, data) {
        const modalContent = document.getElementById('modalContent');
        const modalCount = document.getElementById('modalCount');
        
        let html = '';
        let count = 0;
        
        switch(type) {
            case 'units':
                count = data.units ? data.units.length : 0;
                modalCount.textContent = count;
                if (data.units && data.units.length > 0) {
                    html = '<div class="space-y-3">';
                    data.units.forEach(unit => {
                        const badgeClass = unit.unit_type === 'DIVISION' ? 'bg-blue-100 text-blue-800' :
                                          unit.unit_type === 'SECTION' ? 'bg-purple-100 text-purple-800' :
                                          unit.unit_type === 'UNIT' ? 'bg-green-100 text-green-800' :
                                          'bg-gray-100 text-gray-800';
                        html += `
                            <div class="p-4 bg-white border-2 border-gray-300 rounded-lg hover:border-gray-400 hover:shadow-md transition-all duration-200 mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg mb-2">${unit.name}</h3>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full ${badgeClass}">${unit.unit_type}</span>
                                            ${unit.parent ? `<span class="text-xs text-gray-500">under ${unit.parent.name}</span>` : ''}
                                        </div>
                                    </div>
                                    <a href="/admin/organization-units/${unit.id}" class="text-gray-600 hover:text-gray-800 ml-4 p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-12"><p class="text-gray-500 text-lg">No units found</p></div>';
                }
                break;
                
            case 'positions':
            case 'filled-positions':
            case 'vacant-positions':
                count = data.positions ? data.positions.length : 0;
                modalCount.textContent = count;
                if (data.positions && data.positions.length > 0) {
                    html = '<div class="space-y-3">';
                    data.positions.forEach(position => {
                        const isFilled = position.active_assignments && position.active_assignments.length > 0;
                        html += `
                            <div class="p-4 bg-white border-2 border-gray-300 rounded-lg hover:border-gray-400 hover:shadow-md transition-all duration-200 mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg mb-2">
                                            ${position.name || (position.title ? position.title.name : 'N/A')}
                                            ${position.abbreviation ? `<span class="text-gray-500 font-normal">(${position.abbreviation})</span>` : ''}
                                        </h3>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm text-gray-600">${position.unit ? position.unit.name : 'N/A'}</span>
                                            ${position.is_head ? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Head</span>' : ''}
                                            ${isFilled ? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Filled</span>' : '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Vacant</span>'}
                                        </div>
                                        ${isFilled && position.active_assignments ? `
                                            <div class="mt-2 text-sm text-gray-600">
                                                Assigned to: ${position.active_assignments.map(a => a.user ? a.user.name : 'N/A').join(', ')}
                                            </div>
                                        ` : ''}
                                    </div>
                                    <a href="/admin/positions/${position.id}" class="text-gray-600 hover:text-gray-800 ml-4 p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-12"><p class="text-gray-500 text-lg">No positions found</p></div>';
                }
                break;
                
            case 'employees':
                count = data.employees ? data.employees.length : 0;
                modalCount.textContent = count;
                if (data.employees && data.employees.length > 0) {
                    html = '<div class="space-y-3">';
                    data.employees.forEach(employee => {
                        const displayName = employee.full_name || employee.name || 'N/A';
                        const initial = displayName !== 'N/A' ? displayName.charAt(0).toUpperCase() : '?';
                        html += `
                            <div class="p-4 bg-white border-2 border-gray-300 rounded-lg hover:border-gray-400 hover:shadow-md transition-all duration-200 mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center flex-shrink-0">
                                            <span class="text-gray-700 font-semibold text-lg">${initial}</span>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h3 class="font-semibold text-gray-800 text-lg mb-1">${displayName}</h3>
                                            <p class="text-sm text-gray-600 mb-1">${employee.email || 'N/A'}</p>
                                            ${employee.employee_number ? `<p class="text-xs text-gray-500">ID: ${employee.employee_number}</p>` : ''}
                                        </div>
                                    </div>
                                    <a href="/admin/users/${employee.id}" class="text-gray-600 hover:text-gray-800 ml-4 p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-12"><p class="text-gray-500 text-lg">No employees found</p></div>';
                }
                break;
                
            case 'advisory-bodies':
                count = data.advisory_bodies ? data.advisory_bodies.length : 0;
                modalCount.textContent = count;
                if (data.advisory_bodies && data.advisory_bodies.length > 0) {
                    html = '<div class="space-y-3">';
                    data.advisory_bodies.forEach(body => {
                        html += `
                            <div class="p-4 bg-white border-2 border-gray-300 rounded-lg hover:border-gray-400 hover:shadow-md transition-all duration-200 mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg mb-2">${body.name || 'N/A'}</h3>
                                        ${body.reports_to ? `
                                            <p class="text-sm text-gray-600">
                                                Reports to: ${body.reports_to.name || 'N/A'} 
                                                ${body.reports_to.unit ? `(${body.reports_to.unit.name})` : ''}
                                            </p>
                                        ` : ''}
                                    </div>
                                    <a href="/admin/advisory-bodies/${body.id}" class="text-gray-600 hover:text-gray-800 ml-4 p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-12"><p class="text-gray-500 text-lg">No advisory bodies found</p></div>';
                }
                break;
                
            default:
                html = '<div class="text-center py-12"><p class="text-gray-500 text-lg">Unknown data type</p></div>';
        }
        
        modalContent.innerHTML = html;
    }
    
    function closeModal() {
        const modal = document.getElementById('dashboardModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentModalType = null;
    }
    
    function closeModalOnBackdrop(event) {
        if (event.target.id === 'dashboardModal') {
            closeModal();
        }
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection

@push('scripts')
    {{-- ApexCharts from jsDelivr (allowed by existing CSP used for Alpine.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            // Units by Type Donut
            (function () {
                const data = @json($stats['units_by_type'] ?? []);
                const labels = Object.keys(data);
                const series = Object.values(data);

                const el = document.querySelector('#unitsByTypeChart');
                if (!el || !labels.length) return;

                const options = {
                    chart: {
                        type: 'donut',
                        height: 260,
                        toolbar: { show: false }
                    },
                    labels: labels,
                    series: series,
                    colors: ['#2563eb', '#7c3aed', '#22c55e'],
                    legend: {
                        position: 'bottom',
                        labels: { colors: '#4b5563' }
                    },
                    dataLabels: { enabled: true },
                    stroke: { width: 1 },
                };

                new ApexCharts(el, options).render();
            })();

            // Position Status Donut (Filled vs Vacant)
            (function () {
                const filled = {{ (int) $stats['filled_positions'] }};
                const vacant = {{ (int) $stats['vacant_positions'] }};
                const total = filled + vacant;
                const el = document.querySelector('#positionStatusChart');
                if (!el || !total) return;

                const options = {
                    chart: {
                        type: 'donut',
                        height: 260,
                        toolbar: { show: false }
                    },
                    labels: ['Filled', 'Vacant'],
                    series: [filled, vacant],
                    colors: ['#22c55e', '#ef4444'],
                    legend: {
                        position: 'bottom',
                        labels: { colors: '#4b5563' }
                    },
                    dataLabels: { enabled: true },
                    stroke: { width: 1 },
                };

                new ApexCharts(el, options).render();
            })();

            // Organization Snapshot Bar Chart
            (function () {
                const categories = ['Units', 'Positions', 'Employees', 'Advisory Bodies'];
                const seriesData = [
                    {{ (int) $stats['total_units'] }},
                    {{ (int) $stats['total_positions'] }},
                    {{ (int) $stats['total_employees'] }},
                    {{ (int) $stats['advisory_bodies'] }},
                ];

                const el = document.querySelector('#orgSnapshotChart');
                if (!el) return;

                const options = {
                    chart: {
                        type: 'bar',
                        height: 260,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Count',
                        data: seriesData
                    }],
                    xaxis: {
                        categories: categories,
                        labels: { style: { colors: '#6b7280' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#6b7280' } }
                    },
                    colors: ['#0ea5e9'],
                    plotOptions: {
                        bar: {
                            columnWidth: '40%',
                            borderRadius: 6
                        }
                    },
                    dataLabels: { enabled: false },
                    grid: {
                        borderColor: '#e5e7eb'
                    }
                };

                new ApexCharts(el, options).render();
            })();
        });
    </script>
@endpush
