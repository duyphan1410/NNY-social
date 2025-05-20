// detail.js
let activeTextarea = null;
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
    const commentForms = document.querySelectorAll('.comment-form');

    // Khởi tạo mentionMap cho mỗi textarea
    document.querySelectorAll('.comment-form textarea').forEach(textarea => {
        // Khởi tạo mentionMap để lưu trữ ID người dùng
        textarea.mentionMap = {};
    });

    replyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const authorName = this.dataset.author;
            const userId = this.dataset.userId;

            // Lấy form chung bên dưới comment-list
            const commonForm = document.querySelector('.comment-form');
            if (!commonForm) {
                console.error('Không tìm thấy form comment chung');
                return;
            }

            const textarea = commonForm.querySelector('textarea[name="content"]');
            if (!textarea) {
                console.error('Không tìm thấy textarea trong form comment');
                return;
            }

            // Chèn @Tên người dùng
            const mentionText = `@${authorName}`;
            const fullText = `${mentionText} `;
            textarea.value = fullText;

            // Thiết lập vị trí bắt đầu mention là 0
            const currentMentionStart = 0;

            // Khởi tạo mentionMap nếu chưa có
            if (!textarea.mentionMap) {
                textarea.mentionMap = {};
            }

            // Lưu trữ ID của người dùng trong mentionMap
            textarea.mentionMap[currentMentionStart] = {
                display: mentionText,
                storage: `@[${authorName}](user:${userId})`,
                start: 0,
                end: mentionText.length
            };


            // Gắn dữ liệu mention nếu cần dùng sau
            textarea.setAttribute('data-mention-name', authorName);
            textarea.setAttribute('data-mention-id', userId);

            textarea.selectionStart = textarea.selectionEnd = fullText.length;

            // Nếu bạn có logic xử lý mention autocomplete thì gọi lại ở đây
            if (typeof checkAndTriggerMentionLogic === 'function') {
                checkAndTriggerMentionLogic(textarea);
            }
        });
    });


    // Tự động resize textarea và xử lý mention
    document.querySelectorAll('.comment-form textarea').forEach(textarea => {
        // Khởi tạo mentionMap nếu chưa có
        if (!textarea.mentionMap) {
            textarea.mentionMap = {};
        }

        textarea.addEventListener('input', function() {
            // Resize textarea
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.scrollHeight > 520) {
                this.style.height = '520px';
            }

            // Lưu textarea hiện tại
            activeTextarea = this;

            // Kiểm tra và kích hoạt mention
            checkAndTriggerMentionLogic(this);
        });

        // Xử lý sự kiện focus để cập nhật activeTextarea
        textarea.addEventListener('focus', function() {
            activeTextarea = this;
        });
    });

    // Validate & xử lý mention trước khi submit
    commentForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const textarea = this.querySelector('textarea[name="content"]');
            const content = textarea.value.trim();

            if (!content) {
                event.preventDefault();
                alert('Vui lòng nhập nội dung bình luận!');
                return;
            }

            // Xử lý mentions trước khi submit
            const finalContent = processMentionsBeforeSubmit(textarea);
            textarea.value = finalContent;

            // Xóa phần tử gợi ý nếu đang hiển thị
            hideMentionSuggestions();
        });
    });

    // Highlight những người được mention
    highlightMentionsInComments();

    // Các hiệu ứng tương tác
    addCommentInteractions();

    // Đóng gợi ý khi click ra ngoài
    document.addEventListener('click', function(event) {
        const mentionSuggestBox = document.getElementById('mention-suggestions');
        if (mentionSuggestBox && !mentionSuggestBox.contains(event.target) &&
            !event.target.classList.contains('comment-form-textarea')) {
            hideMentionSuggestions();
        }
    });
});

