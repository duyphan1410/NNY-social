<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelShare extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'reel_id',
        'user_id',
        'share_type',
        'shared_to_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharedToUser()
    {
        return $this->belongsTo(User::class, 'shared_to_user_id');
    }
}
