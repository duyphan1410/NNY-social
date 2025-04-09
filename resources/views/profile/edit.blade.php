@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-8 p-6 bg-white rounded-xl shadow">
        <h1 class="text-lg font-bold mb-4">Chỉnh sửa hồ sơ</h1>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="first_name" class="block font-semibold">Họ</label>
                <input type="text" name="first_name" id="first_name"
                       value="{{ old('first_name', $user->first_name) }}"
                       class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label for="last_name" class="block font-semibold">Tên</label>
                <input type="text" name="last_name" id="last_name"
                       value="{{ old('last_name', $user->last_name) }}"
                       class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Giới thiệu</label>
                <textarea name="bio" class="w-full border px-3 py-2 rounded">{{ old('bio', $user->detail->bio ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Địa điểm</label>
                <input type="text" name="location" value="{{ old('location', $user->detail->location ?? '') }}" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Ngày sinh</label>
                <input type="date" name="birthday" value="{{ old('birthday', $user->detail->birthday ?? '') }}" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Giới tính</label>
                <select name="gender" class="w-full border px-3 py-2 rounded">
                    <option value="">-- Chọn --</option>
                    <option value="male" {{ old('gender', $user->detail->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender', $user->detail->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ old('gender', $user->detail->gender ?? '') == 'other' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection
