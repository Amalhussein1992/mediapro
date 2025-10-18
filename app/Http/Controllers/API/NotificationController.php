<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $unreadOnly = $request->input('unread_only', false);

            $query = Notification::where('user_id', auth()->id());

            if ($unreadOnly) {
                $query->unread();
            }

            $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::where('user_id', auth()->id())->unread()->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $count = Notification::where('user_id', auth()->id())->unread()->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread count',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $notification = Notification::where('user_id', auth()->id())
                ->findOrFail($id);

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            Notification::where('user_id', auth()->id())
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $notification = Notification::where('user_id', auth()->id())
                ->findOrFail($id);

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all read notifications
     */
    public function clearRead(): JsonResponse
    {
        try {
            Notification::where('user_id', auth()->id())
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Read notifications cleared successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear read notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register/Update push token for the authenticated user
     */
    public function registerPushToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'push_token' => 'required|string',
                'device_type' => 'nullable|string|in:ios,android,web',
            ]);

            $user = auth()->user();
            $user->update([
                'expo_push_token' => $validated['push_token'],
                'device_type' => $validated['device_type'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push token registered successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register push token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove push token (e.g., on logout)
     */
    public function removePushToken(): JsonResponse
    {
        try {
            $user = auth()->user();
            $user->update([
                'expo_push_token' => null,
                'device_type' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push token removed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove push token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a test push notification to the authenticated user
     */
    public function sendTestNotification(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->expo_push_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No push token registered for this user',
                ], 400);
            }

            $success = $this->pushService->createAndSend(
                $user->id,
                'test',
                '🎉 Test Notification',
                'This is a test notification from your Social Media Manager app!',
                ['test' => true],
                'normal'
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
