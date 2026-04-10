<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notification types that also trigger an email.
     */
    private static array $emailTypes = [
        'new_order',
        'order_delivered',
        'order_completed',
        'order_cancelled',
        'payment_refunded',
        'withdraw_processed',
        'withdraw_rejected',
        'dispute_resolved',
    ];

    public static function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null
    ): AppNotification {
        $notification = AppNotification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'data'       => $data,
            'action_url' => $actionUrl,
        ]);

        // Send email for important events
        if (in_array($type, self::$emailTypes)) {
            static::sendEmail($userId, $title, $message, $actionUrl);
        }

        return $notification;
    }

    private static function sendEmail(int $userId, string $title, string $message, ?string $actionUrl): void
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return;
            }

            Mail::send('emails.notification', [
                'userName'  => $user->name,
                'title'     => $title,
                'message'   => $message,
                'actionUrl' => $actionUrl,
            ], function ($mail) use ($user, $title) {
                $mail->to($user->email, $user->name)
                     ->subject($title . ' - ServeMix');
            });
        } catch (\Exception $e) {
            // Don't break the main flow if email fails
            \Log::warning('Failed to send notification email: ' . $e->getMessage());
        }
    }
}
