<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserSearchController\UserSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

Route::post('/broadcasting/auth', function (Request $request) {
    if (auth()->check()) {
        return Broadcast::auth($request);
    }
    return response('Unauthorized', 401);
})->middleware(['web', 'auth']);


Route::post('/logout', function (Request $request) {
    Auth::logout();
    return response()->json(['message' => 'Logged out']);
});

