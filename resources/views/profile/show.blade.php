@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-8 p-4 bg-white rounded-xl shadow">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar }}" class="w-20 h-20 rounded-full" alt="{{ $user->first_name }} {{ $user->last_name }}">
            <div>
                <h2 class="text-xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>

        @if($user->detail)
            <div class="mt-4 space-y-1 text-gray-700">
                <p><strong>Giới thiệu:</strong> {{ $user->detail->bio ?? 'Không có' }}</p>
                <p><strong>Địa điểm:</strong> {{ $user->detail->location ?? 'Không rõ' }}</p>
                <p><strong>Ngày sinh:</strong> {{ $user->detail->birthday ?? 'Chưa cung cấp' }}</p>
                <p><strong>Giới tính:</strong> {{ $user->detail->gender_label ?? 'Không rõ' }}</p>
            </div>
        @endif
    </div>
@endsection
