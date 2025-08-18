@foreach($conversations as $conversation)
    <a href="{{ route('chat.show', $conversation) }}">
        Cuộc trò chuyện với:
        {{ $conversation->users->where('id', '!=', auth()->id())->first()->name }}
    </a>
@endforeach
