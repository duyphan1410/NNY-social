<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = ['post_id', 'image_url', 'public_id'];

    // Relationship với Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
