<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Like;
use App\Models\Comment;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'content'];
    protected $withCount = ['likes', 'comments'];

    // Relationship với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship với PostImages
    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    // Relationship với PostVideos
    public function videos()
    {
        return $this->hasMany(PostVideo::class);
    }

    // Thêm relationship với Comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Thêm relationship với Likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Kiểm tra xem người dùng hiện tại đã like bài viết chưa
    public function isLikedByUser($userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $this->likes()->where('user_id', $userId)->exists();
    }

    //để lấy số lượng likes
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    // để lấy số lượng comments
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function scopeWithEngagement($query)
    {
        return $query->withCount(['likes', 'comments'])
            ->with(['user', 'images', 'videos']);
    }
}
