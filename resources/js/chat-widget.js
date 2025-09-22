document.addEventListener('DOMContentLoaded', function () {
    console.log("Current user:", window.CURRENT_USER_ID)

    // Start listening for real-time messages
    if (window.CURRENT_USER_ID) {
        startListeningForMessages();
    }

    document.querySelectorAll('.open-chat-btn').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            openChatPopup(userId, userName);
        })
    })
});

// Real-time message listening - sử dụng pattern giống như notification
function startListeningForMessages() {
    const userId = window.CURRENT_USER_ID;

    if (window.hasSubscribedToMessages) {
        // Đã subscribe rồi thì không cần lặp lại
        return;
    }

    window.hasSubscribedToMessages = true;

    // Sử dụng pattern giống như notification system
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.message.received', (e) => {
            console.log('💬 Nhận được tin nhắn mới từ websocket:', e);

            const senderId = e.message.sender_id;
            const popup = document.getElementById(`chat-popup-${senderId}`);

            if (!popup) {
                // Mở popup chat nếu chưa có
                openChatPopup(senderId, e.sender.name);
            } else {
                // Thêm tin nhắn vào chat đã mở
                // nhưng nhớ check duplicate theo message.id
                if (!document.getElementById(`message-${e.message.id}`)) {
                    appendNewMessage(senderId, e.message, e.sender);
                }
            }
        })
        .error((err) => {
            console.error("❌ Echo message channel error:", err);
        });
}

// Append new message to chat UI
function appendNewMessage(userId, message, sender) {
    const body = document.getElementById(`chat-body-${userId}`);
    if (!body) return;

    // Check if message already exists
    const existingMessage = body.querySelector(`[data-message-id="${message.id}"]`);
    if (existingMessage) return;

    const div = document.createElement('div');
    div.className = message.sender_id === window.CURRENT_USER_ID ? 'message me' : 'message them';
    div.setAttribute('data-message-id', message.id);

    const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    let status = '';
    if (message.sender_id === window.CURRENT_USER_ID) {
        status = message.is_read ? '✓✓ Đã xem' : '✓ Đã gửi';
    }

    const deleteHtml = (message.sender_id === window.CURRENT_USER_ID)
        ? `<span class="delete-btn" data-message-id="${message.id}" title="Xóa tin nhắn">🗑️</span>`
        : '';

    div.innerHTML = `
        <div class="message-wrapper">
            <div class="bubble">
                ${escapeHtml(message.content)}
            </div>
            ${deleteHtml}
        </div>
        <div class="meta">${time} ${status}</div>
    `;

    body.appendChild(div);
    body.scrollTop = body.scrollHeight;

    // Add delete event listener if needed
    if (message.sender_id === window.CURRENT_USER_ID) {
        const btn = div.querySelector('.delete-btn');
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                deleteMessage(message.id, this);
            });
        }
    }

    // Show notification if chat is not focused
    showMessageNotification(userId, sender, message);
}

// Show notification for new messages
function showMessageNotification(userId, sender, message) {
    const popup = document.getElementById(`chat-popup-${userId}`);
    if (!popup) return;

    // Add visual indicator
    const header = popup.querySelector('.chat-header strong');
    if (header && message.sender_id !== window.CURRENT_USER_ID) {
        header.style.fontWeight = 'bold';
        header.style.color = '#007bff';

        // Reset after 3 seconds
        setTimeout(() => {
            header.style.fontWeight = 'normal';
            header.style.color = 'inherit';
        }, 3000);
    }
}

const BASE_URL = window.location.origin + '/social-network/public';

// helper escape
function escapeHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function openChatPopup(userId, userName) {
    let existingPopup = document.querySelector(`#chat-popup-${userId}`);
    if (existingPopup) {
        existingPopup.classList.remove('hidden');
        return;
    }

    const container = document.getElementById('chat-popups-container');
    const popup = document.createElement('div');
    popup.id = `chat-popup-${userId}`;
    popup.className = 'chat-popup';

    popup.innerHTML = `
        <div class="chat-header">
            <strong>${userName}</strong>
            <span style="margin-left:auto;cursor:pointer;" onclick="closeChat(${userId}); this.closest('.chat-popup').remove()">✖</span>
        </div>
        <div class="chat-body" id="chat-body-${userId}"></div>
        <div class="chat-input">
            <textarea id="chat-input-${userId}" placeholder="Type a message..."></textarea>
            <button class="chat-send-btn" onclick="sendMessage(${userId})">Send</button>
        </div>
    `;

    container.appendChild(popup);

    // load tin nhắn
    fetch(`${BASE_URL}/messages/${userId}`, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(messages => {
            const body = document.getElementById(`chat-body-${userId}`);
            body.innerHTML = '';
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = msg.sender_id === window.CURRENT_USER_ID ? 'message me' : 'message them';

                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                let status = '';
                if (msg.sender_id === window.CURRENT_USER_ID) {
                    status = msg.is_read ? '✓✓ Đã xem' : '✓ Đã gửi';
                }

                // nếu là tin của mình thì thêm nút delete
                const deleteHtml = (msg.sender_id === window.CURRENT_USER_ID)
                    ? `<span class="delete-btn" data-message-id="${msg.id}" title="Xóa tin nhắn">🗑️</span>`
                    : '';

                div.innerHTML = `
                    <div class="message-wrapper">
                        <div class="bubble">
                            ${escapeHtml(msg.content)}
                        </div>
                        ${deleteHtml}
                    </div>
                    <div class="meta">${time} ${status}</div>
                `;

                body.appendChild(div);

                if (msg.sender_id === window.CURRENT_USER_ID) {
                    const btn = div.querySelector('.delete-btn');
                    if (btn) {
                        btn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            deleteMessage(msg.id, this);
                        });
                    }
                }
            });
            body.scrollTop = body.scrollHeight;
        });
}

function sendMessage(userId) {
    const textarea = document.getElementById(`chat-input-${userId}`);
    const content = textarea.value.trim();
    if (!content) return;

    fetch(`${BASE_URL}/messages`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ receiver_id: userId, content: content })
    })
        .then(res => res.json())
        .then(msg => {
            textarea.value = '';
            const body = document.getElementById(`chat-body-${userId}`);
            const div = document.createElement('div');
            div.className = 'message me';

            const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            div.innerHTML = `
                <div class="message-wrapper">
                    <div class="bubble">
                        ${escapeHtml(msg.content)}
                    </div>
                    <span class="delete-btn" data-message-id="${msg.id}" title="Xóa tin nhắn">🗑️</span>
                </div>
                <div class="meta">${time} ✓ Đã gửi</div>
            `;

            const btn = div.querySelector('.delete-btn');
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                deleteMessage(msg.id, this);
            });

            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        })
}

function deleteMessage(messageId, el) {
    if (!confirm('Bạn có chắc muốn xóa tin nhắn này?')) return;
    fetch(`${BASE_URL}/messages/${messageId}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                el.closest('.message').remove();
            } else {
                alert(data.error || 'Xóa thất bại');
            }
        })
        .catch(err => console.error(err));
}

function closeChat(userId) {
    // Không cần cleanup channel vì dùng chung channel user
    console.log(`Đóng chat với user ${userId}`);
}

window.deleteMessage = deleteMessage;
window.closeChat = closeChat;
window.sendMessage = sendMessage;
