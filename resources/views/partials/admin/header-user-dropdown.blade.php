@auth
<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() { this.dropdownOpen = !this.dropdownOpen; },
    closeDropdown() { this.dropdownOpen = false; }
}" @click.away="closeDropdown()">
    <button type="button" class="flex items-center text-gray-700 dark:text-gray-400"
        @click.prevent="toggleDropdown()">
        <span class="mr-3 overflow-hidden rounded-full h-11 w-11 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-semibold">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </span>
        <span class="block mr-1 font-medium text-sm">{{ auth()->user()->name }}</span>
        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="dropdownOpen" x-transition
        class="absolute right-0 mt-2 w-56 rounded-2xl border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50"
        style="display: none;">
        <div class="pb-3 border-b border-gray-200 dark:border-gray-800">
            <span class="block font-medium text-gray-700 text-sm dark:text-gray-300">{{ auth()->user()->name }}</span>
            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 mt-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5"
                @click="closeDropdown()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                {{ __('Logout') }}
            </button>
        </form>
    </div>
</div>
@endauth
