<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organizational Chart - {{ config('app.name', 'MOE') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN (temporary until Vite is configured) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {}
            }
        }
    </script>
    
    <!-- OrgChart.js Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/js/jquery.orgchart.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .org-node {
            transition: all 0.3s ease;
        }
        .org-node:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .connector-line {
            stroke: #cbd5e1;
            stroke-width: 2;
        }
        .level-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .level-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .level-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .level-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .level-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        
        /* OrgChart.js Custom Styles */
        .orgchart {
            background: transparent;
        }
        .orgchart .node {
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .orgchart .node:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }
        .orgchart .node .title {
            font-weight: 600;
        }
        .orgchart .verticalNodes {
            border-top: 2px solid #cbd5e1;
        }
        .orgchart .horizontalNodes {
            border-left: 2px solid #cbd5e1;
        }
        .orgchart .lines .downLine {
            background-color: #cbd5e1;
        }
        .orgchart .lines .rightLine,
        .orgchart .lines .leftLine,
        .orgchart .lines .topLine {
            border-color: #cbd5e1;
        }
        
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
        
        /* Print Styles */
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Hide ALL top-level page elements */
            body > div.bg-white.border-b,
            body > div.tanzania-flag {
                display: none !important;
            }
            
            /* Show main container but hide title section */
            body > div.container.mx-auto.px-4.py-8 {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            
            /* Hide title section (first div in main container) */
            body > div.container.mx-auto.px-4.py-8 > div:first-child {
                display: none !important;
            }
            
            /* Show ONLY the org chart container */
            .bg-white.rounded-2xl.shadow-2xl {
                display: block !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 20px !important;
                margin: 0 !important;
                background: white !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Hide all buttons, navigation, and other page elements */
            .no-print,
            button,
            nav,
            .tanzania-flag,
            .bg-white.border-b,
            .flex.flex-wrap.justify-center,
            #toggleViewBtn,
            #exportModal,
            #unitDetailsModal,
            header {
                display: none !important;
            }
            
            /* Show print header */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 3px solid #1f2937;
            }
            
            /* Hide legend in print */
            .mb-6.p-4.bg-gray-50,
            .no-print {
                display: none !important;
            }
            
            /* Ensure org chart is visible and expanded */
            #orgchart-container {
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                page-break-inside: avoid;
                display: block !important;
            }
            
            #orgchart-container .orgchart {
                width: 100% !important;
                height: auto !important;
                transform: scale(1) !important;
                transform-origin: top left !important;
                display: block !important;
            }
            
            /* Expand all nodes for printing */
            #orgchart-container .orgchart .node {
                page-break-inside: avoid;
                break-inside: avoid;
                margin: 8px !important;
            }
            
            /* Ensure all children are visible */
            #orgchart-container .orgchart .nodes {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Hide toggle buttons */
            #orgchart-container .orgchart .toggleBtn {
                display: none !important;
            }
            
            /* Make text readable */
            #orgchart-container .orgchart .node * {
                color: inherit !important;
            }
            
            /* CRITICAL: Force all colors to print - especially gradients */
            #orgchart-container .orgchart .node,
            #orgchart-container .orgchart .node *,
            div[style*="background"],
            div[style*="gradient"],
            [style*="linear-gradient"],
            [style*="background:"] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Ensure unit type badge colors print */
            .level-1, .level-2, .level-3, .level-4, .level-5,
            [class*="level-"] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Force all jQuery-created inline styles to print with colors */
            .orgchart .node[style] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Ensure unit type badge colors print */
            .level-1, .level-2, .level-3, .level-4, .level-5,
            [class*="level-"] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Print instruction note */
            body::before {
                content: "Make sure 'Background graphics' is enabled in your print settings!";
                display: block;
                background: #fff3cd;
                color: #856404;
                padding: 10px;
                margin-bottom: 10px;
                border: 2px solid #ffc107;
                border-radius: 5px;
                font-weight: bold;
                text-align: center;
            }
            
            /* Hide traditional view if visible */
            #traditional-view {
                display: none !important;
            }
        }
        
        .print-header {
            display: none;
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
                
                @if($directorates->count() > 0)
                    @foreach($directorates as $directorate)
                        <a href="{{ route('org-chart.unit.show', $directorate->id) }}" class="text-white hover:text-yellow-300 transition-colors">
                            {{ $directorate->name }}
                        </a>
                    @endforeach
                @else
                    <a href="#" class="text-white hover:text-yellow-300 transition-colors">Directorates</a>
                @endif
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Filter Panel (Role-aware: Admin and Viewer can filter) -->
        @if(isset($isAdmin) && $isAdmin || isset($isViewer) && $isViewer || !auth()->check())
        <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="filterPanel">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </h3>
                <button id="toggleFilters" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <span>Show Filters</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            
            <div id="filterContent" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Unit Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <select id="filterUnit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Units</option>
                        @foreach($allUnitsForFilter ?? [] as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->unit_type }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Unit Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Type</label>
                    <select id="filterUnitType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="MINISTRY">Ministry</option>
                        <option value="DIRECTORATE">Directorate</option>
                        <option value="DIVISION">Division</option>
                        <option value="UNIT">Unit</option>
                        <option value="SECTION">Section</option>
                    </select>
                </div>
                
                <!-- Assignment Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Type</label>
                    <select id="filterAssignmentType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="SUBSTANTIVE">Substantive</option>
                        <option value="ACTING">Acting</option>
                        <option value="TEMPORARY">Temporary</option>
                        <option value="SECONDMENT">Secondment</option>
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" id="filterDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" id="filterDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Show Vacant -->
                <div class="flex items-center">
                    <input type="checkbox" id="filterShowVacant" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="filterShowVacant" class="ml-2 text-sm font-medium text-gray-700">Show Vacant Positions</label>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2 items-end">
                    <button id="applyFilters" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                    <button id="clearFilters" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Clear
                    </button>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Title Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Organizational Chart
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-6">
                Ministry Structure
            </p>
            <div class="flex flex-wrap justify-center gap-4 no-print">
                <button onclick="expandAll()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    Expand All
                </button>
                <button onclick="collapseAll()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-md">
                    Collapse All
                </button>
                @if(isset($isAdmin) && $isAdmin)
                <button onclick="refreshChart()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md" title="Refresh data from database">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
                @endif
                <button onclick="printOrgChart()" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-colors shadow-md font-semibold no-print" title="Print organizational chart with colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Chart
                </button>
                @if(isset($isAdmin) && $isAdmin)
                <button onclick="showExportModal()" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg hover:from-red-700 hover:to-orange-700 transition-colors shadow-md font-semibold no-print">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Chart
                </button>
                @endif
                @if(!$rootUnits->isEmpty())
                <button id="toggleViewBtn" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-md font-semibold no-print">
                    Switch to Traditional View
                </button>
                @endif
            </div>
        </div>

        <!-- Org Chart Container -->
        <div class="bg-white rounded-2xl shadow-2xl p-4 md:p-8 overflow-hidden">
            <!-- Print Header (hidden on screen, visible in print) -->
            <div class="print-header">
                <h1 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 8px;">THE UNITED REPUBLIC OF TANZANIA</h1>
                <h2 style="font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px;">MINISTRY OF EDUCATION, SCIENCE AND TECHNOLOGY</h2>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 5px;">Organizational Chart</p>
                <p style="font-size: 12px; color: #9ca3af;">Printed on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
            </div>
            
            <!-- Legend -->
            @if(!$rootUnits->isEmpty())
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 no-print">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Chart Legend</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div>
                        <div class="font-semibold text-gray-600 mb-2">Position Status:</div>
                        <div class="flex items-center gap-2 mb-1">
                            <div style="width: 20px; height: 20px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); border: 2px solid #10b981;"></div>
                            <span>Filled Position</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div style="width: 20px; height: 20px; border-radius: 50%; background: linear-gradient(135deg, #ef4444, #dc2626); border: 2px solid #ef4444;"></div>
                            <span>Vacant Position</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-600 mb-2">Unit Types:</div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <div style="width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                                <span>Ministry</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div style="width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
                                <span>Directorate</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div style="width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
                                <span>Division</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div style="width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);"></div>
                                <span>Unit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div style="width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);"></div>
                                <span>Section</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-600 mb-2">Controls:</div>
                        <div class="space-y-1 text-gray-600">
                            <div>• Click node to view details</div>
                            <div>• Click expand/collapse to toggle branches</div>
                            <div>• Drag to pan, scroll to zoom</div>
                            <div>• Click "Refresh Data" to reload from database</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div id="org-chart-container" class="min-h-screen">
                @if($rootUnits->isEmpty())
                    <div class="text-center py-20">
                        <div class="text-gray-400 text-6xl mb-4">📊</div>
                        <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Organizational Data</h3>
                        <p class="text-gray-500">Please add organizational units to see the chart.</p>
                    </div>
                @else
                    <!-- OrgChart.js Container (Default) -->
                    <div id="orgchart-container" style="width: 100%; height: 800px;"></div>
                    
                    <!-- Traditional View (Hidden by default) -->
                    <div id="traditional-view" class="hidden" style="display: none;">
                        <div class="flex flex-col items-center py-4">
                            @foreach($rootUnits as $rootUnit)
                                @include('org-chart.partials.unit-node', ['unit' => $rootUnit, 'level' => 1, 'allUnits' => $allUnits])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Unit Details Modal -->
    <div id="unitDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4" onclick="closeModalOnBackdrop(event)">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div id="unitDetailsContent" class="p-6">
                <!-- Loading state -->
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading unit details...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4" onclick="closeExportModalOnBackdrop(event)">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div id="exportModalContent" class="p-0">
                <!-- Loading state -->
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading export options...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View Toggle
        let currentView = 'orgchart'; // 'orgchart' or 'traditional'
        
        $(document).ready(function() {
            $('#toggleViewBtn').on('click', function() {
                if (currentView === 'orgchart') {
                    // Switch to Traditional View
                    $('#orgchart-container').hide();
                    $('#traditional-view').removeClass('hidden').css('display', 'block');
                    currentView = 'traditional';
                    $(this).text('Switch to Interactive View');
                } else {
                    // Switch to Interactive View
                    $('#traditional-view').addClass('hidden').css('display', 'none');
                    $('#orgchart-container').show();
                    currentView = 'orgchart';
                    $(this).text('Switch to Traditional View');
                }
            });
        });
        
        // Global org chart instance
        let orgChartInstance = null;
        
        // Initialize OrgChart.js (Dynamic - pulls from database)
        @if(!$rootUnits->isEmpty())
        function loadOrgChart(refresh = false) {
            // Show loading indicator
            if (refresh) {
                $('#orgchart-container').html(`
                    <div class="text-center py-20">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <p class="mt-4 text-gray-600">Refreshing data from database...</p>
                    </div>
                `);
            }
            
            // Build filter parameters
            const params = new URLSearchParams();
            const unitId = $('#filterUnit').val();
            const unitType = $('#filterUnitType').val();
            const assignmentType = $('#filterAssignmentType').val();
            const status = $('#filterStatus').val();
            const dateFrom = $('#filterDateFrom').val();
            const dateTo = $('#filterDateTo').val();
            const showVacant = $('#filterShowVacant').is(':checked');
            
            if (unitId) params.append('unit_id', unitId);
            if (unitType) params.append('unit_type', unitType);
            if (assignmentType) params.append('assignment_type', assignmentType);
            if (status) params.append('status', status);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            params.append('show_vacant', showVacant ? '1' : '0');
            
            // Fetch org chart data from database with filters
            const url = '{{ route("org-chart.orgchartjs") }}' + (params.toString() ? '?' + params.toString() : '');
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Transform data for OrgChart.js
                    const orgChartData = transformDataForOrgChart(data);
                    
                    if (!orgChartData) {
                        $('#orgchart-container').html(`
                            <div class="text-center py-20">
                                <p class="text-gray-600">No organizational data available.</p>
                            </div>
                        `);
                        return;
                    }
                    
                    // Destroy existing instance if refreshing
                    if (orgChartInstance && refresh) {
                        $('#orgchart-container').orgchart('destroy');
                    }
                    
                    // Initialize OrgChart
                    orgChartInstance = $('#orgchart-container').orgchart({
                        'data': orgChartData,
                        'nodeContent': 'title',
                        'pan': true,
                        'zoom': true,
                        'direction': 't2b', // top to bottom
                        'toggleSiblingsResp': true,
                        'expandCollapse': true,
                        'draggable': false,
                        'createNode': function($node, data) {
                            // Check if this is an advisory body
                            const isAdvisoryBody = data.is_advisory_body || data.unit_type === 'ADVISORY_BODY';
                            
                            // Custom node template with vacant/filled indicators
                            const unitTypeColors = {
                                'MINISTRY': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                'DIRECTORATE': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'DIVISION': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                'UNIT': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                                'SECTION': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                                'ADVISORY_BODY': 'linear-gradient(135deg, #D4AF37 0%, #C4A027 100%)',
                            };
                            
                            // Determine background color - darker/reddish for vacant positions
                            let bgColor = unitTypeColors[data.unit_type] || '#e5e7eb';
                            if (data.is_vacant && !isAdvisoryBody) {
                                // Add red tint for vacant positions (but not for advisory bodies)
                                bgColor = bgColor.replace('100%)', '85%)') + ', linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(220, 38, 38, 0.3) 100%)';
                            }
                            
                            // Avatar with status indicator - special styling for advisory bodies
                            let avatarBg, avatar;
                            if (isAdvisoryBody) {
                                avatarBg = 'linear-gradient(135deg, #D4AF37, #C4A027)';
                                avatar = `<div class="org-avatar" style="width: 45px; height: 45px; border-radius: 50%; background: ${avatarBg}; display: flex; align-items: center; justify-content: center; color: #1F2937; font-weight: bold; margin: 0 auto 8px; border: 3px solid #1F2937; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <svg style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>`;
                            } else {
                                avatarBg = data.is_vacant 
                                    ? 'linear-gradient(135deg, #ef4444, #dc2626)' 
                                    : 'linear-gradient(135deg, #10b981, #059669)';
                                avatar = data.avatar 
                                    ? `<div class="org-avatar" style="width: 45px; height: 45px; border-radius: 50%; background: ${avatarBg}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin: 0 auto 8px; border: 3px solid ${data.is_vacant ? '#ef4444' : '#10b981'}; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">${data.avatar}</div>`
                                    : `<div class="org-avatar" style="width: 45px; height: 45px; border-radius: 50%; background: ${avatarBg}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin: 0 auto 8px; border: 3px solid ${data.is_vacant ? '#ef4444' : '#10b981'}; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">?</div>`;
                            }
                            
                            // Assignment type badge colors
                            const assignmentTypeColors = {
                                'SUBSTANTIVE': 'rgba(16, 185, 129, 0.9)',
                                'ACTING': 'rgba(59, 130, 246, 0.9)',
                                'TEMPORARY': 'rgba(245, 158, 11, 0.9)',
                                'SECONDMENT': 'rgba(139, 92, 246, 0.9)'
                            };
                            
                            // Position status badge - different for advisory bodies
                            let statusBadge = '';
                            if (isAdvisoryBody) {
                                statusBadge = '<div style="display: inline-block; background: rgba(31, 41, 55, 0.9); color: #D4AF37; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; margin-top: 4px;">ADVISORY BODY</div>';
                            } else {
                                if (data.is_vacant) {
                                    statusBadge = '<div style="display: inline-block; background: rgba(239, 68, 68, 0.9); color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; margin-top: 4px;">VACANT</div>';
                                } else if (data.assignment_type) {
                                    const bgColor = assignmentTypeColors[data.assignment_type] || 'rgba(16, 185, 129, 0.9)';
                                    statusBadge = `<div style="display: inline-block; background: ${bgColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; margin-top: 4px;">${data.assignment_type}</div>`;
                                } else {
                                    statusBadge = '<div style="display: inline-block; background: rgba(16, 185, 129, 0.9); color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; margin-top: 4px;">FILLED</div>';
                                }
                            }
                            
                            // Position statistics - not shown for advisory bodies
                            const positionStats = (!isAdvisoryBody && data.total_positions > 0)
                                ? `<div style="font-size: 9px; margin-top: 6px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.2);">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                        <span>Filled:</span>
                                        <span style="color: #10b981; font-weight: 600;">${data.filled_positions}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Vacant:</span>
                                        <span style="color: #ef4444; font-weight: 600;">${data.vacant_positions}</span>
                                    </div>
                                </div>`
                                : '';
                            
                            // Children indicator
                            const childrenIndicator = data.has_children 
                                ? `<div style="font-size: 9px; margin-top: 4px; opacity: 0.8;">
                                    <svg style="width: 12px; height: 12px; display: inline; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                    </svg>
                                    ${data.children_count} ${data.children_count === 1 ? 'unit' : 'units'}
                                </div>`
                                : '';
                            
                            // Text color for advisory bodies
                            const textColor = isAdvisoryBody ? '#1F2937' : 'white';
                            
                            $node.css({
                                'background': bgColor,
                                'border-radius': '10px',
                                'padding': '14px',
                                'box-shadow': isAdvisoryBody
                                    ? '0 4px 6px rgba(212, 175, 55, 0.3), 0 2px 4px rgba(0,0,0,0.1)'
                                    : (data.is_vacant 
                                        ? '0 4px 6px rgba(239, 68, 68, 0.3), 0 2px 4px rgba(0,0,0,0.1)' 
                                        : '0 4px 6px rgba(0,0,0,0.1)'),
                                'cursor': 'pointer',
                                'border': isAdvisoryBody 
                                    ? '2px solid #1F2937' 
                                    : (data.is_vacant ? '2px solid rgba(239, 68, 68, 0.5)' : '2px solid rgba(255,255,255,0.3)'),
                                'transition': 'all 0.3s ease',
                                'min-width': '200px'
                            });
                            
                            $node.on('mouseenter', function() {
                                $(this).css({
                                    'transform': 'scale(1.05)',
                                    'z-index': '1000',
                                    'box-shadow': isAdvisoryBody
                                        ? '0 8px 16px rgba(212, 175, 55, 0.4), 0 4px 8px rgba(0,0,0,0.2)'
                                        : (data.is_vacant 
                                            ? '0 8px 16px rgba(239, 68, 68, 0.4), 0 4px 8px rgba(0,0,0,0.2)' 
                                            : '0 8px 16px rgba(0,0,0,0.2)')
                                });
                            }).on('mouseleave', function() {
                                $(this).css({
                                    'transform': 'scale(1)',
                                    'z-index': 'auto'
                                });
                            });
                            
                            // Store advisory body info in the node for later access
                            $node.data('is-advisory-body', isAdvisoryBody);
                            $node.data('advisory-body-id', data.advisory_body_id);
                            $node.data('unit-id', data.id);
                            
                            $node.on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                // Check if this is an advisory body - use negative ID as primary indicator
                                const nodeId = data.id;
                                const isAdvisory = (nodeId < 0) || isAdvisoryBody || (data.unit_type === 'ADVISORY_BODY');
                                
                                // For advisory bodies, extract the actual ID from the negative ID
                                // If nodeId is -1, the actual advisory body ID is 1
                                let advisoryBodyId = data.advisory_body_id;
                                if (isAdvisory && nodeId < 0 && !advisoryBodyId) {
                                    // Extract the actual ID from the negative ID
                                    advisoryBodyId = Math.abs(nodeId);
                                }
                                
                                if (isAdvisory && advisoryBodyId) {
                                    // Show advisory body details in modal (public route)
                                    showAdvisoryBodyDetails(advisoryBodyId);
                                    return false;
                                } else if (isAdvisory && nodeId < 0) {
                                    // Fallback: use absolute value of negative ID as advisory body ID
                                    const absId = Math.abs(nodeId);
                                    showAdvisoryBodyDetails(absId);
                                    return false;
                                } else if (nodeId && nodeId > 0) {
                                    // Only call showUnitDetails for positive IDs (regular units)
                                    showUnitDetails(nodeId);
                                } else {
                                    // For invalid cases, don't do anything
                                    console.warn('Invalid unit ID or advisory body without ID:', nodeId, advisoryBodyId);
                                }
                                return false;
                            });
                            
                            // Add custom HTML with enhanced visualization
                            const customHtml = `
                                <div style="text-align: center; color: ${textColor};">
                                    ${avatar}
                                    <div style="font-size: 10px; font-weight: 600; margin-bottom: 4px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">${isAdvisoryBody ? 'ADVISORY BODY' : data.unit_type}</div>
                                    <div style="font-size: 15px; font-weight: bold; margin-bottom: 6px; line-height: 1.2;">${data.name}</div>
                                    ${!isAdvisoryBody ? `<div style="font-size: 12px; opacity: 0.95; margin-bottom: 4px; font-weight: 500;">${data.title}</div>` : ''}
                                    ${statusBadge}
                                    ${positionStats}
                                    ${childrenIndicator}
                                </div>
                            `;
                            
                            $node.html(customHtml);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading org chart:', error);
                    $('#orgchart-container').html(`
                        <div class="text-center py-20">
                            <div class="text-red-500 text-4xl mb-4">⚠️</div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Chart</h3>
                            <p class="text-gray-600">Unable to load organizational chart. Please refresh the page.</p>
                            <button onclick="location.reload()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Refresh Page
                            </button>
                        </div>
                    `);
                });
        }
        
        // Refresh chart function
        function refreshChart() {
            loadOrgChart(true);
        }
        
        // Filter toggle
        $(document).ready(function() {
            $('#toggleFilters').on('click', function() {
                const content = $('#filterContent');
                const isHidden = content.hasClass('hidden');
                content.toggleClass('hidden');
                $(this).find('span').text(isHidden ? 'Hide Filters' : 'Show Filters');
                $(this).find('svg').css('transform', isHidden ? 'rotate(180deg)' : 'rotate(0deg)');
            });
            
            // Apply filters
            $('#applyFilters').on('click', function() {
                loadOrgChart(true);
            });
            
            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#filterUnit').val('');
                $('#filterUnitType').val('');
                $('#filterAssignmentType').val('');
                $('#filterStatus').val('ACTIVE');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                $('#filterShowVacant').prop('checked', true);
                loadOrgChart(true);
            });
            
            // Load chart on page load
            loadOrgChart(false);
        });
        @endif
        
        // Transform API data to OrgChart.js format
        function transformDataForOrgChart(data) {
            if (!data || data.length === 0) return null;
            
            // Build a map of all nodes by ID for quick lookup
            const nodeMap = new Map();
            const processedNodes = new Set();
            data.forEach(node => {
                nodeMap.set(node.id, node);
            });
            
            // Find root nodes (no parent or parent not in data)
            const rootNodes = data.filter(node => {
                return !node.pid || node.pid === null || !nodeMap.has(node.pid);
            });
            
            // If no root nodes found, try to find nodes with null/undefined pid
            if (rootNodes.length === 0) {
                const nullParentNodes = data.filter(node => !node.pid || node.pid === null);
                if (nullParentNodes.length > 0) {
                    rootNodes.push(...nullParentNodes);
                }
            }
            
            // If still no root, find the MINISTRY node or use first node
            if (rootNodes.length === 0 && data.length > 0) {
                const ministryNode = data.find(n => n.unit_type === 'MINISTRY' || n.name.toLowerCase().includes('ministry'));
                if (ministryNode) {
                    rootNodes.push(ministryNode);
                } else {
                    console.warn('No root node found, using first node as root');
                    rootNodes.push(data[0]);
                }
            }
            
            // Build tree recursively - includes ALL nodes
            function buildTree(node) {
                if (processedNodes.has(node.id)) {
                    // Already processed (circular reference protection)
                    return null;
                }
                processedNodes.add(node.id);
                
                // Find all children of this node (handle both positive and negative IDs for advisory bodies)
                const children = data.filter(n => {
                    if (processedNodes.has(n.id)) return false; // Skip already processed
                    // Exact match
                    if (n.pid === node.id) return true;
                    // Handle advisory bodies with negative IDs
                    if (n.pid && node.id && Math.abs(n.pid) === Math.abs(node.id)) return true;
                    return false;
                });
                
                const result = {
                    'id': node.id,
                    'name': node.name,
                    'title': node.title,
                    'unit_type': node.unit_type,
                    'code': node.code,
                    'position_title': node.position_title,
                    'email': node.email,
                    'avatar': node.avatar,
                    'is_vacant': node.is_vacant,
                    'has_children': children.length > 0,
                    'total_positions': node.total_positions || 0,
                    'filled_positions': node.filled_positions || 0,
                    'vacant_positions': node.vacant_positions || 0,
                    'children_count': children.length,
                    'is_advisory_body': node.is_advisory_body || false,
                    'advisory_body_id': node.advisory_body_id || null
                };
                
                if (children.length > 0) {
                    result.children = children
                        .map(child => buildTree(child))
                        .filter(child => child !== null); // Remove nulls from circular refs
                }
                
                return result;
            }
            
            // Build tree starting from root(s)
            // If multiple roots, prioritize MINISTRY, otherwise use first
            let rootNode = rootNodes[0];
            if (rootNodes.length > 1) {
                const ministryNode = rootNodes.find(n => n.unit_type === 'MINISTRY' || n.name.toLowerCase().includes('ministry'));
                if (ministryNode) {
                    rootNode = ministryNode;
                }
            }
            
            const tree = buildTree(rootNode);
            
            // If there are unprocessed nodes (orphans), attach them to root
            const unprocessedNodes = data.filter(n => !processedNodes.has(n.id));
            if (unprocessedNodes.length > 0 && tree) {
                console.warn(`Found ${unprocessedNodes.length} orphaned nodes, attaching to root`);
                if (!tree.children) {
                    tree.children = [];
                }
                unprocessedNodes.forEach(orphan => {
                    const orphanTree = buildTree(orphan);
                    if (orphanTree) {
                        tree.children.push(orphanTree);
                    }
                });
            }
            
            return tree;
        }
        
        // Expand all nodes in org chart
        function expandAll() {
            if (orgChartInstance && typeof orgChartInstance.orgchart === 'function') {
                $('#orgchart-container').find('.node').each(function() {
                    const $node = $(this);
                    const $children = $node.siblings('.nodes').find('.node');
                    if ($children.length > 0 && $node.find('.edge').hasClass('verticalEdge')) {
                        $node.find('.toggleBtn').trigger('click');
                    }
                });
            } else {
                // Fallback for traditional view
                document.querySelectorAll('.children-container').forEach(el => {
                    el.classList.remove('hidden');
                });
            }
        }
        
        // Collapse all nodes in org chart (except root)
        function collapseAll() {
            if (orgChartInstance && typeof orgChartInstance.orgchart === 'function') {
                $('#orgchart-container').find('.node').each(function() {
                    const $node = $(this);
                    // Don't collapse root node
                    if ($node.closest('.orgchart').find('.node').first()[0] !== $node[0]) {
                        const $children = $node.siblings('.nodes').find('.node');
                        if ($children.length > 0 && !$node.find('.edge').hasClass('verticalEdge')) {
                            $node.find('.toggleBtn').trigger('click');
                        }
                    }
                });
            } else {
                // Fallback for traditional view
                document.querySelectorAll('.children-container').forEach(el => {
                    if (!el.classList.contains('root-children')) {
                        el.classList.add('hidden');
                    }
                });
            }
        }

        // Show unit details modal
        function showUnitDetails(unitId) {
            // Don't process negative IDs (these are advisory bodies)
            if (!unitId || unitId <= 0) {
                console.warn('Invalid unit ID:', unitId);
                return;
            }
            
            const modal = document.getElementById('unitDetailsModal');
            const content = document.getElementById('unitDetailsContent');
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Fetch unit details - use Laravel route helper
            const url = `{{ route('org-chart.unit.show', ['id' => '__ID__']) }}`.replace('__ID__', unitId);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    // Check response type
                    const contentType = response.headers.get('content-type');
                    
                    // If we got redirected (status 302, 301, etc.), the browser might have followed it
                    // Check if the response is HTML and looks like a login page
                    if (response.redirected && response.url.includes('login')) {
                        throw new Error('Authentication required');
                    }
                    
                    if (!response.ok && response.status !== 200) {
                        // Check if it's a redirect status
                        if (response.status >= 300 && response.status < 400) {
                            throw new Error('Authentication required');
                        }
                        throw new Error('Unit not found');
                    }
                    
                    return response.text();
                })
                .then(html => {
                    // Check if the response is actually a login page
                    if (html && (html.includes('login') || html.includes('Login') || html.includes('password') || html.includes('Sign in'))) {
                        // Double check - if it's a very short response or contains login form, it's probably a redirect
                        if (html.length < 5000 || html.includes('name="email"') || html.includes('type="password"')) {
                            throw new Error('Authentication required');
                        }
                    }
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading unit details:', error);
                    const errorMessage = error.message === 'Authentication required' 
                        ? 'Please log in to view unit details.'
                        : 'Unable to load unit details. Please try again.';
                    content.innerHTML = `
                        <div class="text-center py-12">
                            <div class="text-red-500 text-4xl mb-4">⚠️</div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Details</h3>
                            <p class="text-gray-600">${errorMessage}</p>
                            ${error.message === 'Authentication required' ? `
                                <a href="/login" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Go to Login
                                </a>
                            ` : ''}
                            <button onclick="closeUnitDetailsModal()" class="mt-4 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                Close
                            </button>
                        </div>
                    `;
                });
        }

        // Close modal
        function closeUnitDetailsModal() {
            const modal = document.getElementById('unitDetailsModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Show advisory body details modal
        function showAdvisoryBodyDetails(advisoryBodyId) {
            if (!advisoryBodyId || advisoryBodyId <= 0) {
                console.warn('Invalid advisory body ID:', advisoryBodyId);
                return;
            }
            
            const modal = document.getElementById('unitDetailsModal');
            const content = document.getElementById('unitDetailsContent');
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Fetch advisory body details - use public route
            const url = `{{ route('org-chart.advisory-body.show', ['id' => '__ID__']) }}`.replace('__ID__', advisoryBodyId);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (response.redirected && response.url.includes('login')) {
                        throw new Error('Authentication required');
                    }
                    
                    if (response.status === 401 || response.status === 403) {
                        throw new Error('Authentication required');
                    }
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: Advisory body not found`);
                    }
                    
                    return response.text();
                })
                .then(html => {
                    // Check if response is a login page
                    if (html && html.length < 10000) {
                        if (html.includes('login') && (html.includes('name="email"') || html.includes('type="password"'))) {
                            throw new Error('Authentication required');
                        }
                    }
                    
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading advisory body details:', error);
                    const isAuthError = error.message === 'Authentication required';
                    const errorMessage = isAuthError 
                        ? 'Unable to load advisory body details.'
                        : `Unable to load advisory body details: ${error.message}`;
                    
                    content.innerHTML = `
                        <div class="text-center py-12">
                            <div class="text-red-500 text-4xl mb-4">⚠️</div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Details</h3>
                            <p class="text-gray-600 mb-4">${errorMessage}</p>
                            <button onclick="closeUnitDetailsModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Close
                            </button>
                        </div>
                    `;
                });
        }
        
        // Close modal when clicking backdrop
        function closeModalOnBackdrop(event) {
            if (event.target.id === 'unitDetailsModal') {
                closeUnitDetailsModal();
            }
        }

        // Print org chart function
        function printOrgChart() {
            // Expand all nodes before printing
            if (orgChartInstance && typeof orgChartInstance.orgchart === 'function') {
                // First, expand all nodes recursively
                function expandAllNodes() {
                    let expanded = false;
                    $('#orgchart-container').find('.node').each(function() {
                        const $node = $(this);
                        const $toggleBtn = $node.find('.toggleBtn');
                        if ($toggleBtn.length > 0) {
                            // Check if node is collapsed
                            const $edge = $node.find('.edge');
                            const isCollapsed = !$edge.hasClass('verticalEdge');
                            if (isCollapsed) {
                                $toggleBtn.trigger('click');
                                expanded = true;
                            }
                        }
                    });
                    return expanded;
                }
                
                // Keep expanding until all nodes are expanded
                let attempts = 0;
                const expandInterval = setInterval(() => {
                    attempts++;
                    const hasMore = expandAllNodes();
                    if (!hasMore || attempts > 10) {
                        clearInterval(expandInterval);
                        // Wait a bit more for animations, then print
                        setTimeout(() => {
                            window.print();
                        }, 500);
                    }
                }, 300);
            } else {
                // Fallback: expand all in traditional view
                document.querySelectorAll('.children-container').forEach(el => {
                    el.classList.remove('hidden');
                });
                setTimeout(() => {
                    window.print();
                }, 300);
            }
        }

        // Show export modal
        function showExportModal() {
            const modal = document.getElementById('exportModal');
            const content = document.getElementById('exportModalContent');
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Fetch export options
            fetch('{{ route("org-chart.export") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                    // Re-initialize form action after loading
                    setTimeout(() => {
                        if (typeof updateFormAction === 'function') {
                            updateFormAction();
                        }
                        // Re-attach event listeners
                        document.querySelectorAll('input[name="format"]').forEach(radio => {
                            radio.addEventListener('change', updateFormAction);
                        });
                    }, 100);
                })
                .catch(error => {
                    console.error('Error loading export options:', error);
                    content.innerHTML = `
                        <div class="text-center py-12 p-6">
                            <div class="text-red-500 text-4xl mb-4">⚠️</div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Export Options</h3>
                            <p class="text-gray-600 mb-4">Unable to load export options. Please try again.</p>
                            <button onclick="closeExportModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    `;
                });
        }

        // Close export modal
        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking backdrop
        function closeExportModalOnBackdrop(event) {
            if (event.target.id === 'exportModal') {
                closeExportModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUnitDetailsModal();
                closeExportModal();
            }
        });
    </script>
</body>
</html>
