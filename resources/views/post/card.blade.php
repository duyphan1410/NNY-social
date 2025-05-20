@php
    $isOwner = auth()->check() && auth()->id() === $post->user->id;
    $profileRoute = $isOwner
        ? route('profile.me')
        : route('profile.show', $post->user->id);
@endphp

@php
    // Tách riêng xử lý hình ảnh và video
    $images = collect($post->images ?? []);
    $videos = collect($post->videos ?? []);

    // Giới hạn số lượng theo yêu cầu
    $imagesToShow = $images->take(5);
    $videosToShow = $videos->take(3);

    // Đếm số lượng thực tế
    $imageCount = $imagesToShow->count();
    $videoCount = $videosToShow->count();
    $totalMediaCount = $imageCount + $videoCount;

    // Xác định class cho layout dựa trên số lượng phương tiện
    $layoutClass = '';
    if ($totalMediaCount == 1) {
        $layoutClass = 'media-single';
    } elseif ($totalMediaCount == 2) {
        $layoutClass = 'media-two';
    } elseif ($totalMediaCount == 3) {
        $layoutClass = 'media-three';
    } elseif ($totalMediaCount == 4) {
        $layoutClass = 'media-four';
    } elseif ($totalMediaCount >= 5) {
        $layoutClass = 'media-five';
    }
@endphp
<div class="post">
    <div class="post-header">
        <div class="user-info">
            <a href="{{$profileRoute}}">
                <img class="post-avatar" src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}">
                <div class="user-details">
                    <h4 class="user-name">{{ $post->user->first_name }} {{ $post->user->last_name }}</h4>
                    <a href="{{ route('post.show', ['id' => $post->id]) }}"><span class="post-time">{{ $post->created_at->diffForHumans() }}</span></a>
                </div>
            </a>
        </div>
        <div class="post-options dropdown">
            <button class="dropdown-btn">
                <i class="fas fa-ellipsis-h"></i>
            </button>
            <ul class="dropdown-menu">
                @if(auth()->check() && auth()->id() == $post->user_id)
                    <li>
                        <a href="{{ route('post.edit', ['id' => $post->id]) }}">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('post.destroy', $post) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <button class="report-post-btn" data-post-id="{{ $post->id }}">
                            <i class="fas fa-flag"></i> Báo cáo
                        </button>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="post-content">
        <p class="post-text">{{ $post->content }}</p>
    </div>

    @if ($post->sharedPost)
        @php
            $isOwner = auth()->check() && auth()->id() === $post->sharedPost->user->id;
            $profileRoute = $isOwner
                ? route('profile.me')
                : route('profile.show', $post->sharedPost->user->id);
        @endphp
        <div class="shared-post">
            <div class="post-header">
                <div class="user-info">
                    <a href="{{ $profileRoute }}">
                        <img class="post-avatar" src="{{ $post->sharedPost->user->avatar }}" alt="{{ $post->sharedPost->user->name }}">
                        <div class="user-details">
                            <h4 class="user-name">{{ $post->sharedPost->user->first_name }} {{ $post->sharedPost->user->last_name }}</h4>
                            <a href="{{ route('post.show', ['id' => $post->sharedPost->id]) }}"><span class="post-time">{{ $post->sharedPost->created_at->diffForHumans() }}</span></a>
                        </div>
                    </a>
                </div>
            </div>
            <div class="post-content">
                <p class="post-text">{{ $post->sharedPost->content }}</p>
            </div>
            @php
                $sharedMedia = collect([]);
                if ($post->sharedPost->images) {
                    $sharedMedia = $sharedMedia->merge($post->sharedPost->images);
                }
                if ($post->sharedPost->videos) {
                    $sharedMedia = $sharedMedia->merge($post->sharedPost->videos);
                }
                $sharedMediaCount = $sharedMedia->count();
                $sharedMediaClass = $sharedMediaCount == 1 ? 'single' : ($sharedMediaCount == 2 ? 'two' : ($sharedMediaCount == 3 ? 'three' : 'four'));
            @endphp

            @if ($sharedMediaCount > 0)
                <div class="post-media {{ $sharedMediaClass }}">
                    @foreach ($sharedMedia as $item)
                        @if (isset($item->image_url))
                            <img class="post-image" src="{{ $item->image_url }}" alt="Shared Post Image">
                        @elseif (isset($item->video_url))
                            <video class="post-video" controls>
                                <source src="{{ $item->video_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <!-- Hiển thị phương tiện -->
        @if ($totalMediaCount > 0)
            <div class="post-media-container {{ $layoutClass }}">
                <!-- Hiển thị video (ưu tiên hiển thị trước) -->
                @foreach ($videosToShow as $index => $video)
                    <div class="media-item video-item">
                        <video class="post-video" controls>
                            <source src="{{ $video->video_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="media-overlay">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                @endforeach

                <!-- Hiển thị hình ảnh -->
                @foreach ($imagesToShow as $index => $image)
                    <div class="media-item image-item">
                        <img class="post-image" src="{{ $image->image_url }}" alt="Post Image {{ $index + 1 }}">

                        <!-- Nếu là ảnh cuối cùng và còn ảnh chưa hiển thị -->
                        @if ($index == 4 && $images->count() > 5)
                            <div class="more-overlay">
                                <span>+{{ $images->count() - 5 }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <div class="post-stats">
        <div class="like-count">
            <button class="view-likes-btn" data-post-id="{{ $post->id }}">
                <span>{{ $post->likes_count ?? 0 }} thích</span>
            </button>
        </div>

        <div id="likes-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h3>Những người đã thích</h3>
                <ul id="likes-list">
                </ul>
            </div>
        </div>

        <div class="comment-count">
            <span>{{ $post->comments_count ?? 0 }} bình luận</span>
        </div>
    </div>

    <div class="post-actions">
        <button class="action-btn like-btn {{ $post->user_has_liked ? 'active' : '' }}" data-post-id="{{ $post->id }}">
            <i class="fas fa-heart"></i>
            <span class="like-count">{{ $post->likes_count }}</span>
            <span>Thích</span>
        </button>
        <span class="like-float" style="display:none;">❤️</span>
        <a href="{{ route('post.show', ['id' => $post->id]) }}"
           class="action-btn comment-btn">
            <i class="fas fa-comment"></i>
            <span>Bình luận</span>
        </a>
        <a href="{{ route('post.share.form', ['id' => $post->id]) }}" class="action-btn share-btn">
            <i class="fas fa-share"></i>
            <span>Chia sẻ</span>
        </a>
    </div>
</div>
