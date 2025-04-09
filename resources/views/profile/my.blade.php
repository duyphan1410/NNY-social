@extends('layouts.app')

@section('content')
    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            Hồ sơ đã được cập nhật thành công!
        </div>
    @endif
    <div class="max-w-3xl mx-auto mt-8 p-4 bg-white rounded-xl shadow">
        <h1 class="text-xl font-bold mb-4">Hồ sơ của tôi</h1>

        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar }}" class="w-20 h-20 rounded-full" alt="{{ $user->first_name }} {{ $user->last_name }}">
            <div>
                <h2 class="text-lg font-semibold">{{ $user->first_name }} {{ $user->last_name }}</h2>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
            </div>
        </div>

        @if($user->detail)
            <div class="mt-4 space-y-1 text-gray-700">
                <p><strong>Giới thiệu:</strong> {{ $user->detail->bio ?? 'Chưa cập nhật' }}</p>
                <p><strong>Địa điểm:</strong> {{ $user->detail->location ?? 'Chưa cập nhật' }}</p>
                <p><strong>Ngày sinh:</strong> {{ $user->detail->birthday ?? 'Chưa cập nhật' }}</p>
                <p><strong>Giới tính:</strong> {{ $user->detail->gender_label ?? 'Chưa cập nhật' }}</p>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Chỉnh sửa hồ sơ</a>
        </div>
    </div>
@endsection
