<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'use_count',
    ];

    public function reels()
    {
        return $this->belongsToMany(Reel::class, 'reel_hashtags')
            ->withTimestamps();
    }
}
