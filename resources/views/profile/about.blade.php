@extends('layouts.app')

@push('styles')
    @vite(['resources/css/profile.css'])
    @vite(['resources/css/about.css'])
    @vite(['resources/css/home.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
@endpush

@section('content')

    @php
        $isOwner = auth()->id() === $user->id;
    @endphp
    <div class="about-container">
        <div class="about-card">
            @include('profile.header', ['user' => $user, 'isOwner' => $isOwner])
            <div class="about-grid">
                <div class="about-details-section">
                    <div class="about-info-card">
                        <h2 class="about-info-title">Thông tin cá nhân</h2>
                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-user mr-1"></i> Bio:</span>
                            {{ $user->detail->bio ?? 'Chưa cập nhật' }}
                        </p>
                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-map-marker-alt mr-1"></i> Vị trí:</span>
                            {{ $user->detail->location ?? 'Chưa cập nhật' }}
                        </p>
                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-birthday-cake mr-1"></i> Ngày sinh:</span>
                            {{ $user->detail->birthday ?? 'Chưa cập nhật' }}
                        </p>
                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-venus-mars mr-1"></i> Giới tính:</span>
                            {{ $user->detail->gender ? (['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'][$user->detail->gender] ?? 'Chưa cập nhật') : 'Chưa cập nhật' }}
                        </p>

                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-heart mr-1"></i> Sở thích:</span> {{ $user->detail->hobbies ?? 'Chưa cập nhật'}}
                        </p>

                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-link mr-1"></i> Website:</span>
                            <a href="{{ $user->detail->website }}" target="_blank" rel="noopener noreferrer">{{ $user->detail->website ?? 'Chưa cập nhật' }}
                            </a>
                        </p>
                        <p class="about-info-paragraph">
                            <span class="about-info-label"><i class="fas fa-heartbeat mr-1"></i> Tình trạng:</span> {{ $user->detail->relationship_status ?? 'Chưa cập nhật' }}
                        </p>

                        <p class="about-info-paragraph"><span class="about-info-label"><i class="fas fa-share-alt mr-1"></i> Mạng xã hội:</span>
                            @php
                                $socialLinks = json_decode($user->detail->social_links, true);
                            @endphp
                            @if ($socialLinks)
                                @foreach ($socialLinks as $platform => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">
                                        @if ($platform === 'facebook')
                                            <i class="fab fa-facebook mr-1"></i>
                                        @elseif ($platform === 'twitter')
                                            <i class="fab fa-twitter mr-1"></i>
                                        @elseif ($platform === 'instagram')
                                            <i class="fab fa-instagram mr-1"></i>
                                        @elseif ($platform === 'linkedin')
                                            <i class="fab fa-linkedin mr-1"></i>
                                        @elseif ($platform === 'github')
                                            <i class="fab fa-github mr-1"></i>
                                        @else
                                            <i class="fas fa-globe mr-1"></i> {{ ucfirst($platform) }}
                                        @endif
                                    </a>@if (!$loop->last), @endif
                                @endforeach
                            @else
                                Chưa cập nhật
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
