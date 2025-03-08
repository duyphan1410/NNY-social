<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

class ImageController extends Controller
{
    public function uploadMultiple(Request $request)
    {
        try {
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            if (!$request->hasFile('images')) {
                return [];
            }
            $uploadApi = new UploadApi();
            $imageUrls = [];

            foreach ($request->file('images') as $image) {
                $uploadResponse = $uploadApi->upload($image->getRealPath(), [
                    'folder' => 'post_images',
                    'format' => 'webp',
                    'transformation' => [
                        'width' => 1024,
                        'height' => 1024,
                        'crop' => 'limit',
                        'quality' => 'auto:good'
                    ]
                ]);

                // Kiểm tra URL trước khi lưu
                $imageUrls[] = $uploadResponse['secure_url'];
                \Log::info('Upload thành công: ' . $uploadResponse['secure_url']);
            }

            // Kiểm tra xem có lưu đúng nhiều URL không
            \Log::info('Danh sách URL ảnh: ', $imageUrls);

            return $imageUrls;
        } catch (\Exception $e) {
            \Log::error('Lỗi upload: ' . $e->getMessage());
            return ['error' => 'Lỗi khi upload: ' . $e->getMessage()];
        }
    }

}
