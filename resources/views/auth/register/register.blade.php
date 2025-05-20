@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="left-side">
            <h1>Mở rộng kết nối</h1>
            <p>Kết nối với những người bạn từ khắp nơi trên thế giới, chia sẻ những câu chuyện, ý tưởng và trải nghiệm của bạn.</p>
        </div>

        <div class="right-side">
            <div class="logo">
                <img height="32" src="{{ asset('img/logo.png') }}" alt="Logo">
            </div>

            <p class="welcome-text">Chào mừng bạn đến với mạng xã hội NNY,<br>nơi kết nối những giá trị.</p>

            <form method="POST" action="{{ route('register') }}" class="login-form">
                @csrf

                <div class="form-row">
                    <div class="form-group half-width">
                        <label for="first_name">Tên</label>
                        <input id="first_name" type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="form-group half-width">
                        <label for="last_name">Họ</label>
                        <input id="last_name" type="text" class="form-control" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Tên tài khoản</label>
                    <input id="username" type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('name') }}" required autocomplete="username" autofocus>
                    @error('username')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Địa chỉ email</label>
                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password">
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Nhập lại mật khẩu</label>
                    <input id="password-confirm" type="password"
                           class="form-control"
                           name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="sign-in-button">
                    Đăng ký
                </button>

                <p class="sign-up-text">
                    Already have an account?
                    <a href="{{ route('login') }}" class="sign-up-link">Sign In</a>
                </p>
            </form>
        </div>
    </div>
@endsection
