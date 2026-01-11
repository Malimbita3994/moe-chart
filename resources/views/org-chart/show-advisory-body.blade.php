<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">{{ $advisoryBody->name }}</h2>
            <button onclick="closeUnitDetailsModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="inline-block px-3 py-1 bg-gradient-to-r from-yellow-400 to-yellow-600 text-gray-900 text-sm font-semibold rounded-full">
            ADVISORY BODY
        </div>
    </div>

    <div class="space-y-6">
        <!-- Reports To Section -->
        @if($advisoryBody->reportsTo)
            <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200">
                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Reports To</h3>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <p class="text-lg font-semibold text-gray-800">{{ $advisoryBody->reportsTo->name ?? 'N/A' }}</p>
                        @if($advisoryBody->reportsTo->unit)
                            <p class="text-sm text-gray-600">{{ $advisoryBody->reportsTo->unit->name }}</p>
                        @endif
                        @if($advisoryBody->reportsTo->title)
                            <p class="text-xs text-gray-500">{{ $advisoryBody->reportsTo->title->name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Additional Information -->
        <div class="bg-white rounded-lg p-4 border-2 border-gray-200">
            <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Information</h3>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $advisoryBody->name }}</dd>
                </div>
                @if($advisoryBody->created_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $advisoryBody->created_at->format('F d, Y') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    @auth
        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.advisory-bodies.show', $advisoryBody->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Full Details (Admin)
            </a>
        </div>
    @endauth
</div>
