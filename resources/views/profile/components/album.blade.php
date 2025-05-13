<div class="bg-gray-50 p-4 rounded shadow">
    <h3 class="text-lg font-semibold mb-3">Tất cả ảnh và video đã tải lên</h3>
    @php
        $mergedMedia = collect();
        if($user->posts) {
            foreach($user->posts as $post) {
                // Lấy ảnh
                if($post->images && count($post->images) > 0) {
                    foreach($post->images as $image) {
                        $mergedMedia->push([
                            'type' => 'photo',
                            'url' => $image->image_url,
                            'created_at' => $post->created_at,
                        ]);
                    }
                }
                // Lấy video (giả sử model PostVideo có relationship 'post' và thuộc tính 'video_url')
                if($post->videos && count($post->videos) > 0) {
                    foreach($post->videos as $video) {
                        $mergedMedia->push([
                            'type' => 'video',
                            'url' => $video->video_url,
                            'created_at' => $post->created_at, // Tương tự, có thể sắp xếp theo thời gian đăng bài
                        ]);
                    }
                }
            }
        }

        // Sắp xếp theo thời gian tạo (nếu cần, ví dụ mới nhất trước)
        $sortedMedia = $mergedMedia->sortByDesc('created_at');

        // Giới hạn 6 mục mới nhất
        $displaymergedMedia = $sortedMedia->take(9);
    @endphp
    @if($displaymergedMedia->count() > 0)
        <div class="album-grid-flex">
            @foreach($displaymergedMedia as $item)
                <div class="album-item-flex">
                    @if ($item['type'] === 'photo')
                        <img class="w-full h-full object-cover rounded" src="{{ $item['url'] }}" alt="Photo">
                    @elseif ($item['type'] === 'video')
                        <video class="w-full h-full object-cover rounded" controls>
                            <source src="{{ $item['url'] }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </div>
            @endforeach
        </div>

        @if($displaymergedMedia->count() > 9)
            <div class="mt-3 text-right">
        <span class="text-gray-500 text-sm">
            +{{ $displaymergedMedia->count() - 9 }} ảnh khác
        </span>
            </div>
        @endif
    @else
        <p class="text-gray-500">Chưa có ảnh nào</p>
    @endif
</div>
