import './bootstrap';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');
window.Alpine = Alpine;
Alpine.start();

console.log('🔁 Alpine + Echo script loaded');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local-key',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    cluster: 'mt1'
});
const baseUrl = window.location.origin + '/social-network/public';

console.log('✅ Echo initialized');

const userIdMeta = document.head.querySelector('meta[name="user-id"]');
if (!userIdMeta) {
    console.error('❌ Không tìm thấy meta[name="user-id"]');
} else {
    const userId = userIdMeta.content;
    console.log('👤 User ID:', userId);

    // Lắng nghe thông báo mới
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.notification.received', (e) => {
            console.log('🔔 Thông báo mới nhận:', e.data);
            const notificationList = document.querySelector('#notification-list');

            // Xóa thông báo "Không có thông báo nào" nếu có
            const emptyNotice = notificationList.querySelector('li.text-center.text-gray-400');
            if (emptyNotice) {
                emptyNotice.remove();
            }

            // Tạo thông báo mới với nút hành động
            const newNotification = document.createElement('li');
            newNotification.className = 'notification-item unread';
            newNotification.dataset.id = e.data.id;

            newNotification.innerHTML = `
            <div class="flex justify-between items-center">
                <a href="${e.data.url}" class="flex-grow">
                    <div class="notification-message">${e.data.message}</div>
                    <div class="notification-time">Vừa xong</div>
                </a>
                <button class="action-btn bg-blue-500 text-white rounded px-2 py-1 text-xs ml-2" data-id="${e.data.id}">
                    Xử lý
                </button>
            </div>
        `;

            notificationList.prepend(newNotification);
            attachClickEvent(newNotification); // Gắn sự kiện click cho thông báo

            // Gắn sự kiện cho nút
            const actionBtn = newNotification.querySelector('.action-btn');
            if (actionBtn) {
                attachActionButtonEvent(actionBtn);
            }

            updateNotificationCounter(1);

            if (window.toastr) {
                toastr.info(e.data.message);
            }
        });

    console.log(`📡 Đã đăng ký lắng nghe kênh App.Models.User.${userId}`);
}

// Toggle dropdown
document.querySelector('#notification-btn')?.addEventListener('click', function (e) {
    console.log('📥 Toggle dropdown clicked');
    const dropdown = document.querySelector('#notification-dropdown');
    dropdown.classList.toggle('hidden');
});

// Ẩn dropdown khi click ra ngoài
document.addEventListener('click', function () {
    const dropdown = document.querySelector('#notification-dropdown');
    if (dropdown && !dropdown.classList.contains('hidden')) {
        console.log('📤 Ẩn dropdown do click bên ngoài');
        dropdown.classList.add('hidden');
    }
});

// Gắn sự kiện click cho các item ban đầu
document.querySelectorAll('.notification-item').forEach(item => {
    attachClickEvent(item);
});

function attachActionButtonEvent(button) {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Ngăn sự kiện lan truyền lên thẻ li cha

        const notificationId = this.dataset.id;
        console.log(`🔘 Nút hành động cho thông báo ${notificationId} được nhấn`);

        // Hiển thị trạng thái đang xử lý
        this.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
        this.disabled = true;

        // Gửi yêu cầu xử lý đến server
        fetch(`${baseUrl}/notifications/${notificationId}/process`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log(`✅ Thông báo ${notificationId} đã được xử lý`);
                    this.innerHTML = `<i class="fa fa-check"></i>`;
                    this.classList.remove('bg-blue-500');
                    this.classList.add('bg-green-500');
                } else {
                    console.error(`❌ Không thể xử lý thông báo ${notificationId}`);
                    this.innerHTML = `<i class="fa fa-exclamation-triangle"></i>`;
                    this.classList.remove('bg-blue-500');
                    this.classList.add('bg-red-500');
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('❌ Lỗi khi xử lý thông báo:', error);
                this.innerHTML = `<i class="fa fa-exclamation-triangle"></i>`;
                this.classList.remove('bg-blue-500');
                this.classList.add('bg-red-500');
                this.disabled = false;
            });
    });
}

