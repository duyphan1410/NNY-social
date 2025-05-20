const baseUrl = window.location.origin + '/social-network/public';
let page = 1;
let loading = false;

// Đánh dấu tất cả đã đọc
document.getElementById('mark-all-read-btn').addEventListener('click', function() {
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
                document.querySelectorAll('.nf-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });

                console.log('✅ Đã đánh dấu tất cả thông báo là đã đọc');
            }
        })
        .catch(error => console.error('Lỗi khi đánh dấu tất cả đã đọc:', error));
});
