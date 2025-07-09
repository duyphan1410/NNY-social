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
            <div class="container max-w-5xl mx-auto mt-6">
                <div class="grid grid-cols-1 gap-6 px-4 md:px-8 py-6">
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white shadow rounded-lg overflow-hidden p-6">
                            <h2 class="text-xl font-semibold mb-4">Video đã tải lên</h2>
                            <div class="album-grid-flex grid grid-cols-3 gap-4">
                                @forelse ($videos as $item)
                                    <div class="post-media-container album-item-flex">
                                        @if ($item['type'] === 'video')
                                            <div class="media-item video-item" data-post-id="{{ $item['post_id'] ?? '' }}">
                                                <video class="post-video w-full h-48 object-cover rounded-md shadow-sm" controls>
                                                    <source src="{{ $item['url'] }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="media-overlay">
                                                    <i class="fas fa-play-circle"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-600">Chưa có video nào được đăng.</p>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                {{--                                {{ $mergedMedia->links() }}--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
