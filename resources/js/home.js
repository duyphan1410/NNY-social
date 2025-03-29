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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.reel-btn.prev').disabled = true;
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".dropdown-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();  // Ngăn chặn sự kiện click lan ra ngoài
            let parent = this.closest(".dropdown");
            parent.classList.toggle("active");
        });
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener("click", function () {
        document.querySelectorAll(".dropdown").forEach(dropdown => {
            dropdown.classList.remove("active");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-form").forEach(form => {
        form.addEventListener("submit", function (event) {
            let confirmDelete = confirm("Bạn có chắc chắn muốn xóa?");
            if (!confirmDelete) {
                event.preventDefault(); // Ngăn form gửi nếu chọn Hủy
            }
        });
    });
});
