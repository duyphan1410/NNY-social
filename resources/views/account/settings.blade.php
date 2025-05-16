@extends('layouts.app')

@push('styles')
    @vite(['resources/css/setting.css'])
@endpush

@section('content')
    <div class="container">
        <h3>Cài đặt tài khoản</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('account.settings.update') }}">
            @csrf

            <div class="form-group">
                <label>Họ</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
            </div>

            <div class="form-group">
                <label>Tên</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label>Mật khẩu mới (bỏ trống nếu không đổi)</label>
                <input type="password" name="password" class="form-control">
                <input type="password" name="password_confirmation" class="form-control mt-1" placeholder="Xác nhận mật khẩu">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
        </form>
    </div>
@endsection
