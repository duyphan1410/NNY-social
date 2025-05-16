<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Post\LikeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Reel\ReelController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Events\NewNotificationEvent;

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

//Route Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.dashboard');
    Route::post('/users/{id}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('admin.users.toggleBan');

    // Quản lý bài đăng
    Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('admin.posts');
    Route::post('/posts/{id}/toggle-visibility', [\App\Http\Controllers\Admin\PostController::class, 'toggleVisibility'])->name('admin.posts.toggleVisibility');
    Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('admin.posts.destroy');
    //Thống kê
    Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('admin.statistics');
});



//Router bài đăng
Route::prefix('post')->name('post.')->group(function () {
    Route::middleware('auth')->group(function () {
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
    //Ds liked
    Route::get('/{post}/likes', [PostController::class, 'getLikes'])->name('likes');
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
        Route::post('/avatar/update', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/cover/update', [ProfileController::class, 'updateCover'])->name('cover.update');
    });

    // Trang profile của người khác
    Route::get('/{user}', [ProfileController::class, 'show'])->name('show');
    Route::get('/{user}/about', [ProfileController::class, 'about'])->name('about');
    Route::get('/{user}/photos', [ProfileController::class, 'photos'])->name('photos');
    Route::get('/{user}/videos', [ProfileController::class, 'videos'])->name('videos');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/account/settings', [\App\Http\Controllers\Account\SettingsController::class, 'edit'])->name('account.settings');
    Route::post('/account/settings', [\App\Http\Controllers\Account\SettingsController::class, 'update'])->name('account.settings.update');
});


Route::middleware(['auth'])->group(function() {
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    // Lấy tất cả thông báo
    Route::get('/notifications', [NotificationController::class, 'getAllNotifications']);
    // Xóa một thông báo
    Route::delete('/notifications/{id}', [NotificationController::class, 'deleteNotification']);

});


Route::get('/dashboard', function () {
    return view('dashboard.dashboard'); // Cần chỉ rõ thư mục
})->middleware(['auth', 'verified'])->name('dashboard');

Route::fallback(function () {
    return response()->json(['message' => 'Route không tồn tại'], 404);
});


require __DIR__.'/auth.php';

Auth::routes();

