<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép tất cả user gửi request này
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'cover' => ['nullable', 'string'], // nếu bạn dùng ảnh bìa (cover) là URL
        ];
    }
}
