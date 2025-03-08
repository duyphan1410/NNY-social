<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'duration',
        'file_url',
        'is_original',
        'original_user_id',
    ];

    protected $casts = [
        'is_original' => 'boolean',
        'duration' => 'float',
    ];

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }
}
