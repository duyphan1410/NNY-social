<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cover_img_url', // Đã sửa thành cover_img_url để nhất quán với database (nếu bạn dùng tên đó)
        'bio',
        'location',
        'birthdate', // Đã sửa thành birthdate để nhất quán với tên thường dùng
        'gender',
        'website', // Thêm trường website (nếu bạn đã thêm vào schema)
        'relationship_status',
        'hobbies',
        'social_links',
    ];

    protected $dates = ['birthdate']; // Để Laravel tự động xử lý định dạng ngày tháng

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGenderLabelAttribute()
    {
        return match ($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => 'Chưa cập nhật',
        };
    }
}
