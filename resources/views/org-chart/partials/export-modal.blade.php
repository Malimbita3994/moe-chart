<div class="p-0">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">Export Organizational Chart</h2>
                    <p class="text-sm opacity-90 mt-1">Configure your export settings</p>
                </div>
            </div>
            <button onclick="closeExportModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Modal Body -->
    <div class="p-6">
        <form id="exportForm" method="GET" action="{{ route('org-chart.export.pdf') }}">
            <!-- Export Format -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Export Format</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" name="format" value="pdf" checked onchange="updateFormAction()" class="peer sr-only">
                        <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300">
                            <div class="text-center">
                                <div class="text-2xl mb-1">📄</div>
                                <div class="font-medium text-gray-700">PDF</div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="format" value="image" onchange="updateFormAction()" class="peer sr-only">
                        <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300">
                            <div class="text-center">
                                <div class="text-2xl mb-1">🖼️</div>
                                <div class="font-medium text-gray-700">Image (PNG)</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Page Settings -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Page Settings</label>
                <div class="space-y-4">
                    <div>
                        <label for="page_size" class="block text-xs text-gray-600 mb-2">Page Size</label>
                        <select name="page_size" id="page_size" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition-all">
                            <option value="A4" selected>A4 (210 × 297 mm)</option>
                            <option value="A3">A3 (297 × 420 mm)</option>
                            <option value="A2">A2 (420 × 594 mm)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-2">Orientation</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative">
                                <input type="radio" name="orientation" value="portrait" class="peer sr-only">
                                <div class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300">
                                    <span class="font-medium text-sm text-gray-700">📱 Portrait</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="orientation" value="landscape" checked class="peer sr-only">
                                <div class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300">
                                    <span class="font-medium text-sm text-gray-700">🖥️ Landscape</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Details -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Document Details</label>
                <div class="space-y-4">
                    <div>
                        <label for="effective_date" class="block text-xs text-gray-600 mb-2">Version / Effective Date</label>
                        <input type="date" name="effective_date" id="effective_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition-all">
                    </div>
                    <div>
                        <label for="watermark" class="block text-xs text-gray-600 mb-2">Watermark</label>
                        <select name="watermark" id="watermark" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition-all">
                            <option value="OFFICIAL" selected>OFFICIAL</option>
                            <option value="DRAFT">DRAFT</option>
                            <option value="NONE">None</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Filter Options</label>
                <div class="space-y-4">
                    <div>
                        <label for="unit_filter" class="block text-xs text-gray-600 mb-2">Export Selected Branch (Optional)</label>
                        <select name="unit_id" id="unit_filter" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition-all">
                            <option value="">All Units (Full Chart)</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->id }}">{{ str_repeat('—', $unit->level - 1) }} {{ $unit->name }} ({{ strtoupper($unit->unit_type) }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select a specific unit to export only that branch</p>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 transition-all">
                            <input type="checkbox" name="show_vacant" value="1" checked class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700">Show Vacant Positions</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 transition-all">
                            <input type="checkbox" name="show_legend" value="1" checked class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700">Show Color Legend</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="generated_by" value="{{ config('app.name', 'MOE Chart System') }}">

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center gap-2">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generate Export
                    </span>
                </button>
                <button type="button" onclick="closeExportModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFormAction() {
        const form = document.getElementById('exportForm');
        const formatRadio = document.querySelector('input[name="format"]:checked');
        
        if (!formatRadio) {
            return;
        }
        
        const format = formatRadio.value;
        
        if (format === 'image') {
            form.action = '{{ route("org-chart.export.image") }}';
        } else {
            form.action = '{{ route("org-chart.export.pdf") }}';
        }
    }
    
    // Initialize form action
    document.addEventListener('DOMContentLoaded', function() {
        updateFormAction();
        document.querySelectorAll('input[name="format"]').forEach(radio => {
            radio.addEventListener('change', updateFormAction);
        });
    });
</script>
