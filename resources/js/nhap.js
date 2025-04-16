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
