<?php

use App\Http\Controllers\UserSearchController\UserSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users/search', [UserSearchController::class, 'search']);

Route::post('/cloudinary/delete', function (Request $request) {
    $publicId = $request->input('public_id');
    $resourceType = $request->input('resource_type', 'image'); // hoặc 'video'

    if (!$publicId) {
        return response()->json(['error' => 'Thiếu public_id'], 400);
    }

    $cloudName = config('services.cloudinary.cloud_name');
    $apiKey = config('services.cloudinary.api_key');
    $apiSecret = config('services.cloudinary.api_secret');
    $timestamp = time();

    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($stringToSign);

    $response = Http::asForm()->post("https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/destroy", [
        'public_id' => $publicId,
        'timestamp' => $timestamp,
        'api_key' => $apiKey,
        'signature' => $signature
    ]);

    return response()->json($response->json());
});
