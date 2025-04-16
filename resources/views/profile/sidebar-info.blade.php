@php
    $isOwner = auth()->id() === $user->id;
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
        <p>
            <span class="inline-block w-24 text-gray-600 font-medium">Giới tính:</span>
            <span>{{ ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'][$user->detail->gender] ?? 'Chưa cập nhật' }}</span>
        </p>
        <p>
            <span class="inline-block w-24 text-gray-600 font-medium">Ngày sinh:</span>
            <span>{{ $user->detail->birthday ?? 'Chưa cập nhật' }}</span>
        </p>
        <p>
            <span class="inline-block w-24 text-gray-600 font-medium">Vị trí:</span>
            <span>{{ $user->detail->location ?? 'Chưa cập nhật' }}</span>
        </p>
        <p>
            <span class="inline-block w-24 text-gray-600 font-medium">Bio:</span>
            <span>{{ $user->detail->bio ?? 'Chưa cập nhật' }}</span>
        </p>
    </div>
</div>
