<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class HospitalNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:hospital_admin');
    }

    /**
     * Get unread notifications for the hospital admin
     */
    public function getUnread()
    {
        $hospitalAdmin = Auth::guard('hospital_admin')->user();

        $notifications = $hospitalAdmin
            ->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? $notification->type,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                ];
            });

        $unreadCount = $hospitalAdmin->notifications()->whereNull('read_at')->count();

        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId)
    {
        $hospitalAdmin = Auth::guard('hospital_admin')->user();

        $notification = Notification::find($notificationId);

        if (!$notification || $notification->notifiable_id !== $hospitalAdmin->id || $notification->notifiable_type !== get_class($hospitalAdmin)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $hospitalAdmin = Auth::guard('hospital_admin')->user();

        $hospitalAdmin->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        $hospitalAdmin = Auth::guard('hospital_admin')->user();

        $hospitalAdmin->notifications()->delete();

        return response()->json(['success' => true]);
    }
}
