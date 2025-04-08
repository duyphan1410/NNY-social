let currentPosition = 0;

function scrollReel(direction) {
    const reelWrapper = document.querySelector('.reel-wrapper');
    const reelItems = document.querySelectorAll('.reel-item');

    if (!reelWrapper || reelItems.length === 0) return;

    const reelItemWidth = reelItems[0].offsetWidth + 10; // Khoảng cách giữa các reel
    const reelContainerWidth = document.querySelector('.reel-container').offsetWidth;
    const reelWrapperWidth = reelItems.length * reelItemWidth;

    const maxPosition = Math.max(0, Math.floor((reelWrapperWidth - reelContainerWidth) / reelItemWidth));

    if (direction === 1 && currentPosition < maxPosition) {
        currentPosition++;
    } else if (direction === -1 && currentPosition > 0) {
        currentPosition--;
    }

    reelWrapper.style.transform = `translateX(${-currentPosition * reelItemWidth}px)`;

    // Cập nhật trạng thái nút bấm
    document.querySelector('.reel-btn.prev').disabled = currentPosition === 0;
    document.querySelector('.reel-btn.next').disabled = currentPosition >= maxPosition;
}

window.openShareForm = function(postId) {
    const form = document.getElementById(`share-form-${postId}`);
    if (form) {
        form.classList.toggle('hidden');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.reel-btn.prev').disabled = true;
});

document.addEventListener("DOMContentLoaded", function () {
    // Xử lý dropdown
    document.querySelectorAll(".dropdown-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();  // Ngăn chặn sự kiện click lan ra ngoài
            let parent = this.closest(".dropdown");
            parent.classList.toggle("active");
        });
    });

    // Ẩn dropdown khi click ra ngoài, NGOẠI TRỪ các thành phần bên trong dropdown
    document.addEventListener("click", function (event) {
        // Kiểm tra xem phần tử được click có nằm trong dropdown không
        if (!event.target.closest(".dropdown-menu") && !event.target.closest(".delete-form")) {
            document.querySelectorAll(".dropdown").forEach(dropdown => {
                dropdown.classList.remove("active");
            });
        }
    });

    // Xử lý form xóa
    document.querySelectorAll(".delete-form").forEach(form => {
        form.addEventListener("click", function(event) {
            // Ngăn sự kiện lan ra document khi click vào form
            event.stopPropagation();
        });

        const deleteButton = form.querySelector('button[type="submit"]');
        if (deleteButton) {
            deleteButton.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();

                let confirmDelete = confirm("Bạn có chắc chắn muốn xóa?");
                if (confirmDelete) {
                    setTimeout(() => {
                        form.submit();
                    }, 0);
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function () {
            const postId = this.dataset.postId;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const buttonEl = this;
            const baseUrl = window.location.origin + '/social-network/public';

            fetch(`${baseUrl}/post/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({})
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    // Toggle nút like
                    console.log(data)
                    buttonEl.classList.toggle('active');

                    // Cập nhật số like hiển thị
                    const likeCountSpan = buttonEl.querySelector('.like-count');
                    if (likeCountSpan && data.likes_count !== undefined) {
                        likeCountSpan.textContent = data.likes_count;
                    }



                    // 💥 Hiệu ứng trái tim nhảy lên
                    const heart = document.createElement('span');
                    heart.textContent = '❤️';
                    heart.style.position = 'absolute';
                    heart.style.fontSize = '1.5rem';
                    heart.style.animation = 'floatUp 1s ease-out';
                    heart.style.left = `${buttonEl.offsetLeft + 10}px`;
                    heart.style.top = `${buttonEl.offsetTop - 10}px`;
                    document.body.appendChild(heart);
                    setTimeout(() => heart.remove(), 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi like!');
                });
        });
    });
});


