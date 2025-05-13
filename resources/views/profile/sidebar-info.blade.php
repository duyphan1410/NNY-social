@php
    $isOwner = auth()->id() === $user->id;

    $bio = $user->detail->bio ?? 'Chưa cập nhật';
    $maxLength = 50;
    $isLong = strlen($bio) > $maxLength;
    $shortBio = Str::limit($bio, $maxLength);
    $shortBioWithDots = $shortBio . '...';
@endphp

<div class="bg-gray-50 p-4 rounded shadow">
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-semibold">Giới thiệu</h3>
        @if ($isOwner)
            <a href="{{ route('profile.edit') }}" class="text-blue-600 text-sm hover:underline">
                <i class="fas fa-pencil-alt"></i>
            </a>
        @endif
    </div>
    <div class="space-y-2">
        <p class="flex items-baseline">
            <span class="inline-block w-24 text-gray-600 font-medium">Giới tính:</span>
            <span>{{ $user->detail->gender ? (['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'][$user->detail->gender] ?? 'Chưa cập nhật') : 'Chưa cập nhật' }}</span>
        </p>
        <p class="flex items-baseline">
            <span class="inline-block w-24 text-gray-600 font-medium">Ngày sinh:</span>
            <span>{{ $user->detail->birthday ?? 'Chưa cập nhật' }}</span>
        </p>
        <p class="flex items-baseline">
            <span class="inline-block w-24 text-gray-600 font-medium">Vị trí:</span>
            <span>{{ $user->detail->location ?? 'Chưa cập nhật' }}</span>
        </p>
        <p class="flex items-baseline" >
            <span class="inline-block w-24 text-gray-600 font-medium">Bio:</span>
            <span id="bio-text" class="bio-text {{ $isLong ? 'bio-text-collapsed' : '' }}" >
                {{ $isLong ? $shortBio . '...' : $bio }}
                @if ($isLong)
                    <a href="#" onclick="toggleBio(event)" id="toggle-link" class="text-blue-500 ml-2">Xem thêm</a>
                @endif
            </span>
        </p>
    </div>
</div>

<script>
    function toggleBio(e) {
        e.preventDefault();
        const fullBio = @json($bio);
        const shortBio = @json($shortBioWithDots);
        const bioText = document.getElementById("bio-text");
        const link = document.getElementById("toggle-link");

        const isExpanded = link.dataset.expanded === "true";


        bioText.innerHTML = (isExpanded ? shortBio : fullBio)
            + `<a href="#" onclick="toggleBio(event)" id="toggle-link" data-expanded="${!isExpanded}" class="text-blue-500 ml-2">${isExpanded ? '  Xem thêm' : '  Thu gọn'}</a>`;
        if (isExpanded) {
            bioText.classList.remove('bio-text-expanded');
            bioText.classList.add('bio-text-collapsed');
        } else {
            bioText.classList.remove('bio-text-collapsed');
            bioText.classList.add('bio-text-expanded');
        }
    }
</script>
