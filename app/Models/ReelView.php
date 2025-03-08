<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'reel_id',
        'user_id',
        'ip_address',
        'device_info',
        'view_duration',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'view_duration' => 'float',
        'viewed_at' => 'datetime',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
