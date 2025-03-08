<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

class ImageController extends Controller
{
    public function uploadMultiple($request)
    {
        try {
            // Cấu hình Cloudinary
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);


            $uploadApi = new UploadApi();
            $imageUrls = [];

            // Lặp qua từng file ảnh
            foreach ($request as $image) {
                $uploadResponse = $uploadApi->upload($image->getRealPath(), [
                    'folder' => 'post_images', // Thư mục trên Cloudinary
                    'format' => 'webp', // Chuyển sang WEBP
                    'transformation' => [
                        'width' => 1024,
                        'height' => 1024,
                        'crop' => 'limit',
                        'quality' => 'auto:good'
                    ]
                ]);

                // Lưu URL vào mảng
                $imageUrls[] = $uploadResponse['secure_url'];
            }

            return $imageUrls; // Trả về danh sách URL ảnh

        } catch (\Exception $e) {
            \Log::error('Lỗi upload: ' . $e->getMessage());
            return [];
        }
    }
}
