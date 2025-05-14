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

document.addEventListener('DOMContentLoaded', function() {
    const replyButtons = document.querySelectorAll('.reply-btn');
    const commentTextarea = document.querySelector('.comment-form textarea[name="content"]');

    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const authorName = this.dataset.author;
            commentTextarea.value = `@${authorName} `;
            commentTextarea.focus(); // Tự động focus vào textarea để người dùng nhập tiếp
        });
    });
});
