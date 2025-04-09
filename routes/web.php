<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Post\LikeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Reel\ReelController;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ImageController;
use App\Http\Controllers\VideoController;

// Route login
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('/login', [LoginController::class, 'login']);
// Route logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Router Register
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);


//Router bài đăng
Route::middleware('auth')->group(function () {
    Route::prefix('post')->name('post.')->group(function () {
        // Tạo bài đăng mới
        Route::get('/create', [PostController::class, 'create'])->name('create');
        // Lưu bài đăng mới
        Route::post('/', [PostController::class, 'store'])->name('store');
        // Hiển thị chi tiết bài đăng
        Route::get('/{id}/detail', [PostController::class, 'show'])->name('show');
        // Hiển thị form chỉnh sửa bài đăng
        Route::get('/{id}/edit', [PostController::class, 'edit'])->name('edit');
        // Cập nhật bài đăng
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        // Xóa bài đăng
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        //Share bài đăng
        Route::post('/{id}/share', [PostController::class, 'share'])->name('share');
        Route::get('/{id}/share', [PostController::class, 'shareForm'])->name('share.form');
        //Like và comment
        Route::post('/{id}/like', [LikeController::class, 'toggleLike'])->name('like');
        Route::post('/{id}/comment', [CommentController::class, 'store'])->name('comment.store');
        //Xóa bình luận
        Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');


    });
});
//Router Reel
Route::middleware('auth')->group(function () {
    Route::prefix('reel')->name('reel.')->group(function () {
        // Tạo reel mới
        Route::get('/create', [ReelController::class, 'create'])->name('create');
        Route::post('/store', [ReelController::class, 'store'])->name('store');
    });
});
// Router bạn bè
Route::middleware('auth')->group(function () {
    Route::prefix('friend')->name('friend.')->group(function () {
        Route::get('/', [FriendController::class, 'index'])->name('index');
        //Thêm bạn
        Route::post('/request', [FriendController::class, 'sendRequest'])->name('request');
        //Hủy kết bạn
        Route::post('/cancel-request', [FriendController::class, 'cancelRequest'])->name('cancelRequest');
        //Chấp nhận
        Route::post('/accept', [FriendController::class, 'acceptRequest'])->name('accept');
        //Từ chối
        Route::post('/reject', [FriendController::class, 'rejectRequest'])->name('reject');
        //Xóa bạn
        Route::post('/unfriend', [FriendController::class, 'unfriend'])->name('unfriend');
        //Tạo ds
        Route::get('/list', [FriendController::class, 'listFriends'])->name('list');
        //Tìm kiếm (bạn)
        Route::get('/search', [FriendController::class, 'search'])->name('search');

    });
});

//Route người dùng
Route::prefix('profile')->name('profile.')->group(function () {

    // Trang profile của chính mình (yêu cầu đăng nhập)
    Route::middleware('auth')->group(function () {
        Route::get('/me', [ProfileController::class, 'myProfile'])->name('me');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Trang profile của người khác
    Route::get('/{id}', [ProfileController::class, 'show'])->name('show');
});


Route::get('/dashboard', function () {
    return view('dashboard.dashboard'); // Cần chỉ rõ thư mục
})->middleware(['auth', 'verified'])->name('dashboard');

Route::fallback(function () {
    return response()->json(['message' => 'Route không tồn tại'], 404);
});




require __DIR__.'/auth.php';

Auth::routes();

