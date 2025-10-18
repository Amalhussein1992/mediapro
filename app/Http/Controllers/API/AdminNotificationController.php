<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminNotificationController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'nullable|string',
                'priority' => 'nullable|string|in:low,normal,high,urgent',
                'action_url' => 'nullable|string',
                'data' => 'nullable|array',
            ]);

            $success = $this->pushService->createAndSend(
                $validated['user_id'],
                $validated['type'] ?? 'admin_message',
                $validated['title'],
                $validated['message'],
                $validated['data'] ?? null,
                $validated['priority'] ?? 'normal',
                $validated['action_url'] ?? null
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification (user may not have push token)',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'nullable|string',
                'priority' => 'nullable|string|in:low,normal,high,urgent',
                'action_url' => 'nullable|string',
                'data' => 'nullable|array',
            ]);

            $successCount = 0;
            $failCount = 0;

            foreach ($validated['user_ids'] as $userId) {
                $success = $this->pushService->createAndSend(
                    $userId,
                    $validated['type'] ?? 'admin_message',
                    $validated['title'],
                    $validated['message'],
                    $validated['data'] ?? null,
                    $validated['priority'] ?? 'normal',
                    $validated['action_url'] ?? null
                );

                if ($success) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sent to {$successCount} users, failed for {$failCount} users",
                'sent_count' => $successCount,
                'failed_count' => $failCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'nullable|string',
                'priority' => 'nullable|string|in:low,normal,high,urgent',
                'action_url' => 'nullable|string',
                'data' => 'nullable|array',
                'only_active' => 'nullable|boolean', // Only send to users who logged in recently
            ]);

            $query = User::whereNotNull('expo_push_token');

            // Optionally filter only active users (logged in within last 30 days)
            if ($validated['only_active'] ?? false) {
                $query->where('last_notification_at', '>=', now()->subDays(30));
            }

            $users = $query->get();
            $userIds = $users->pluck('id')->toArray();

            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users with push tokens found',
                ], 404);
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($userIds as $userId) {
                $success = $this->pushService->createAndSend(
                    $userId,
                    $validated['type'] ?? 'broadcast',
                    $validated['title'],
                    $validated['message'],
                    $validated['data'] ?? null,
                    $validated['priority'] ?? 'normal',
                    $validated['action_url'] ?? null
                );

                if ($success) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Broadcast sent to {$successCount} users, failed for {$failCount} users",
                'total_users' => count($userIds),
                'sent_count' => $successCount,
                'failed_count' => $failCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_notifications' => Notification::count(),
                'unread_notifications' => Notification::where('is_read', false)->count(),
                'notifications_today' => Notification::whereDate('created_at', today())->count(),
                'notifications_this_week' => Notification::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->count(),
                'users_with_push_tokens' => User::whereNotNull('expo_push_token')->count(),
                'total_users' => User::count(),
                'recent_notifications' => Notification::with('user:id,name,email')
                    ->latest()
                    ->limit(10)
                    ->get(),
                'notifications_by_type' => Notification::selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all notifications (admin view)
     */
    public function getAllNotifications(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $type = $request->input('type');
            $userId = $request->input('user_id');

            $query = Notification::with('user:id,name,email');

            if ($type) {
                $query->where('type', $type);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
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
     * Delete notification (admin)
     */
    public function deleteNotification(int $id): JsonResponse
    {
        try {
            $notification = Notification::findOrFail($id);
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
     * Get predefined notification templates
     */
    public function getTemplates(): JsonResponse
    {
        $templates = [
            [
                'id' => 'welcome',
                'name' => 'رسالة ترحيب - Welcome Message',
                'title' => 'مرحباً بك! - Welcome!',
                'message' => 'نحن سعداء بانضمامك إلينا! - We are happy to have you join us!',
                'type' => 'welcome',
                'priority' => 'normal',
            ],
            [
                'id' => 'update',
                'name' => 'تحديث التطبيق - App Update',
                'title' => 'تحديث جديد متاح - New Update Available',
                'message' => 'إصدار جديد من التطبيق متاح الآن! - A new version of the app is now available!',
                'type' => 'update',
                'priority' => 'high',
            ],
            [
                'id' => 'maintenance',
                'name' => 'صيانة - Maintenance',
                'title' => 'صيانة مجدولة - Scheduled Maintenance',
                'message' => 'سيكون التطبيق تحت الصيانة قريباً - The app will be under maintenance soon',
                'type' => 'maintenance',
                'priority' => 'urgent',
            ],
            [
                'id' => 'promotion',
                'name' => 'عرض ترويجي - Promotion',
                'title' => 'عرض خاص! - Special Offer!',
                'message' => 'لا تفوت العرض الخاص لفترة محدودة! - Don\'t miss our limited time special offer!',
                'type' => 'promotion',
                'priority' => 'normal',
            ],
            [
                'id' => 'reminder',
                'name' => 'تذكير - Reminder',
                'title' => 'تذكير - Reminder',
                'message' => 'لديك مهام معلقة - You have pending tasks',
                'type' => 'reminder',
                'priority' => 'normal',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $templates,
        ], 200);
    }
}
