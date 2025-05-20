// detail.js
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý dropdown
    document.querySelectorAll(".dropdown-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();
            let parent = this.closest(".dropdown");
            parent.classList.toggle("active");
        });
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener("click", function (event) {
        if (!event.target.closest(".dropdown-menu") && !event.target.closest(".delete-form")) {
            document.querySelectorAll(".dropdown").forEach(dropdown => {
                dropdown.classList.remove("active");
            });
        }
    });

    // Xử lý form xóa
    document.querySelectorAll(".delete-form").forEach(form => {
        form.addEventListener("click", function(event) {
            event.stopPropagation();
        });

        const deleteButton = form.querySelector('button[type="submit"]');
        if (deleteButton) {
            deleteButton.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                form.submit();
            });
        }
    });

    // Xử lý nút trả lời comment
    const replyButtons = document.querySelectorAll('.reply-btn');
    const commentTextarea = document.querySelector('.comment-form textarea[name="content"]');

    replyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const authorName = this.dataset.author;
            const userId = this.dataset.userId;

            // Chỉ hiển thị @Tên Người Dùng trong textarea
            commentTextarea.value = `@${authorName} `;
            commentTextarea.focus();

            // Gắn dữ liệu để thay thế khi submit
            commentTextarea.setAttribute('data-mention-name', authorName);
            commentTextarea.setAttribute('data-mention-id', userId);

        });
    });

    // Tự động resize textarea
    document.querySelectorAll('.comment-form textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.scrollHeight > 520) {
                this.style.height = '520px';
            }
        });
    });

    // Validate & xử lý mention trước khi submit
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            const textarea = this.querySelector('textarea[name="content"]');
            const content = textarea.value.trim();

            if (!content) {
                event.preventDefault();
                alert('Vui lòng nhập nội dung bình luận!');
                return;
            }

            const mentionName = textarea.getAttribute('data-mention-name');
            const mentionId = textarea.getAttribute('data-mention-id');

            if (mentionName && mentionId) {
                const regex = new RegExp(`@${mentionName}`);
                const replaced = content.replace(regex, `@[${mentionName}](user:${mentionId})`);
                textarea.value = replaced;

                // Xoá thuộc tính tạm
                textarea.removeAttribute('data-mention-name');
                textarea.removeAttribute('data-mention-id');
            }
        });
    });

    // Highlight những người được mention
    highlightMentionsInComments();

    // Các hiệu ứng tương tác
    addCommentInteractions();
});

// Highlight mention dạng @[Tên](user:ID)
function highlightMentionsInComments() {
    document.querySelectorAll('.comment-text').forEach(comment => {
        const content = comment.textContent;
        const mentionPattern = /\@\[(.+?)\]\(user:(\d+)\)/g;
        const baseUrl = window.location.origin + '/social-network/public';
        const newContent = content.replace(mentionPattern, `<a href="${baseUrl}/profile/$2" class="mention">@$1</a>`);

        if (newContent !== content) {
            comment.innerHTML = newContent;
        }
    });
}

// Hiệu ứng tương tác cho comment
function addCommentInteractions() {
    document.querySelectorAll('.comment-bubble').forEach(bubble => {
        bubble.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#e4e6eb';
        });
        bubble.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#f0f2f5';
        });
    });

    document.querySelectorAll('.comment').forEach(comment => {
        comment.addEventListener('mouseenter', function() {
            const actionButtons = this.querySelectorAll('.reply-btn, .delete-btn');
            actionButtons.forEach(btn => btn.style.opacity = '1');
        });
        comment.addEventListener('mouseleave', function() {
            const actionButtons = this.querySelectorAll('.reply-btn, .delete-btn');
            actionButtons.forEach(btn => btn.style.opacity = '0.7');
        });
    });
}