// Highlight mention dạng @[Tên](user:ID)
function highlightMentionsInComments() {
    document.querySelectorAll('.comment-text').forEach(comment => {
        const content = comment.innerHTML;
        const mentionPattern = /\@\[(.+?)\]\(user:(\d+)\)/g;
        const baseUrl = window.location.origin;
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

let mentionTimeout; // Để debounce các request AJAX
let currentMentionQuery = ''; // Lưu trữ từ khóa tìm kiếm hiện tại
let currentMentionStart = -1; // Vị trí bắt đầu của @mention trong textarea

// Hàm kiểm tra và kích hoạt logic mention
function checkAndTriggerMentionLogic(textarea) {
    const text = textarea.value;
    const cursorPosition = textarea.selectionStart;

    // Tìm vị trí của '@' gần nhất trước con trỏ
    const atIndex = text.lastIndexOf('@', cursorPosition - 1);

    if (atIndex !== -1) {
        // Kiểm tra xem @ có ở đầu văn bản hoặc có khoảng trắng phía trước không
        const isValidAt = atIndex === 0 || (atIndex > 0 && /\s/.test(text[atIndex - 1]));

        if (isValidAt) {
            const query = text.substring(atIndex + 1, cursorPosition);
            // Kiểm tra xem query có chứa khoảng trắng hoặc không hợp lệ không
            if (query.includes('\n')) {
                hideMentionSuggestions();
                return;
            }

            currentMentionQuery = query;
            currentMentionStart = atIndex;

            clearTimeout(mentionTimeout);
            mentionTimeout = setTimeout(() => {
                fetchMentions(query, textarea);
            }, 300); // Debounce 300ms
        } else {
            hideMentionSuggestions();
        }
    } else {
        hideMentionSuggestions();
    }
}

// Hàm ẩn danh sách gợi ý
function hideMentionSuggestions() {
    const existingSuggestBox = document.getElementById('mention-suggestions');
    if (existingSuggestBox) {
        existingSuggestBox.remove();
    }
    currentMentionQuery = '';
    currentMentionStart = -1;
}

// Hàm tạo và hiển thị danh sách gợi ý
function showMentionSuggestions(users, textarea) {
    let existingSuggestBox = document.getElementById('mention-suggestions');
    if (existingSuggestBox) {
        existingSuggestBox.remove(); // Xóa cái cũ nếu có
    }

    if (users.length === 0) {
        return; // Không có gợi ý thì không hiển thị gì cả
    }

    const suggestBox = document.createElement('ul');
    suggestBox.id = 'mention-suggestions';
    suggestBox.className = 'absolute z-10 bg-white border border-gray-300 rounded shadow-lg mt-1 w-full max-h-48 overflow-y-auto'; // Tailwind classes

    users.forEach(user => {
        const li = document.createElement('li');
        li.className = 'p-2 hover:bg-gray-100 cursor-pointer text-gray-800 flex items-center';
        li.dataset.userId = user.id;
        li.dataset.userName = user.name;

        // Thêm avatar (tùy chọn)
        if (user.avatar_url) {
            const avatar = document.createElement('img');
            avatar.src = user.avatar_url;
            avatar.alt = user.name;
            avatar.className = 'w-6 h-6 rounded-full mr-2';
            li.appendChild(avatar);
        }

        const nameSpan = document.createElement('span');
        nameSpan.textContent = user.name;
        li.appendChild(nameSpan);

        li.addEventListener('click', function () {
            const selectedName = this.dataset.userName;
            const selectedId = this.dataset.userId;

            const text = textarea.value;
            const beforeMention = text.substring(0, currentMentionStart);
            const afterMention = text.substring(textarea.selectionEnd);

            const mentionText = `@${selectedName}`;
            const newText = `${beforeMention}${mentionText} ${afterMention}`;

            textarea.value = newText;

            if (!textarea.mentionMap) {
                textarea.mentionMap = {};
            }

            // Tạo một đối tượng để lưu chi tiết về mention
            textarea.mentionMap[currentMentionStart] = {
                display: mentionText,
                storage: `@[${selectedName}](user:${selectedId})`,
                start: currentMentionStart,
                end: currentMentionStart + mentionText.length
            };

            const newCursorPosition = beforeMention.length + mentionText.length + 1;
            textarea.selectionStart = textarea.selectionEnd = newCursorPosition;

            hideMentionSuggestions();
            textarea.focus();
        });

        suggestBox.appendChild(li);
    });

    // Định vị phía trên textarea
    const rect = textarea.getBoundingClientRect();
    const scrollTop = window.scrollY || document.documentElement.scrollTop;

    // Tính toán chiều cao của suggestBox sau khi render
    document.body.appendChild(suggestBox);
    const suggestBoxHeight = suggestBox.offsetHeight;

    // Cập nhật vị trí
    suggestBox.style.left = `${rect.left + window.scrollX}px`;
    suggestBox.style.top = `${rect.top + scrollTop - suggestBoxHeight - 4}px`; // cách textarea 4px
    suggestBox.style.width = `${rect.width}px`;
}

// Hàm gửi yêu cầu AJAX tìm kiếm người dùng
function fetchMentions(query, textarea) {
    if (query.length < 1) { // Bắt đầu tìm kiếm khi có ít nhất 1 ký tự sau @
        hideMentionSuggestions();
        return;
    }

    // Endpoint API tìm kiếm người dùng
    fetch(`http://localhost:8000/api/users/search?q=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',  // Để Laravel nhận diện AJAX request
            'Accept': 'application/json'           // Yêu cầu response dạng JSON
        },
        credentials: 'include'  // Gửi kèm cookie xác thực
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (currentMentionQuery === query && currentMentionStart !== -1) { // Đảm bảo kết quả tương ứng với truy vấn hiện tại
                showMentionSuggestions(data, textarea);
            }
        })
        .catch(error => {
            console.error('Error fetching mentions:', error);
            hideMentionSuggestions();
        });
}

// Hàm xử lý mention trước khi submit form
function processMentionsBeforeSubmit(textarea) {
    let content = textarea.value;
    const map = textarea.mentionMap || {};

    // Sắp xếp các mention theo vị trí bắt đầu giảm dần (để không làm sai offset)
    const mentions = Object.values(map).sort((a, b) => b.start - a.start);

    mentions.forEach(m => {
        content = content.slice(0, m.start) + m.storage + content.slice(m.end);
    });

    textarea.mentionMap = {};
    return content.trim();
}

document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#comments-')) {
        const target = document.querySelector(hash);
        if (target) {
            const offset = 100; // Khoảng cách từ top (pixels)
            const elementPosition = target.getBoundingClientRect().top + window.scrollY;
            window.scrollTo({
                top: elementPosition - offset,
                behavior: 'smooth'
            });
        }
    }
});
