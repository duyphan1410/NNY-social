<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments'; // Chỉ định bảng trong database
    protected $fillable = ['user_id', 'post_id', 'content']; // Các cột có thể được gán dữ liệu

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
}
