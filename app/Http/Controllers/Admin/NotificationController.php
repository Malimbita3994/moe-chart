<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(10);

        $placeholderAvatar = 'data:image/svg+xml,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="20" fill="#e5e7eb"/><text x="20" y="26" font-size="14" fill="#6b7280" text-anchor="middle" font-family="sans-serif">?</text></svg>'
        );

        $items = $notifications->getCollection()->map(function ($n) use ($placeholderAvatar) {
            $data = $n->data;
            $actor = isset($data['actor_id']) ? User::find($data['actor_id']) : null;
            return (object) [
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
        });
        $notifications->setCollection($items);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'title' => 'Notifications',
        ]);
    }

    /**
     * Mark a notification as read and optionally redirect to its URL.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $url = $notification->data['url'] ?? route('admin.notifications.index');
        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        foreach ($request->user()->unreadNotifications as $n) {
            $n->markAsRead();
        }
        return redirect()->route('admin.notifications.index')->with('success', 'All notifications marked as read.');
    }
}
