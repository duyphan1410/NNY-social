<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Các thuộc tính có thể gán hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'message',
        'url',
        'type',
        'data',
        'reference_id',
        'read_at',
        'processed_at',
        'viewed_at',
        'view_duration'
    ];

    /**
     * Các thuộc tính nên được ép kiểu
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
        'processed_at' => 'datetime',
        'viewed_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Các thuộc tính mặc định
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'info',
    ];

    /**
     * Các thuộc tính được thêm vào kết quả của model
     *
     * @var array
     */
    protected $appends = ['is_read', 'is_processed'];

    /**
     * Get user that owns this notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra nếu thông báo đã được đọc
     *
     * @return bool
     */
    public function getIsReadAttribute()
    {
        return $this->read_at !== null;
    }

    /**
     * Kiểm tra nếu thông báo đã được xử lý
     *
     * @return bool
     */
    public function getIsProcessedAttribute()
    {
        return $this->processed_at !== null;
    }

    /**
     * Scope một truy vấn để chỉ lấy các thông báo chưa đọc.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope một truy vấn để chỉ lấy các thông báo đã đọc.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope một truy vấn để chỉ lấy các thông báo chưa xử lý.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }

    /**
     * Scope một truy vấn để chỉ lấy các thông báo đã xử lý.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Scope một truy vấn để lấy thông báo theo loại.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Đánh dấu thông báo là đã đọc.
     *
     * @return bool
     */
    public function markAsRead()
    {
        if ($this->read_at === null) {
            return $this->update(['read_at' => now()]);
        }

        return true;
    }

    /**
     * Đánh dấu thông báo là đã xử lý.
     *
     * @return bool
     */
    public function markAsProcessed()
    {
        if ($this->processed_at === null) {
            return $this->update([
                'processed_at' => now(),
                'read_at' => $this->read_at ?? now() // Cũng đánh dấu là đã đọc nếu chưa đọc
            ]);
        }

        return true;
    }
}
