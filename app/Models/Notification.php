<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'message',
        'url',
        'type',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Kiểm tra xem thông báo đã đọc chưa
     *
     * @return bool
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    /**
     * Đánh dấu thông báo đã đọc
     *
     * @return bool
     */
    public function markAsRead()
    {
        if ($this->read_at === null) {
            $this->read_at = Carbon::now();
            return $this->save();
        }

        return true;
    }

    /**
     * Tạo thông báo mới
     *
     * @param int $userId
     * @param string $message
     * @param string $url
     * @param array $data
     * @return Notification
     */
    public static function createNotification($userId, $message, $url = null, $type = null)
    {
        return self::create([
            'user_id' => $userId,
            'message' => $message,
            'url' => $url,
            'type' => null,
            'read_at' => null,
        ]);
    }


    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
