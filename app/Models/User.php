<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'avatar',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    /**
     * Lấy tất cả các reel mà người dùng đã thích
     */
    public function likedReels()
    {
        return $this->belongsToMany(Reel::class, 'reel_likes')
            ->withTimestamps();
    }

    /**
     * Lấy tất cả bình luận của người dùng trên reels
     */
    public function reelComments()
    {
        return $this->hasMany(ReelComment::class);
    }

    /**
     * Lấy tất cả bộ sưu tập reel của người dùng
     */
    public function reelCollections()
    {
        return $this->hasMany(ReelCollection::class);
    }

    /**
     * Lấy tất cả âm thanh gốc do người dùng tạo ra
     */
    public function originalAudios()
    {
        return $this->hasMany(Audio::class, 'original_user_id');
    }

    /**
     * Lấy tất cả lượt xem reel của người dùng
     */
    public function reelViews()
    {
        return $this->hasMany(ReelView::class);
    }

    /**
     * Lấy tất cả lượt chia sẻ reel của người dùng
     */
    public function reelShares()
    {
        return $this->hasMany(ReelShare::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function allImages()
    {
        return $this->posts()->with('images')->get()->pluck('images')->flatten();
    }

    public function allVideos()
    {
        return $this->posts()->with('videos')->get()->pluck('videos')->flatten();
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id')
            ->withTimestamps();
    }

}
