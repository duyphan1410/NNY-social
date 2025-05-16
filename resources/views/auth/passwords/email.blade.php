@extends('layouts.app')

@push('styles')
    @vite(['resources/css/auth-forms.css'])
@endpush

@push('scripts')
    @vite(['resources/js/auth-forms.js'])
@endpush

@section('content')
    <div class="auth-container">
        <h2>Quên mật khẩu</h2>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Nhập email của bạn</label>
                <input id="email" type="email" name="email" required autofocus
                       class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Gửi link đặt lại mật khẩu
            </button>
        </form>
    </div>
@endsection
