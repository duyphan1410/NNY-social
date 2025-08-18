    <div id="chat-box">
    @foreach ($messages as $msg)
        <div class="{{ $msg->user_id === auth()->id() ? 'me' : 'other' }}">
            <strong>{{ $msg->user->name }}:</strong> {{ $msg->content }}
        </div>
        @endforeach
    </div>

    <input type="text" id="chat-input">
    <button onclick="sendMessage()">Gửi</button>

    <script>
        const conversationId = {{ $conversation->id }};
        const userId = {{ auth()->id() }};

        // Lắng nghe tin nhắn realtime
        Echo.private(`chat.${conversationId}`)
            .listen('NewMessageSent', (e) => {
                let box = document.getElementById("chat-box");
                box.innerHTML += `<div><strong>${e.sender_name}</strong>: ${e.content}</div>`;
            });

        function sendMessage() {
            const content = document.getElementById("chat-input").value;
            axios.post(`/chat/${conversationId}/send`, { content }).then(res => {
                document.getElementById("chat-input").value = "";
            });
        }
    </script>
