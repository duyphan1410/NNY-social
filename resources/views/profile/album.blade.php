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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-8 py-6">
                    <div class="space-y-6">
                        @include('profile.sidebar-info', ['user' => $user])
                        @include('profile.components.album', ['user' => $user])
                    </div>
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white shadow rounded-lg overflow-hidden p-6">
                            <h2 class="text-xl font-semibold mb-4">Tất cả ảnh và video</h2>
                            <div class="grid grid-cols-3 gap-4">
                                @forelse ($mergedMedia as $item)
                                    @if ($item['type'] === 'photo')
                                        <img class="w-full h-48 object-cover rounded-md shadow-sm" src="{{ $item['url'] }}" alt="Original Post Image">
                                    @elseif ($item['type'] === 'video')
                                        <video class="w-full h-48 object-cover rounded-md shadow-sm" controls>
                                            <source src="{{ $item['url'] }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
{{--                                    <div>--}}
{{--                                        <img src="{{ $media->image_url }}" alt="Ảnh đã đăng" class="w-full h-48 object-cover rounded-md shadow-sm">--}}
{{--                                    </div>--}}
                                @empty
                                    <p class="text-gray-600">Chưa có ảnh nào được đăng.</p>
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
