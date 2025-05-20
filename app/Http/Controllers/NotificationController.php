<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Đã xóa thông báo.');
        }

        return back()->with('false', 'Xóa không thành công.');
    }

    public function getUnread()
    {
        $unread = auth()->user()->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // hoặc lấy toàn bộ: ->get();

        return response()->json(['notifications' => $unread]);
    }
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
    public function getAllNotifications(Request $request)
    {
        $query = auth()->user()->notifications(); // Lấy tất cả thông báo của người dùng hiện tại

        // Kiểm tra nếu có tham số 'filter' và giá trị là 'unread'
        if ($request->has('filter') && $request->get('filter') == 'unread') {
            $query->whereNull('read_at'); // Lọc những thông báo chưa được đọc (cột 'read_at' là NULL)
        }

        $notifications = $query->latest()->paginate(10); // Sắp xếp theo thời gian mới nhất và phân trang

        return view('notifications.index', compact('notifications'));
    }
}
