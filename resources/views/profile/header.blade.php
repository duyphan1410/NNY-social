@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="relative ">
        <img id="currentCover"
             src="{{ $user->detail->cover_img_url ??  asset('./img/default-cover.jpg') }}"
             class="w-full h-64 object-cover" alt="Cover"
             onerror="this.src='{{ asset('/img/default-cover.jpg') }}'"
        >
        @if ($isOwner)
            <form action="{{ route('profile.cover.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="button" class="absolute bottom-4 right-4 bg-white px-3 py-1 rounded shadow text-sm hover:bg-gray-100 transition" onclick="document.getElementById('coverUpload').click()">
                    <i class="fas fa-camera mr-1"></i> Chỉnh sửa ảnh bìa
                </button>
                <input hidden="hidden" type="file" name="cover_photo" id="coverUpload" accept="image/*" onchange="previewAndSubmit(this, 'currentCover')">
            </form>
        @endif

        <div class="absolute -bottom-16 transform -translate-x-1/2" style="transform: translateX(-50%);left: 50%;" >
            <img id="currentAvatar" src="{{ $user->avatar }}" class="w-32 h-32 rounded-full border-4 border-white object-cover shadow-lg" alt="Avatar">
            @if ($isOwner)
                <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <button type="button" class="absolute bottom-0 right-0 bg-gray-100 p-2 rounded-full border border-white shadow hover:bg-gray-200 transition cursor-pointer" onclick="document.getElementById('avatarUpload').click()">
                        <i class="fas fa-camera text-gray-600"></i>
                    </button>
                    <input hidden="hidden" type="file" name="avatar" id="avatarUpload" accept="image/*" onchange="previewAndSubmit(this, 'currentAvatar')">
                </form>
            @endif
        </div>
    </div>

    <div class="pt-20 px-8 text-center">
        <h2 class="text-2xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h2>
        <p class="text-gray-600">{{ $user->email }}</p>

        @if ($isOwner && Route::currentRouteName() !== 'profile.edit')
            <a href="{{ route('profile.edit') }}" class="mt-3 inline-block bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                <i class="fas fa-pencil-alt mr-1"></i> Chỉnh sửa trang cá nhân
            </a>
        @endif
    </div>

    <div class="border-b mt-6 flex justify-center gap-8 text-gray-600 font-medium">
        <a href="{{ route('profile.show', $user->id) }}" class="pb-2 px-1 {{ request()->routeIs('profile.show') ? 'border-b-2 border-blue-600 text-blue-600' : '' }}">Bài viết</a>
        <a href="{{ route('profile.about', $user->id) }}" class="pb-2 px-1 {{ request()->routeIs('profile.about') ? 'border-b-2 border-blue-600 text-blue-600' : '' }} hover:text-blue-600 transition">Giới thiệu</a>
        <a href="{{ route('profile.photos', $user->id) }}" class="pb-2 px-1 {{ request()->routeIs('profile.photos') ? 'border-b-2 border-blue-600 text-blue-600' : '' }} hover:text-blue-600 transition">Ảnh</a>
        <a href="{{ route('profile.videos', $user->id) }}" class="pb-2 px-1 {{ request()->routeIs('profile.video') ? 'border-b-2 border-blue-600 text-blue-600' : '' }} hover:text-blue-600 transition">Video</a>
    </div>

    <script>
        function previewAndSubmit(input, previewId) {
            console.log('previewAndSubmit được gọi', input.files);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
                input.form.submit();
            }
        }
    </script>
</div>
