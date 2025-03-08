<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class VideoController extends Controller
{
    public function uploadVideo(Request $request)
    {
        try {
            $config = Configuration::instance([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            $cloudinary = new Cloudinary($config);

            $uploadedVideo = $request->file('video');
            $uploadResponse = $cloudinary->uploadApi()->upload(
                $uploadedVideo->getRealPath(),
                [
                    'folder' => 'videos',
                    'resource_type' => 'video' // Quan trọng: chỉ định là upload video
                ]
            );

            // Lấy URL an toàn của video
            $videoUrl = $uploadResponse['secure_url'];

            return redirect()->back()
                ->with('success', 'Upload video thành công')
                ->with('video_url', $videoUrl);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

}
