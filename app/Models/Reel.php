<?php
// app/Models/Reel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'caption', 'duration', 'status', 'is_public',
        'views_count', 'likes_count', 'comments_count', 'shares_count',
        'audio_id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'duration' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(ReelMedia::class);
    }

    public function audio()
    {
        return $this->belongsTo(Audio::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'reel_likes')
            ->withTimestamps();
    }

    public function views()
    {
        return $this->hasMany(ReelView::class);
    }

    public function shares()
    {
        return $this->hasMany(ReelShare::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'reel_hashtags')
            ->withTimestamps();
    }

    public function collections()
    {
        return $this->belongsToMany(ReelCollection::class, 'collection_reels', 'reel_id', 'collection_id')
            ->withPivot('added_at');
    }

    public function reports()
    {
        return $this->hasMany(ReelReport::class);
    }
}
