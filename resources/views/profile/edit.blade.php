@extends('layouts.app')

@push('styles')
    @vite(['resources/css/profile.css'])
    @vite(['resources/css/edit_profile_info.css'])
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
            <div class=" gap-6 px-4 md:px-8 py-6">
                <div class="profile-edit-container">
                    <div class="profile-edit-card">
                        <h1 class="profile-edit-title">Chỉnh sửa hồ sơ</h1>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-edit-group">
                                <label for="first_name" class="form-edit-label">Họ</label>
                                <input type="text" name="first_name" id="first_name"
                                       value="{{ old('first_name', $user->first_name) }}"
                                       class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label for="last_name" class="form-edit-label">Tên</label>
                                <input type="text" name="last_name" id="last_name"
                                       value="{{ old('last_name', $user->last_name) }}"
                                       class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label class="form-edit-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label class="form-edit-label">Giới thiệu</label>
                                <textarea name="bio" class="form-edit-textarea">{{ old('bio', $user->detail->bio ?? '') }}</textarea>
                            </div>

                            <div class="form-edit-group">
                                <label class="form-edit-label">Địa điểm</label>
                                <input type="text" name="location" value="{{ old('location', $user->detail->location ?? '') }}" class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label class="form-edit-label">Ngày sinh</label>
                                <input type="date" name="birthday" value="{{ old('birthday', $user->detail->birthday ?? '') }}" class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label class="form-edit-label">Giới tính</label>
                                <select name="gender" class="form-edit-select">
                                    <option value="">-- Chọn --</option>
                                    <option value="male" {{ old('gender', $user->detail->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender', $user->detail->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender', $user->detail->gender ?? '') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            <div class="form-edit-group">
                                <label for="website" class="form-edit-label">Website</label>
                                <input type="url" name="website" id="website"
                                       value="{{ old('website', $user->detail->website ?? '') }}"
                                       class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label for="relationship_status" class="form-edit-label">Tình trạng mối quan hệ</label>
                                <input type="text" name="relationship_status" id="relationship_status"
                                       value="{{ old('relationship_status', $user->detail->relationship_status ?? '') }}"
                                       class="form-edit-input">
                            </div>

                            <div class="form-edit-group">
                                <label for="hobbies" class="form-edit-label">Sở thích</label>
                                <textarea name="hobbies" id="hobbies"
                                          class="form-edit-textarea">{{ old('hobbies', $user->detail->hobbies ?? '') }}</textarea>
                            </div>

                            <div class="form-edit-group">
                                <label for="social_links" class="form-edit-label">Liên kết mạng xã hội (JSON)</label>
                                <textarea name="social_links" id="social_links"
                                          class="form-edit-textarea">{{ old('social_links', $user->detail->social_links ?? '') }}</textarea>
                                <small class="text-gray-500">Nhập dưới dạng JSON (ví dụ: {"facebook": "...", "twitter": "..."})</small>
                            </div>

                            @if ($errors->any())
                                <div class="form-edit-error-message">
                                    <ul class="form-edit-error-list">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mt-6">
                                <button type="submit" class="save-profile-button">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
