import './bootstrap';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
Alpine.start();

const baseUrl = window.location.origin + '/social-network/public';

// Echo config
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local-key',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    cluster: 'mt1',
    authEndpoint: baseUrl + '/broadcasting/auth', // ✅ thêm dòng này
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }
});

console.log("✅ app.js đã load");

// Khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Trang đã load, khởi tạo hệ thống thông báo');

    const notificationBtn = document.getElementById('notification-btn');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationCount = document.getElementById('notification-count');
    const markAllReadBtn = document.getElementById('mark-all-read');

    // Toggle dropdown khi click vào nút thông báo
    notificationBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');

        // Nếu dropdown hiển thị và có thông báo chưa đọc, đánh dấu đã xem (chưa đánh dấu đã đọc)
        if (notificationDropdown.classList.contains('show')) {
            markNotificationsAsViewed();
        }
    });

    // Đóng dropdown khi click bên ngoài
    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && e.target !== notificationBtn) {
            notificationDropdown.classList.remove('show');
        }
    });

    // Gắn sự kiện click cho các notification hiện có
    function attachNotificationEvents() {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Chỉ xử lý khi click vào phần nội dung, không phải action button
                if (!e.target.closest('.action-btn-nof')) {
                    const notificationId = this.dataset.id;

                    // Đánh dấu đã đọc
                    markAsRead(notificationId);

                    // Nếu có link trong notification, chuyển hướng
                    const link = this.querySelector('a');
                    if (link && link.href) {
                        window.location.href = link.href;
                    }
                }
            });
        });
    }

    // Gắn sự kiện cho các nút xử lý trong thông báo
    function attachActionButtonEvents() {
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const notificationId = this.dataset.id;

                // Đánh dấu đã đọc
                markAsRead(notificationId);

                // Thêm logic xử lý thông báo ở đây nếu cần
                console.log(`Xử lý thông báo có ID: ${notificationId}`);

                // Ẩn notification item sau khi xử lý
                const item = this.closest('.notification-item');
                if (item) {
                    item.style.display = 'none';
                }

                // Cập nhật số lượng thông báo
                updateNotificationCount();


            });
        });
    }

    // Đánh dấu đã xem khi mở dropdown (không phải đánh dấu đã đọc)
    function markNotificationsAsViewed() {
        // Cập nhật giao diện để hiển thị đã xem
        notificationCount.classList.add('hidden');
    }

    // Đánh dấu một thông báo đã đọc
    function markAsRead(notificationId) {
        fetch(`${baseUrl}/notifications/mark-as-read/${notificationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật UI
                    const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('unread');
                    }

                    // Cập nhật số lượng thông báo
                    updateNotificationCount();
                }
            })
            .catch(error => console.error('Lỗi khi đánh dấu đã đọc:', error));
    }

    // Đánh dấu tất cả thông báo đã đọc
    markAllReadBtn.addEventListener('click', () => {
        fetch(`${baseUrl}/notifications/mark-all-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật UI
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });

                    // Cập nhật số lượng
                    notificationCount.textContent = '0';
                    notificationCount.classList.add('hidden');

                    console.log('✅ Đã đánh dấu tất cả thông báo là đã đọc');
                }
            })
            .catch(error => console.error('Lỗi khi đánh dấu tất cả đã đọc:', error));
    });

    // Cập nhật số lượng thông báo chưa đọc
    function updateNotificationCount() {
        fetch(`${baseUrl}/notifications/unread-count`)
            .then(res => res.json())
            .then(data => {
                console.log(data.count);
                const notificationCount = document.getElementById('notification-count');
                notificationCount.textContent = data.count;
                if (data.count > 0) {
                    notificationCount.classList.remove('hidden');
                    notificationCount.style.visibility = 'visible';
                    document.querySelector('#notification-btn').classList.add('bg-red-600');
                } else {
                    notificationCount.classList.add('hidden');
                    notificationCount.style.visibility = 'hidden';
                    document.querySelector('#notification-btn').classList.remove('bg-red-600');
                }
            })
            .catch(error => console.error('Lỗi khi lấy số lượng thông báo:', error));
    }

    // Khởi tạo các sự kiện
    attachNotificationEvents();
    attachActionButtonEvents();

    // Lấy số lượng thông báo chưa đọc ban đầu
    updateNotificationCount();

    const userId = document.querySelector('meta[name="user-id"]').content;

    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.notification.received', (e) => {
            console.log('📬 Nhận được thông báo mới từ websocket:', e);

            // Tạo thông báo mới
            const list = document.getElementById('notification-list');
            const li = document.createElement('li');
            li.className = 'notification-item unread';
            li.dataset.id = e.id;

            li.innerHTML = `
            <div class="notification-container">
                <div class="notification-content">
                    <a href="${e.url || '#'}">
                        <div class="notification-message">${e.message}</div>
                        <div class="notification-time text-xs text-gray-500">vừa xong</div>
                    </a>
                </div>
                <div class="notification-actions">
                    <button class="action-btn-nof text-sm text-blue-600 hover:underline" data-id="${e.id}">Xử lý</button>
                </div>
            </div>
        `;

            list.prepend(li);

            // Cập nhật số lượng
            updateNotificationCount();
            attachNotificationEvents();
            attachActionButtonEvents();
        });
});


