<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUnread()
    {
        $user = Auth::user();

        $notifications = $user
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

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();

        $notification = Notification::find($notificationId);

        if (!$notification || $notification->notifiable_id !== $user->id || $notification->notifiable_type !== get_class($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
