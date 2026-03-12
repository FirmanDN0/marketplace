<?php

namespace App\Services;

use App\Models\AppNotification;

class NotificationService
{
    public static function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null
    ): AppNotification {
        return AppNotification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'data'       => $data,
            'action_url' => $actionUrl,
        ]);
    }
}
