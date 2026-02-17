<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addNamespace('tailadmin', resource_path('views/admin/resources/views'));

        View::composer('tailadmin::layouts.app', function ($view) {
            $placeholderAvatar = 'data:image/svg+xml,' . rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="20" fill="#e5e7eb"/><text x="20" y="26" font-size="14" fill="#6b7280" text-anchor="middle" font-family="sans-serif">?</text></svg>'
            );

            if (! Auth::check()) {
                $view->with('adminNotifications', []);
                return;
            }

            $notifications = Auth::user()
                ->notifications()
                ->take(20)
                ->get();

            $adminNotifications = $notifications->map(function ($n) use ($placeholderAvatar) {
                $data = $n->data;
                $actor = isset($data['actor_id']) ? User::find($data['actor_id']) : null;
                return [
                    'id' => $n->id,
                    'userName' => $actor ? ($actor->full_name ?: $actor->name) : 'System',
                    'userImage' => $actor ? $actor->avatar_url : $placeholderAvatar,
                    'message' => $data['message'] ?? '',
                    'subject' => $data['subject'] ?? ($data['type'] ?? 'Update'),
                    'type' => $data['type'] ?? 'system',
                    'time' => $n->created_at->diffForHumans(),
                    'url' => $data['url'] ?? null,
                    'read_at' => $n->read_at,
                ];
            })->values()->all();

            $view->with('adminNotifications', $adminNotifications);
            $view->with('adminNotificationsUnreadCount', Auth::user()->unreadNotifications()->count());
        });
    }
}
