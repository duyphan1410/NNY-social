<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments'; // Chỉ định bảng trong database
    protected $fillable = ['user_id', 'post_id', 'content','parent_comment_id']; // Các cột có thể được gán dữ liệu

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getParsedContentAttribute()
    {
        return preg_replace(
            '/@\[(.*?)\]\(user:\d+\)/',
            '<a href="/profile/$2" class="mention">@$1</a>',
            e($this->content)
        );
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }


}
