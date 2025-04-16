@extends('layouts.app')

@push('styles')
    @vite(['resources/css/profile.css'])
    @vite(['resources/css/home.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
@endpush

@section('content')

    @php
        $isOwner = auth()->id() === $user->id;
    @endphp
    <div class="container max-w-5xl mx-auto mt-6">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @include('profile.header', ['user' => $user, 'isOwner' => auth()->id() === $user->id])
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-8 py-6">
                <div class="space-y-6">
                    @include('profile.sidebar-info', ['user' => $user])
                    @include('profile.components.album', ['user' => $user])
                </div>
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Thông tin chi tiết</h2>
                        {{-- Hiển thị thông tin chi tiết về người dùng --}}
                        <p><strong>Bio:</strong> {{ $user->detail->bio ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Vị trí:</strong> {{ $user->detail->location ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Ngày sinh:</strong> {{ $user->detail->birthday ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Giới tính:</strong> {{ $user->detail->gender ? (['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'][$user->detail->gender] ?? 'Chưa cập nhật') : 'Chưa cập nhật' }}</p>
                    </div>
                    @if ($isOwner)
                        <div class="bg-white shadow rounded-lg p-6 mt-6">
                            <h2 class="text-xl font-semibold mb-4">Chỉnh sửa thông tin cá nhân</h2>
    {{--                        @include('profile.edit-form') --}}{{-- Nếu bạn có form chỉnh sửa riêng --}}
                            {{-- Hoặc bạn có thể đặt form chỉnh sửa trực tiếp ở đây --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
