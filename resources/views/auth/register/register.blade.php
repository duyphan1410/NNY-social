@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="left-side">
            <h1>Connect With The World</h1>
            <p>Join our community and connect with people from around the globe. Share your stories, ideas, and experiences.</p>
        </div>

        <div class="right-side">
            <div class="logo">
                <img height="32" src="{{ asset('build/assets/img/logo2NNY.png') }}" alt="Logo">
            </div>

            <p class="welcome-text">Welcome to NNY, your social network to<br>meaningful connections</p>

            <form method="POST" action="{{ route('register') }}" class="login-form">
                @csrf

                <div class="form-row">
                    <div class="form-group half-width">
                        <label for="first_name">First name</label>
                        <input id="first_name" type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="form-group half-width">
                        <label for="last_name">Last name</label>
                        <input id="last_name" type="text" class="form-control" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('name') }}" required autocomplete="username" autofocus>
                    @error('username')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password">
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirm Password</label>
                    <input id="password-confirm" type="password"
                           class="form-control"
                           name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="sign-in-button">
                    Register
                </button>

                <p class="sign-up-text">
                    Already have an account?
                    <a href="{{ route('login') }}" class="sign-up-link">Sign In</a>
                </p>
            </form>
        </div>
    </div>
@endsection
