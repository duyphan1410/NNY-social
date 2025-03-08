<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reel_id',
        'reporter_id',
        'reason',
        'description',
        'status',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
