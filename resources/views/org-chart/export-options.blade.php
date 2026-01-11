<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Options - {{ config('app.name', 'MOE') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .container {
            max-width: 48rem;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            padding: 0;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .card-header-content {
            position: relative;
            z-index: 1;
        }
        
        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .header-icon svg {
            width: 32px;
            height: 32px;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        
        .subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .card-body {
            padding: 2.5rem 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-title svg {
            width: 16px;
            height: 16px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group:last-child {
            margin-bottom: 0;
        }
        
        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #9ca3af;
            pointer-events: none;
            z-index: 1;
        }
        
        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: white;
            color: #1f2937;
        }
        
        select {
            padding-left: 3rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 20px;
        }
        
        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        input[type="text"]:hover,
        input[type="date"]:hover,
        select:hover {
            border-color: #d1d5db;
        }
        
        .radio-group, .checkbox-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .radio-option, .checkbox-option {
            position: relative;
            flex: 1;
            min-width: 120px;
        }
        
        .radio-option input[type="radio"],
        .checkbox-option input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .radio-label, .checkbox-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .radio-label::before,
        .checkbox-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: left 0.3s ease;
            z-index: 0;
        }
        
        .radio-label span,
        .checkbox-label span {
            position: relative;
            z-index: 1;
            transition: color 0.3s ease;
        }
        
        .radio-option input[type="radio"]:checked + .radio-label,
        .checkbox-option input[type="checkbox"]:checked + .checkbox-label {
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .radio-option input[type="radio"]:checked + .radio-label::before,
        .checkbox-option input[type="checkbox"]:checked + .checkbox-label::before {
            left: 0;
        }
        
        .radio-label:hover,
        .checkbox-label:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .help-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .help-text svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 2px solid #e5e7eb;
        }
        
        button[type="submit"],
        .btn-cancel {
            flex: 1;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        button[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        button[type="submit"]:hover::before {
            width: 300px;
            height: 300px;
        }
        
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }
        
        button[type="submit"]:active {
            transform: translateY(0);
        }
        
        button[type="submit"] span {
            position: relative;
            z-index: 1;
        }
        
        .btn-cancel {
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
        }
        
        .btn-cancel:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
        }
        
        .placeholder-box {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px dashed #d1d5db;
            text-align: center;
        }
        
        .placeholder-box p {
            font-size: 0.875rem;
            color: #6b7280;
            font-style: italic;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }
        
        .back-link:hover {
            opacity: 1;
        }
        
        @media (max-width: 640px) {
            .card-body {
                padding: 1.5rem;
            }
            
            .card-header {
                padding: 2rem 1.5rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('org-chart.index') }}" class="back-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Chart
        </a>
        
        <div class="card">
            <div class="card-header">
                <div class="card-header-content">
                    <div class="header-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h1>Export Organizational Chart</h1>
                    <p class="subtitle">Configure your export settings and generate professional documents</p>
                </div>
            </div>
            
            <div class="card-body">
                <form id="exportForm" method="GET" action="{{ route('org-chart.export.pdf') }}">
                    <!-- Export Format -->
                    <div class="form-section">
                        <div class="section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Export Format
                        </div>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="format" id="format_pdf" value="pdf" checked onchange="updateFormAction()">
                                <label for="format_pdf" class="radio-label">
                                    <span>📄 PDF Document</span>
                                </label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="format" id="format_image" value="image" onchange="updateFormAction()">
                                <label for="format_image" class="radio-label">
                                    <span>🖼️ Image (PNG)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Page Settings -->
                    <div class="form-section">
                        <div class="section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Page Settings
                        </div>
                        
                        <div class="form-group">
                            <label for="page_size">Page Size</label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </div>
                                <select name="page_size" id="page_size">
                                    <option value="A4" selected>A4 (210 × 297 mm)</option>
                                    <option value="A3">A3 (297 × 420 mm)</option>
                                    <option value="A2">A2 (420 × 594 mm)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Orientation</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" name="orientation" id="orientation_portrait" value="portrait">
                                    <label for="orientation_portrait" class="radio-label">
                                        <span>📱 Portrait</span>
                                    </label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="orientation" id="orientation_landscape" value="landscape" checked>
                                    <label for="orientation_landscape" class="radio-label">
                                        <span>🖥️ Landscape</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Details -->
                    <div class="form-section">
                        <div class="section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Document Details
                        </div>
                        
                        <div class="form-group">
                            <label for="effective_date">Version / Effective Date</label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="date" name="effective_date" id="effective_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="watermark">Watermark</label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                </div>
                                <select name="watermark" id="watermark">
                                    <option value="OFFICIAL" selected>OFFICIAL</option>
                                    <option value="DRAFT">DRAFT</option>
                                    <option value="NONE">None</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="form-section">
                        <div class="section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter Options
                        </div>
                        
                        <div class="form-group">
                            <label for="unit_filter">Export Selected Branch (Optional)</label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <select name="unit_id" id="unit_filter">
                                    <option value="">All Units (Full Chart)</option>
                                    @foreach($allUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ str_repeat('—', $unit->level - 1) }} {{ $unit->name }} ({{ strtoupper($unit->unit_type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="help-text">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Select a specific unit to export only that branch and its children
                            </p>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <div class="checkbox-option">
                                    <input type="checkbox" name="show_vacant" id="show_vacant" value="1" checked>
                                    <label for="show_vacant" class="checkbox-label">
                                        <span>Show Vacant Positions</span>
                                    </label>
                                </div>
                            </div>
                            <p class="help-text">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Uncheck to hide positions that are not currently assigned
                            </p>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <div class="checkbox-option">
                                    <input type="checkbox" name="show_legend" id="show_legend" value="1" checked>
                                    <label for="show_legend" class="checkbox-label">
                                        <span>Show Color Legend</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Features -->
                    <div class="form-section">
                        <div class="section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Advanced Features
                        </div>
                        <div class="placeholder-box">
                            <p>Digital signature support will be added in future updates</p>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="generated_by" value="{{ config('app.name', 'MOE Chart System') }}">

                    <!-- Action Buttons -->
                    <div class="button-group">
                        <button type="submit">
                            <span>Generate Export</span>
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </button>
                        <a href="{{ route('org-chart.index') }}" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFormAction() {
            const form = document.getElementById('exportForm');
            const formatRadio = document.querySelector('input[name="format"]:checked');
            
            if (!formatRadio) {
                console.error('No format selected');
                return;
            }
            
            const format = formatRadio.value;
            
            if (format === 'image') {
                form.action = '{{ route("org-chart.export.image") }}';
            } else {
                form.action = '{{ route("org-chart.export.pdf") }}';
            }
        }
        
        // Initialize form action on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFormAction();
            
            // Also update when format changes
            document.querySelectorAll('input[name="format"]').forEach(radio => {
                radio.addEventListener('change', updateFormAction);
            });
            
            // Add smooth scroll behavior
            document.querySelectorAll('input, select').forEach(element => {
                element.addEventListener('focus', function() {
                    this.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            });
        });
    </script>
</body>
</html>
