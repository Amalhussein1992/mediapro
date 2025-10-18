<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const EXPO_PUSH_ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send a push notification to a user
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        ?array $data = null,
        string $priority = 'default'
    ): bool {
        if (!$user->expo_push_token) {
            Log::info("User {$user->id} has no push token");
            return false;
        }

        return $this->sendPushNotification(
            $user->expo_push_token,
            $title,
            $body,
            $data,
            $priority
        );
    }

    /**
     * Send a push notification to multiple users
     */
    public function sendToMultipleUsers(
        array $userIds,
        string $title,
        string $body,
        ?array $data = null,
        string $priority = 'default'
    ): array {
        $users = User::whereIn('id', $userIds)
            ->whereNotNull('expo_push_token')
            ->get();

        $results = [];
        foreach ($users as $user) {
            $results[$user->id] = $this->sendToUser($user, $title, $body, $data, $priority);
        }

        return $results;
    }

    /**
     * Send push notification via Expo Push API
     */
    private function sendPushNotification(
        string $pushToken,
        string $title,
        string $body,
        ?array $data = null,
        string $priority = 'default'
    ): bool {
        try {
            $message = [
                'to' => $pushToken,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'priority' => $priority,
            ];

            if ($data) {
                $message['data'] = $data;
            }

            $response = Http::post(self::EXPO_PUSH_ENDPOINT, $message);

            if ($response->successful()) {
                $result = $response->json();

                // Check for errors in the response
                if (isset($result['data'][0]['status']) && $result['data'][0]['status'] === 'error') {
                    $errorType = $result['data'][0]['details']['error'] ?? 'unknown';

                    // Handle DeviceNotRegistered error - clear the token
                    if ($errorType === 'DeviceNotRegistered') {
                        Log::warning("Push token is no longer valid: {$pushToken}");
                        // You might want to clear the token from the database here
                        return false;
                    }

                    Log::error("Push notification error: {$errorType}");
                    return false;
                }

                return true;
            }

            Log::error('Push notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Push notification exception', [
                'message' => $e->getMessage(),
                'token' => $pushToken,
            ]);

            return false;
        }
    }

    /**
     * Create a notification record and send push notification
     */
    public function createAndSend(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        string $priority = 'normal',
        ?string $actionUrl = null
    ): bool {
        // Create notification record in database
        $notification = Notification::createNotification(
            $userId,
            $type,
            $title,
            $message,
            $data,
            $priority,
            $actionUrl
        );

        // Send push notification to user's device
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Update last notification timestamp
        $user->update(['last_notification_at' => now()]);

        // Map priority to Expo priority format
        $expoPriority = match($priority) {
            'urgent', 'high' => 'high',
            'low' => 'low',
            default => 'default',
        };

        return $this->sendToUser(
            $user,
            $title,
            $message,
            array_merge($data ?? [], [
                'notification_id' => $notification->id,
                'type' => $type,
                'action_url' => $actionUrl,
            ]),
            $expoPriority
        );
    }
}
