<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NewNotificationEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $data;
    public $userId;

    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;

        try {
            // Lưu thông báo vào database
            $notification = Notification::create([
                'user_id' => $userId,
                'message' => $data['message'],
                'url' => $data['url'] ?? null,
                'type' => $data['type'] ?? 'info',
                'read_at' => null, // Rõ ràng đánh dấu là chưa đọc
            ]);

            // Thêm ID của thông báo vào dữ liệu được gửi qua websocket
            $this->data['id'] = $notification->id;

            Log::info("Đã tạo thông báo mới ID: {$notification->id} cho user ID: {$userId}");
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo thông báo: ' . $e->getMessage());
        }
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->data['id'] ?? null,
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? null,
            'type' => $this->data['type'] ?? 'info',
        ];
    }


    public function broadcastAs()
    {
        return 'notification.received';
    }

}
