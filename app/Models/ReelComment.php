<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reel_id',
        'user_id',
        'content',
        'parent_id',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ReelComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ReelComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'reel_comment_likes', 'comment_id', 'user_id')
            ->withTimestamps();
    }
}
