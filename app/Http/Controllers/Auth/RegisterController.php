<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:8', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Avatar không bắt buộc
        ]);
    }
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => 'https://res.cloudinary.com/dwvt3snha/image/upload/v1743073158/post_images/nem5i8act9bl7bypckmm.webp',
        ]);

        // Tạo một user_detail mới và liên kết với user vừa tạo
        $user->detail()->create([
            'cover_img_url' => null,
            'bio' => null,
            'location' => null,
            'birthdate' => null,
            'gender' => null,
            'website' => null,
            'relationship_status' => null,
            'hobbies' => null,
            'social_links' => null,
        ]);

        return $user;
    }
}
