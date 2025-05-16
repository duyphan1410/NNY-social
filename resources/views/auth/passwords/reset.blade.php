@extends('layouts.app')


@push('styles')
    @vite(['resources/css/auth-forms.css'])
@endpush

@push('scripts')
    @vite(['resources/js/auth-forms.js'])
@endpush

@section('content')
    <div class="auth-container">
        <h2>Đặt lại mật khẩu</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required autofocus
                       class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}">
                @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input id="password" type="password" name="password" required
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm">Xác nhận mật khẩu mới</label>
                <input id="password-confirm" type="password" name="password_confirmation" required class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                Đặt lại mật khẩu
            </button>
        </form>
    </div>
@endsection
