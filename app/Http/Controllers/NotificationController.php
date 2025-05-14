<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\FriendRequest;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Lấy số lượng thông báo chưa đọc của người dùng
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Đánh dấu một thông báo đã được đọc
     *
     * @param int $id ID của thông báo
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);

            // Chỉ cập nhật nếu chưa đọc
            if ($notification->read_at === null) {
                $notification->update(['read_at' => now()]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi đánh dấu thông báo đã đọc: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể đánh dấu thông báo đã đọc'], 500);
        }
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng là đã đọc
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            auth()->user()->notifications()
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi đánh dấu tất cả thông báo đã đọc: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể đánh dấu tất cả thông báo đã đọc'], 500);
        }
    }

    /**
     * Xử lý một thông báo dựa vào loại của nó
     *
     * @param int $id ID của thông báo
     * @return \Illuminate\Http\JsonResponse
     */
    public function processNotification($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);

            // Kiểm tra xem thông báo đã được xử lý chưa
            if ($notification->processed_at !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thông báo này đã được xử lý trước đó'
                ]);
            }

            // Lấy dữ liệu thông báo
            $data = json_decode($notification->data, true) ?? [];
            $type = $notification->type ?? ($data['type'] ?? 'default');
            $referenceId = $data['reference_id'] ?? null;

            // Xử lý dựa trên loại thông báo
            switch ($type) {
                case 'friend_request':
                    // Xử lý yêu cầu kết bạn
                    if ($referenceId) {
                        $friendRequest = FriendRequest::find($referenceId);
                        if ($friendRequest) {
                            // Chấp nhận yêu cầu kết bạn
                            $friendRequest->update(['status' => 'accepted']);

                            // Có thể thêm logic để tạo mối quan hệ bạn bè ở đây
                            // ...

                            Log::info("Đã chấp nhận yêu cầu kết bạn ID: {$referenceId}");
                        }
                    }
                    break;

                case 'new_message':
                    // Xử lý tin nhắn mới
                    if ($referenceId) {
                        $message = Message::find($referenceId);
                        if ($message) {
                            // Đánh dấu tin nhắn đã đọc
                            $message->update(['read_at' => now()]);
                            Log::info("Đã đánh dấu tin nhắn ID: {$referenceId} đã đọc");
                        }
                    }
                    break;

                case 'system_alert':
                    // Xử lý cảnh báo hệ thống
                    Log::info("Đã xác nhận cảnh báo hệ thống ID: {$notification->id}");
                    break;

                case 'comment':
                    // Xử lý thông báo bình luận
                    if ($referenceId) {
                        // Có thể chuyển hướng người dùng đến bình luận hoặc đánh dấu bình luận là đã xem
                        Log::info("Đã xử lý thông báo bình luận ID: {$referenceId}");
                    }
                    break;

                case 'like':
                    // Xử lý thông báo like
                    Log::info("Đã xử lý thông báo like ID: {$notification->id}");
                    break;

                case 'task_assigned':
                    // Xử lý phân công công việc
                    if ($referenceId) {
                        // Đánh dấu đã chấp nhận công việc
                        // $task = Task::find($referenceId);
                        // if ($task) {
                        //     $task->update(['status' => 'in_progress']);
                        // }
                        Log::info("Đã chấp nhận công việc ID: {$referenceId}");
                    }
                    break;

                default:
                    // Xử lý mặc định cho các loại thông báo khác
                    Log::info("Đã xử lý thông báo không xác định ID: {$notification->id}");
                    break;
            }

            // Cập nhật trạng thái đã xử lý cho thông báo
            $notification->update([
                'processed_at' => now(),
                'read_at' => now() // Cũng đánh dấu là đã đọc
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được xử lý thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý thông báo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể xử lý thông báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách tất cả thông báo của người dùng
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllNotifications(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $notifications = auth()->user()->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách thông báo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách thông báo'
            ], 500);
        }
    }

    /**
     * Xóa một thông báo
     *
     * @param int $id ID của thông báo
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNotification($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông báo thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa thông báo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa thông báo'
            ], 500);
        }
    }

    /**
     * Cập nhật thời gian đọc thông báo
     *
     * @param Request $request Dữ liệu request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackViewTime(Request $request)
    {
        try {
            $notificationId = $request->input('notification_id');
            $viewDuration = $request->input('view_duration'); // Thời gian xem tính bằng giây

            $notification = auth()->user()->notifications()->findOrFail($notificationId);
            $notification->update([
                'view_duration' => $viewDuration,
                'viewed_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật thời gian xem thông báo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật thời gian xem'
            ], 500);
        }
    }
}
