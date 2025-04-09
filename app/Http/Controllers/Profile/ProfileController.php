<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Xem hồ sơ của chính mình
     */
    public function myProfile(Request $request): View
    {
        $user = $request->user()->load('detail');
        return view('profile.my', compact('user'));
    }

    /**
     * Xem hồ sơ của người khác (bằng username hoặc id)
     */
    public function show($id): View
    {
        $user = User::with('detail')->where('id', $id)->firstOrFail();
        return view('profile.show', compact('user'));
    }

    /**
     * Trang chỉnh sửa hồ sơ
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('detail');
        return view('profile.edit', compact('user'));
    }

    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Cập nhật các trường cơ bản của user
        $user->fill($request->only(['first_name', 'last_name', 'email']));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Cập nhật user_detail
        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['bio', 'location', 'birthday', 'gender', 'cover']) // tùy form
        );

        return Redirect::route('profile.me')->with('status', 'profile-updated');
    }
}
