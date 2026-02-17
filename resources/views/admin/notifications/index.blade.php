@extends('layouts.admin')

@section('title', $title ?? 'Notifications')
@section('page-title', $title ?? 'Notifications')

@section('content')
<div class="min-w-0 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">All Notifications</h2>
            @if(isset($notifications) && $notifications->total() > 0 && auth()->user() && auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications ?? [] as $notification)
                @php $item = is_object($notification) ? $notification : (object) $notification; @endphp
                <li>
                    <a href="{{ route('admin.notifications.mark-read', $item->id) }}"
                       class="flex gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$item->read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                        <span class="flex-shrink-0 w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600">
                            <img src="{{ $item->userImage }}" alt="" class="w-full h-full object-cover" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm text-gray-800 dark:text-gray-200">
                                <span class="font-medium">{{ $item->userName }}</span>
                                {{ $item->message }}
                                @if($item->subject && $item->subject !== $item->message)
                                    <span class="font-medium">— {{ $item->subject }}</span>
                                @endif
                            </span>
                            <span class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ ucfirst($item->type) }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                                <span>{{ $item->time }}</span>
                            </span>
                        </span>
                    </a>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <p>No notifications yet.</p>
                </li>
            @endforelse
        </ul>
        @if(isset($notifications) && $notifications->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