function attachClickEvent(item) {
    item.addEventListener('click', function(e) {
        // Bỏ qua nếu click vào nút hành động
        if (e.target.closest('.action-btn')) {
            return;
        }

        const notificationId = this.dataset.id;
        console.log(`✅ Click notification ${notificationId} → Gửi PUT`);

        fetch(`${baseUrl}/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log(`📬 Notification ${notificationId} marked as read`);
                    this.classList.remove('unread');
                    updateNotificationCounter(-1);
                }
            });
    });
}
// Đánh dấu tất cả là đã đọc
document.querySelector('#mark-all-read')?.addEventListener('click', function () {
    console.log('📩 Mark all as read clicked');

    fetch(`${baseUrl}/notifications/read-all`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Tất cả thông báo đã được đánh dấu đã đọc');
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });

                const counter = document.querySelector('#notification-count');
                counter.textContent = '0';
                counter.classList.add('hidden');
            }
        });
});

// Cập nhật số lượng
function updateNotificationCounter(change) {
    const counter = document.querySelector('#notification-count');
    let count = parseInt(counter.textContent || '0');
    count += change;
    if (count < 0) count = 0;

    console.log(`🔢 Update counter: ${count} (change: ${change})`);
    counter.textContent = count;

    if (count > 0) {
        counter.classList.remove('hidden');
    } else {
        counter.classList.add('hidden');
    }
}

// Cập nhật số lượng ban đầu khi trang load
document.addEventListener('DOMContentLoaded', () => {

    console.log('🚀 Trang đã load, lấy số lượng thông báo chưa đọc');

    fetch(`${baseUrl}/notifications/unread-count`)
        .then(res => res.json())
        .then(data => {
            const counter = document.querySelector('#notification-count');
            counter.textContent = data.count;
            if (data.count > 0) {
                counter.classList.remove('hidden');
            }
            console.log(`📊 Số thông báo chưa đọc: ${data.count}`);
        });
});

// Khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Trang đã load, khởi tạo hệ thống thông báo');

    // Thêm nút xử lý vào các thông báo hiện có
    document.querySelectorAll('.notification-item').forEach(item => {
        // Chỉ thêm nút nếu chưa có
        if (!item.querySelector('.action-btn')) {
            const notificationId = item.dataset.id;

            // Lấy nội dung hiện tại
            const originalContent = item.innerHTML;

            // Tạo cấu trúc mới với container
            const container = document.createElement('div');
            container.className = 'notification-container';

            // Tạo phần nội dung
            const contentDiv = document.createElement('div');
            contentDiv.className = 'notification-content';
            contentDiv.innerHTML = originalContent;

            // Tạo phần action
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'notification-actions';

            // Tạo nút xử lý
            const actionBtn = document.createElement('button');
            actionBtn.className = 'action-btn';
            actionBtn.dataset.id = notificationId;
            actionBtn.textContent = 'Xử lý';

            // Ghép các phần lại với nhau
            actionsDiv.appendChild(actionBtn);
            container.appendChild(contentDiv);
            container.appendChild(actionsDiv);

            // Thay thế nội dung cũ
            item.innerHTML = '';
            item.appendChild(container);

            // Gắn sự kiện cho nút
            attachActionButtonEvent(actionBtn);
        }
    });

    // Gắn sự kiện click cho các notification hiện có
    document.querySelectorAll('.notification-item').forEach(item => {
        attachClickEvent(item);
    });

    // Lấy số lượng thông báo chưa đọc
    fetch(`${baseUrl}/notifications/unread-count`)
        .then(res => res.json())
        .then(data => {
            const counter = document.querySelector('#notification-count');
            counter.textContent = data.count;
            if (data.count > 0) {
                counter.classList.add('show');
            }
            console.log(`📊 Số thông báo chưa đọc: ${data.count}`);
        });

    // Sửa lại đường dẫn ảnh trong notification nếu cần
    document.querySelectorAll('.notification-item img').forEach(img => {
        if (img.src && !img.src.startsWith('http')) {
            img.src = '/' + img.src.replace(/^\/+/, '');
        }
    });
});
