let currentPosition = 0;



document.addEventListener('DOMContentLoaded', function() {
    // Tạo modal xem ảnh/video phóng to
    const body = document.querySelector('body');

    if (!document.querySelector('.media-lightbox')) {
        const lightbox = document.createElement('div');
        lightbox.className = 'media-lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <div class="lightbox-media-container">
                    <!-- Sẽ chứa ảnh hoặc video tùy theo loại media -->
                </div>
                <div class="lightbox-nav">
                    <button class="lightbox-prev">&lt;</button>
                    <span class="lightbox-counter">1/5</span>
                    <button class="lightbox-next">&gt;</button>
                </div>
            </div>
        `;
        body.appendChild(lightbox);

        // Thêm CSS cho lightbox
        const style = document.createElement('style');
        style.textContent = `
            .media-lightbox {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.9);
                z-index: 1000;
                overflow: hidden;
            }

            .lightbox-content {
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .lightbox-media-container {
                max-height: 85vh;
                max-width: 85vw;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .lightbox-image, .lightbox-video {
                max-height: 85vh;
                max-width: 85vw;
                object-fit: contain;
            }

            .lightbox-close {
                position: absolute;
                top: 20px;
                right: 30px;
                color: white;
                font-size: 40px;
                cursor: pointer;
                z-index: 1010;
            }

            .lightbox-nav {
                position: absolute;
                bottom: 30px;
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 20px;
            }

            .lightbox-prev, .lightbox-next {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                font-size: 20px;
                cursor: pointer;
            }

            .lightbox-counter {
                color: white;
                font-size: 16px;
            }
        `;
        document.head.appendChild(style);

        // Biến lưu trữ
        let currentMedia = [];
        let currentIndex = 0;

        // Xử lý click vào ảnh hoặc video overlay
        document.addEventListener('click', function(e) {
            let targetElem = null;
            let mediaType = null;

            // Kiểm tra xem đã click vào ảnh, overlay của video hoặc vùng quanh video
            if (e.target.classList.contains('post-image')) {
                targetElem = e.target;
                mediaType = 'image';
            } else if (e.target.classList.contains('media-overlay') || e.target.classList.contains('fa-play-circle')) {
                // Click vào overlay hoặc icon play
                targetElem = e.target.closest('.media-item.video-item');
                mediaType = 'video';
            } else if (e.target.classList.contains('image-item')) {
                // Click vào container của ảnh
                targetElem = e.target.querySelector('.post-image');
                mediaType = 'image';
            } else if (e.target.classList.contains('video-item')) {
                // Click vào container của video
                targetElem = e.target;
                mediaType = 'video';
            }

            if (targetElem) {
                e.preventDefault();

                // Lấy tất cả media trong post
                const mediaContainer = targetElem.closest('.post-media-container');

                // Lấy tất cả các item media (cả ảnh và video)
                const allMediaItems = Array.from(mediaContainer.querySelectorAll('.media-item'));

                // Lưu thông tin từng media (loại và đường dẫn)
                currentMedia = allMediaItems.map(item => {
                    if (item.classList.contains('image-item')) {
                        const img = item.querySelector('.post-image');
                        return { type: 'image', src: img.src };
                    } else if (item.classList.contains('video-item')) {
                        const video = item.querySelector('video');
                        return { type: 'video', src: video.querySelector('source').src || video.src };
                    }
                    return null;
                }).filter(item => item !== null);

                // Tìm index của media được click
                let clickedIndex = 0;
                if (mediaType === 'image') {
                    const imageItems = Array.from(mediaContainer.querySelectorAll('.image-item'));
                    const imageItem = targetElem.closest('.image-item') || targetElem;
                    clickedIndex = allMediaItems.indexOf(imageItem);
                } else if (mediaType === 'video') {
                    const videoItem = targetElem.closest('.video-item') || targetElem;
                    clickedIndex = allMediaItems.indexOf(videoItem);
                }

                currentIndex = clickedIndex;

                // Hiển thị media trong lightbox
                openMediaInLightbox(currentMedia[currentIndex], currentIndex);
            }
        });

        // Hàm hiển thị media trong lightbox
        function openMediaInLightbox(media, index) {
            const lightbox = document.querySelector('.media-lightbox');
            const mediaContainer = document.querySelector('.lightbox-media-container');
            const counter = document.querySelector('.lightbox-counter');

            // Xóa nội dung cũ
            mediaContainer.innerHTML = '';

            // Thêm media mới vào container
            if (media.type === 'image') {
                const img = document.createElement('img');
                img.className = 'lightbox-image';
                img.src = media.src;
                mediaContainer.appendChild(img);
            } else {
                const video = document.createElement('video');
                video.className = 'lightbox-video';
                video.src = media.src;
                video.controls = true;
                video.autoplay = true;
                mediaContainer.appendChild(video);
            }

            counter.textContent = `${index + 1}/${currentMedia.length}`;
            lightbox.style.display = 'block';

            // Vô hiệu hóa scroll trên body
            document.body.style.overflow = 'hidden';
        }

        // Đóng lightbox
        document.querySelector('.lightbox-close').addEventListener('click', function() {
            const lightbox = document.querySelector('.media-lightbox');
            const videos = lightbox.querySelectorAll('video');

            // Dừng tất cả video đang chạy
            videos.forEach(video => {
                video.pause();
            });

            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        });

        // Nút prev
        document.querySelector('.lightbox-prev').addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + currentMedia.length) % currentMedia.length;
            openMediaInLightbox(currentMedia[currentIndex], currentIndex);
        });

        // Nút next
        document.querySelector('.lightbox-next').addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % currentMedia.length;
            openMediaInLightbox(currentMedia[currentIndex], currentIndex);
        });

        // Đóng khi click ra ngoài
        document.querySelector('.media-lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                const videos = this.querySelectorAll('video');

                // Dừng tất cả video đang chạy
                videos.forEach(video => {
                    video.pause();
                });

                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Phím tắt
        document.addEventListener('keydown', function(e) {
            const lightbox = document.querySelector('.media-lightbox');
            if (lightbox.style.display === 'block') {
                if (e.key === 'Escape') {
                    const videos = lightbox.querySelectorAll('video');

                    // Dừng tất cả video đang chạy
                    videos.forEach(video => {
                        video.pause();
                    });

                    lightbox.style.display = 'none';
                    document.body.style.overflow = '';
                } else if (e.key === 'ArrowLeft') {
                    document.querySelector('.lightbox-prev').click();
                } else if (e.key === 'ArrowRight') {
                    document.querySelector('.lightbox-next').click();
                }
            }
        });
    }
});

window.openShareForm = function(postId) {
    const form = document.getElementById(`share-form-${postId}`);
    if (form) {
        form.classList.toggle('hidden');
    }
};


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
                        return response.json().then(errData => {
                            console.error('Lỗi từ server:', errData);
                            throw new Error(errData.error || "Network response was not ok");
                        });
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
                    alert('Có lỗi xảy ra: ' + error.message);
                });
        });
    });
});

//JS cho xuất hiện modal danh sách người đã like post
document.addEventListener('DOMContentLoaded', function() {
    const viewLikesButtons = document.querySelectorAll('.view-likes-btn');
    const likesModal = document.getElementById('likes-modal');
    const likesList = document.getElementById('likes-list');
    const closeButton = document.querySelector('.close-button');

    viewLikesButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const baseUrl = window.location.origin + '/social-network/public';
            fetch(`${baseUrl}/post/${postId}/likes`) // Gọi API để lấy danh sách người thích
                .then(response => response.json())
                .then(data => {
                    likesList.innerHTML = ''; // Xóa danh sách cũ
                    if (data.length > 0) {
                        data.forEach(user => {
                            const listItem = document.createElement('li');
                            listItem.textContent = user.first_name + ' ' + user.last_name; // Hoặc thông tin người dùng khác
                            likesList.appendChild(listItem);
                        });
                        likesModal.style.display = 'flex'; // Hiển thị modal
                    } else {
                        const listItem = document.createElement('li');
                        listItem.textContent = 'Chưa có ai thích bài viết này.';
                        likesList.appendChild(listItem);
                        likesModal.style.display = 'flex';
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải danh sách người thích:', error);
                    likesList.innerHTML = 'Có lỗi xảy ra khi tải danh sách.';
                    likesModal.style.display = 'flex';
                });
        });
    });

    closeButton.addEventListener('click', function() {
        likesModal.style.display = 'none'; // Ẩn modal
    });

    window.addEventListener('click', function(event) {
        if (event.target === likesModal) {
            likesModal.style.display = 'none'; // Ẩn modal khi nhấp ra ngoài
        }
    });
});
