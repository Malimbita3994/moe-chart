<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class AdminActivityNotification extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @param string $type Short type/category (e.g. 'user', 'position', 'organization')
     * @param string $message Human-readable message (e.g. "created a new user")
     * @param string $subject Optional subject/title (e.g. "New user: John Doe")
     * @param int|null $actorId User ID who performed the action (for avatar/name)
     * @param string|null $url Optional link when notification is clicked
     */
    public function __construct(
        public string $type,
        public string $message,
        public string $subject = '',
        public ?int $actorId = null,
        public ?string $url = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'subject' => $this->subject,
            'actor_id' => $this->actorId,
            'url' => $this->url,
        ];
    }
}
