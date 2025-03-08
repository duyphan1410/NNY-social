<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelMedia extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'reel_id',
        'media_url',
        'media_type',
        'order_position',
        'width',
        'height',
        'thumbnail_url',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }
}
