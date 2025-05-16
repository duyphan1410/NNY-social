<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Lấy số lượng thông báo chưa đọc của người dùng hiện tại
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Đánh dấu một thông báo đã đọc
     */
    public function markAsRead($id) {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification && !$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }
        return response()->json(['success' => true]);
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Lấy danh sách thông báo của người dùng
     */
    public function getNotifications()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }
}
