<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organizational Chart Export - {{ config('app.name', 'MOE') }}</title>
    
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: white;
            color: #1f2937;
            line-height: 1.5;
        }
        
        /* Utility Classes */
        .bg-white { background-color: #ffffff; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .text-center { text-align: center; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-8 { margin-top: 2rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
        .pt-4 { padding-top: 1rem; }
        .pb-4 { padding-bottom: 1rem; }
        .border { border-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-t { border-top-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        .rounded { border-radius: 0.25rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        
        @media (min-width: 768px) {
            .md\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
        
        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: white;
            position: relative;
        }
        .org-node {
            page-break-inside: avoid;
        }
        .level-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .level-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
        .level-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .level-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important; }
        .level-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important; }
        
        /* Organization Node Styles */
        .org-node {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            min-width: 260px;
            max-width: 320px;
            border: 2px solid #e5e7eb;
        }
        
        .unit-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .children-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
            position: relative;
            width: 100%;
        }
        
        /* Badge Styles */
        .level-1, .level-2, .level-3, .level-4, .level-5 {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            color: white;
            display: inline-block;
        }
        
        /* Additional Utility Classes */
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .flex-wrap { flex-wrap: wrap; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-0\.5 { width: 0.125rem; }
        .h-0\.5 { height: 0.125rem; }
        .min-w-0 { min-width: 0; }
        .flex-1 { flex: 1 1 0%; }
        .text-white { color: #ffffff; }
        .text-gray-400 { color: #9ca3af; }
        .bg-gradient-to-br { background-image: linear-gradient(to bottom right, #60a5fa, #a855f7); }
        .from-blue-400 { background-color: #60a5fa; }
        .to-purple-500 { background-color: #a855f7; }
        .bg-gray-300 { background-color: #d1d5db; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-4 { margin-top: 1rem; }
        .pt-3 { padding-top: 0.75rem; }
        .border-t { border-top: 1px solid #e5e7eb; }
        .transform { transform: translate(-50%, -50%); }
        .-translate-x-1\/2 { transform: translateX(-50%); }
        
        @media (min-width: 768px) {
            .md\:mb-8 { margin-bottom: 2rem; }
            .md\:p-6 { padding: 1.5rem; }
            .md\:min-w-\[280px\] { min-width: 280px; }
            .md\:max-w-\[320px\] { max-width: 320px; }
            .md\:gap-6 { gap: 1.5rem; }
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.08);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
        }
        
        .watermark.OFFICIAL {
            color: rgba(34, 197, 94, 0.1);
        }
        
        .watermark.DRAFT {
            color: rgba(239, 68, 68, 0.1);
        }
    </style>
</head>
<body class="bg-white">
    @if(isset($watermark) && $watermark !== 'NONE')
        <div class="watermark {{ $watermark }}">{{ $watermark }}</div>
    @endif

    <!-- Header -->
    <div class="bg-white border-b-2 border-gray-300 mb-6 pb-4">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                THE UNITED REPUBLIC OF TANZANIA
            </h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                MINISTRY OF EDUCATION, SCIENCE AND TECHNOLOGY
            </h2>
            <h3 class="text-xl font-bold text-gray-700 mb-2">
                Organizational Chart
            </h3>
            
            <!-- Version / Effective Date -->
            @if(isset($effectiveDate))
                <p class="text-sm font-semibold text-gray-700 mb-1">
                    Version / Effective Date: {{ \Carbon\Carbon::parse($effectiveDate)->format('F d, Y') }}
                </p>
            @endif
            
            <!-- Generated By -->
            @if(isset($generatedBy))
                <p class="text-xs text-gray-600 mb-1">
                    Generated by: {{ $generatedBy }}
                </p>
            @endif
            
            <p class="text-xs text-gray-500 mt-2">
                Generated on: {{ now()->format('F d, Y \a\t h:i A') }}
            </p>
        </div>
    </div>

    <!-- Color Legend -->
    @if(isset($showLegend) && $showLegend)
    <div class="mb-6 px-4">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-bold text-gray-700 mb-3">Color Legend</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded level-1"></div>
                    <span class="text-gray-700">Level 1</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded level-2"></div>
                    <span class="text-gray-700">Level 2</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded level-3"></div>
                    <span class="text-gray-700">Level 3</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded level-4"></div>
                    <span class="text-gray-700">Level 4</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded level-5"></div>
                    <span class="text-gray-700">Level 5+</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Org Chart Container -->
    <div class="px-4">
        @if($rootUnits->isEmpty())
            <div class="text-center py-20">
                <p class="text-gray-500">No organizational data available</p>
            </div>
        @else
            <div class="flex flex-col items-center">
                @foreach($rootUnits as $rootUnit)
                    @include('org-chart.partials.unit-node-export', ['unit' => $rootUnit, 'level' => 1, 'allUnits' => $allUnits])
                @endforeach
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
        <p class="font-semibold">Official Document - Ministry of Education, Science and Technology</p>
        @if(isset($effectiveDate))
            <p>This document is valid as of {{ \Carbon\Carbon::parse($effectiveDate)->format('F d, Y') }}</p>
        @else
            <p>This document is generated automatically and is valid as of {{ now()->format('F d, Y') }}</p>
        @endif
        @if(isset($generatedBy))
            <p>Generated by: {{ $generatedBy }}</p>
        @endif
        <p class="mt-1">Document ID: {{ strtoupper(uniqid('MOE-')) }}</p>
    </div>
</body>
</html>
