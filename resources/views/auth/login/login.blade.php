@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="left-side">
            <h1>Connect With The World</h1>
            <p>Join our community and connect with people from around the globe. Share your stories, ideas, and experiences.</p>
        </div>

        <div class="right-side">
            <div class="logo">
                <img height="32" src="{{ asset('build/assets/img/logo.png') }}" alt="Logo">
            </div>

            <p class="welcome-text">Welcome to NNY, your social netword to<br>meaningful connections</p>

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}"
                           required autocomplete="email" autofocus>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember Me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="sign-in-button">
                    Sign In
                </button>

                <p class="sign-up-text">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="sign-up-link">Sign Up</a>
                </p>
            </form>
        </div>
    </div>
@endsection
