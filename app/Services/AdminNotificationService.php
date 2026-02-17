<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminActivityNotification;
use Illuminate\Support\Facades\Log;

class AdminNotificationService
{
    /**
     * Notify all system administrators of an activity.
     * Skips the actor so they don't get their own notification.
     */
    public static function notifyAdmins(string $type, string $message, string $subject = '', ?int $actorId = null, ?string $url = null): void
    {
        try {
            $admins = User::whereHas('role', fn ($q) => $q->where('slug', 'system-administrator'))
                ->where('status', 'ACTIVE')
                ->get();

            $notification = new AdminActivityNotification($type, $message, $subject, $actorId, $url);

            /** @var User $admin */
            foreach ($admins as $admin) {
                if ($actorId !== null && $admin->id === $actorId) {
                    continue;
                }
                $admin->notify($notification);
            }
        } catch (\Throwable $e) {
            Log::warning('AdminNotificationService: failed to notify admins', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
