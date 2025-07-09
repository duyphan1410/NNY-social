<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;
use Cloudinary\Configuration\Configuration;

class ImageController extends Controller
{
    protected $uploadApi;

    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
        ]);
        $this->uploadApi = new UploadApi();
    }

    public function uploadMultiple($files)
    {
        try {
            Log::info('Starting uploadMultiple', ['files' => $files]);
            $imageUrls = [];
            foreach ($files as $image) {
                $uploadResponse = $this->uploadApi->upload($image->getRealPath(), [
                    'folder' => 'post_images',
                    'format' => 'webp',
                    'transformation' => [
                        'width' => 1024,
                        'height' => 1024,
                        'crop' => 'limit',
                        'quality' => 'auto:good'
                    ]
                ]);
                $imageItems[] = [
                    'url' => $uploadResponse['secure_url'],
                    'public_id' => $uploadResponse['public_id'],
                ];

                Log::info('Upload successful (multiple)', ['url' => $uploadResponse['secure_url'], 'originalName' => $image->getClientOriginalName()]);
            }
            Log::info('uploadMultiple returning URLs:', ['imageUrls' => $imageUrls]);
            return $imageItems;
        } catch (\Exception $e) {
            \Log::error('Error in uploadMultiple:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Upload failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadAvatar($file)
    {
        try {
            Log::info('Starting uploadAvatar', ['file' => $file]);
            $uploadResponse = $this->uploadApi->upload($file->getRealPath(), [
                'folder' => 'avatars',
                'format' => 'webp',
                'transformation' => [
                    'width' => 500,
                    'height' => 500,
                    'crop' => 'fill',
                    'gravity' => 'face',
                    'quality' => 'auto:good'
                ]
            ]);
            $imageUrl = $uploadResponse['secure_url'];
            Log::info('Upload successful (avatar):', ['url' => $imageUrl, 'originalName' => $file->getClientOriginalName()]);
            return [
                'url' => $imageUrl,
                'public_id' => $uploadResponse['public_id'],
            ];
            // Trả về một mảng chứa URL duy nhất để nhất quán
        } catch (\Exception $e) {
            \Log::error('Error in uploadAvatar:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Upload failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadCover($file)
    {
        try {
            Log::info('Starting uploadCover', ['file' => $file]);
            $uploadResponse = $this->uploadApi->upload($file->getRealPath(), [
                'folder' => 'cover_photos', // Thư mục riêng cho ảnh bìa
                'format' => 'webp',
                'transformation' => [
                    'width' => 1920, // Kích thước phổ biến cho ảnh bìa
                    'height' => 400,
                    'crop' => 'fill',
                    'gravity' => 'auto',
                    'quality' => 'auto:good'
                ]
            ]);
            $coverUrl = $uploadResponse['secure_url'];
            Log::info('Upload successful (cover):', ['url' => $coverUrl, 'originalName' => $file->getClientOriginalName()]);
            return [
                'url' => $coverUrl,
                'public_id' => $uploadResponse['public_id'],
            ];

        } catch (\Exception $e) {
            \Log::error('Error in uploadCover:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Upload failed', 'message' => $e->getMessage()], 500);
        }
    }
}
