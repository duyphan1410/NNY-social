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
    <div class="container max-w-5xl mx-auto">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @include('profile.header', ['user' => $user, 'isOwner' => auth()->id() === $user->id])
            <!-- Nội dung chia 2 cột -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-8 py-6">
                <!-- Cột trái -->
                <div class="space-y-6">
                    <!-- Thông tin cá nhân -->
                    @include('profile.sidebar-info', ['user' => $user])
                    <!-- Album ảnh/video -->
                    @include('profile.components.album', ['user' => $user])
                </div>
                <!-- Cột phải (bài viết) -->
                <div class="md:col-span-2 space-y-6">
                    {{-- Danh sách bài viết --}}
                    @forelse ($posts as $post)
                        @include('post.card', ['post' => $post])
                    @empty
                        <div class="bg-white shadow rounded-lg p-6 text-center">
                            <i class="fas fa-newspaper text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-600">Chưa có bài viết nào</p>
                            @if ($isOwner)
                                <p class="text-gray-500 text-sm mt-2">Hãy đăng bài viết đầu tiên của bạn!</p>
                            @else
                                <p class="text-gray-500 text-sm mt-2">Người dùng này chưa đăng bài viết nào.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
