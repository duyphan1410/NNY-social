@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="post">
            <div class="post-header">
                <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="avatar">
                <div class="user-info">
                    <h4>{{ $post->user->name }}</h4>
                    <small>{{ $post->created_at->diffForHumans() }}</small>
                </div>
            </div>

            <div class="post-content">
                <p>{{ $post->content }}</p>

                @if($post->images->count() > 0)
                    <div class="post-images">
                        @foreach($post->images as $image)
                            <img src="{{ $image->image_url }}" alt="Post Image">
                        @endforeach
                    </div>
                @endif

                @if($post->videos->count() > 0)
                    <div class="post-videos">
                        @foreach($post->videos as $video)
                            <video controls>
                                <source src="{{ $video->video_url }}" type="video/mp4">
                                Trình duyệt của bạn không hỗ trợ video
                            </video>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="post-actions">
                @if(auth()->id() == $post->user_id)
                    <a href="{{ route('post.edit', $post) }}" class="btn btn-warning">Chỉnh Sửa</a>
                    <form action="{{ route('post.destroy', $post) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bài đăng này?')">Xóa</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
