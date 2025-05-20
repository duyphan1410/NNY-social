// Tích hợp file JavaScript cho edit.blade.php
document.addEventListener("DOMContentLoaded", function () {
    // Biến lưu trữ các hình ảnh và video đã bị xóa
    const removedImages = new Set();
    const removedVideos = new Set();

    // Input fields cho hình ảnh và video đã bị xóa
    const removeImagesInput = document.getElementById("remove_images");
    const removeVideosInput = document.getElementById("remove_videos");

    // Giới hạn số lượng file
    const MAX_IMAGES = 5;
    const MAX_VIDEOS = 3;

    // Khởi tạo các biến
    const imageInput = document.getElementById('images');
    const videoInput = document.getElementById('videos');
    const imagePreview = document.getElementById('image-preview');
    const videoPreview = document.getElementById('video-preview');
    const contentTextarea = document.getElementById('content');

    // Tạo custom upload UI
    setupCustomFileUpload(imageInput, `Kéo thả hoặc chọn ảnh (tối đa ${MAX_IMAGES})`, 'fa-image');
    setupCustomFileUpload(videoInput, `Kéo thả hoặc chọn video (tối đa ${MAX_VIDEOS})`, 'fa-video');

    // Mảng lưu trữ các file đã chọn - đặt ở phạm vi toàn cục cho window
    window.selectedImages = [];
    window.selectedVideos = [];

    // Xử lý các hình ảnh hiện tại
    document.querySelectorAll(".remove-current-image").forEach((btn) => {
        btn.addEventListener("click", () => {
            const imageId = btn.getAttribute("data-id");
            removedImages.add(imageId);
            document.getElementById("current-image-" + imageId).remove();
            removeImagesInput.value = Array.from(removedImages).join(",");
        });
    });

    // Xử lý các video hiện tại
    document.querySelectorAll(".remove-current-video").forEach((btn) => {
        btn.addEventListener("click", () => {
            const videoId = btn.getAttribute("data-id");
            removedVideos.add(videoId);
            document.getElementById("current-video-" + videoId).remove();
            removeVideosInput.value = Array.from(removedVideos).join(",");
        });
    });

    // Cài đặt xử lý khi chọn file ảnh
    imageInput.addEventListener('change', function(event) {
        let newFiles = Array.from(event.target.files);

        // Đếm số lượng ảnh hiện tại (không bị xóa)
        const currentImageCount = document.querySelectorAll("#current-image-preview .preview-item").length;

        // Kiểm tra giới hạn số lượng ảnh
        if (currentImageCount + window.selectedImages.length + newFiles.length > MAX_IMAGES) {
            alert(`Bạn chỉ có thể có tối đa ${MAX_IMAGES} ảnh (bao gồm cả ảnh hiện tại).`);
            return;
        }

        // Thêm các file mới vào mảng đã chọn
        window.selectedImages = window.selectedImages.concat(newFiles);

        // Cập nhật UI và đồng bộ files
        syncFiles(this, window.selectedImages);
        updateFilePreview(imagePreview, window.selectedImages, 'image', this);
    });

    // Cài đặt xử lý khi chọn file video
    videoInput.addEventListener('change', function(event) {
        let newFiles = Array.from(event.target.files);

        // Đếm số lượng video hiện tại (không bị xóa)
        const currentVideoCount = document.querySelectorAll("#current-video-preview .preview-item").length;

        // Kiểm tra giới hạn số lượng video
        if (currentVideoCount + window.selectedVideos.length + newFiles.length > MAX_VIDEOS) {
            alert(`Bạn chỉ có thể có tối đa ${MAX_VIDEOS} video (bao gồm cả video hiện tại).`);
            return;
        }

        // Thêm các file mới vào mảng đã chọn
        window.selectedVideos = window.selectedVideos.concat(newFiles);

        // Cập nhật UI và đồng bộ files
        syncFiles(this, window.selectedVideos);
        updateFilePreview(videoPreview, window.selectedVideos, 'video', this);
    });

    // Thêm hiệu ứng tự động mở rộng cho textarea
    autoResizeTextarea(contentTextarea);

    // Xử lý drag và drop cho ảnh và video
    setupDragAndDrop(imageInput, MAX_IMAGES);
    setupDragAndDrop(videoInput, MAX_VIDEOS);

    // Xử lý gửi form
    setupFormSubmission();
});

// Hàm tạo custom file upload UI
function setupCustomFileUpload(inputElement, placeholderText, iconClass) {
    // Tạo wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'custom-file-upload';

    // Thêm icon
    const icon = document.createElement('i');
    icon.className = 'fas ' + iconClass;
    wrapper.appendChild(icon);

    // Thêm text
    const text = document.createElement('span');
    text.textContent = placeholderText;
    wrapper.appendChild(text);

    // Thêm wrapper vào trước input
    inputElement.parentNode.insertBefore(wrapper, inputElement);
    wrapper.appendChild(inputElement);
}

// Hàm cập nhật số lượng file đã chọn trong UI
function updateSelectedFileCount(inputElement, files) {
    const displayText = inputElement.parentNode.querySelector('span');
    const fileCount = files.length;

    if (fileCount > 0) {
        displayText.textContent = fileCount + ' file được chọn';
    } else {
        if (inputElement.id === 'images') {
            displayText.textContent = 'Kéo thả hoặc chọn ảnh (tối đa 5)';
        } else {
            displayText.textContent = 'Kéo thả hoặc chọn video (tối đa 3)';
        }
    }
}

// Xử lý khi chọn file và hiển thị preview
function updateFilePreview(previewContainer, files, type, inputElement) {
    previewContainer.innerHTML = '';
    previewContainer.className = 'preview-container';

    if (files.length > 0) {
        inputElement.parentNode.classList.add('pulse-animation');
        setTimeout(() => {
            inputElement.parentNode.classList.remove('pulse-animation');
        }, 500);
    }

    // Tạo và hiển thị preview cho từng file
    files.forEach((file, index) => {
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.dataset.index = index; // Thêm chỉ số của file vào dataset

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            previewItem.appendChild(img);
        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.setAttribute('controls', 'controls');
            previewItem.appendChild(video);
        }

        // Nút xóa file
        const removeBtn = document.createElement('button');
        removeBtn.className = 'preview-remove';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Xóa file khỏi mảng
            if (type === 'image') {
                // Xóa file tại index được lưu trong dataset
                window.selectedImages.splice(parseInt(previewItem.dataset.index), 1);
                // Cập nhật lại input và preview
                syncFiles(inputElement, window.selectedImages);
                updateFilePreview(previewContainer, window.selectedImages, type, inputElement);
            } else {
                // Xóa file tại index được lưu trong dataset
                window.selectedVideos.splice(parseInt(previewItem.dataset.index), 1);
                // Cập nhật lại input và preview
                syncFiles(inputElement, window.selectedVideos);
                updateFilePreview(previewContainer, window.selectedVideos, type, inputElement);
            }
        });

        previewItem.appendChild(removeBtn);
        previewContainer.appendChild(previewItem);
    });
}

// Hàm đồng bộ files từ mảng vào input element
function syncFiles(inputElement, files) {
    let dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    inputElement.files = dataTransfer.files;
    console.log(`🔄 Đồng bộ input ${inputElement.id} - Số file:`, inputElement.files.length);

    // Cập nhật text hiển thị
    updateSelectedFileCount(inputElement, files);
}

// Thiết lập tự động mở rộng textarea theo nội dung
function autoResizeTextarea(textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Khởi tạo ban đầu
    setTimeout(function() {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }, 100);
}

// Thiết lập drag và drop
function setupDragAndDrop(inputElement, maxFiles) {
    const dropArea = inputElement.parentNode;
    const type = inputElement.id === 'images' ? 'image' : 'video';
    const previewContainer = document.getElementById(inputElement.id === 'images' ? 'image-preview' : 'video-preview');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('highlight-drop');
    }

    function unhighlight() {
        dropArea.classList.remove('highlight-drop');
    }

    dropArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const droppedFiles = Array.from(dt.files);

        // Đếm số lượng file hiện tại (không bị xóa)
        const currentFileCount = type === 'image'
            ? document.querySelectorAll("#current-image-preview .preview-item").length
            : document.querySelectorAll("#current-video-preview .preview-item").length;

        // Kiểm tra giới hạn số lượng
        const filesArray = type === 'image' ? window.selectedImages : window.selectedVideos;

        if (currentFileCount + filesArray.length + droppedFiles.length > maxFiles) {
            alert(`Bạn chỉ có thể có tối đa ${maxFiles} ${type === 'image' ? 'ảnh' : 'video'} (bao gồm cả ${type === 'image' ? 'ảnh' : 'video'} hiện tại).`);
            return;
        }

        // Thêm các file mới vào mảng
        if (type === 'image') {
            window.selectedImages = window.selectedImages.concat(droppedFiles);
            syncFiles(inputElement, window.selectedImages);
            updateFilePreview(previewContainer, window.selectedImages, type, inputElement);
        } else {
            window.selectedVideos = window.selectedVideos.concat(droppedFiles);
            syncFiles(inputElement, window.selectedVideos);
            updateFilePreview(previewContainer, window.selectedVideos, type, inputElement);
        }
    }, false);
}

// Thiết lập gửi form với hiệu ứng loading
function setupFormSubmission() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        // Kiểm tra validate trước khi submit
        if (!form.checkValidity()) {
            return;
        }

        e.preventDefault();

        // Đảm bảo đồng bộ files trước khi submit
        syncFiles(document.getElementById('images'), window.selectedImages);
        syncFiles(document.getElementById('videos'), window.selectedVideos);

        // Hiển thị loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
        submitBtn.disabled = true;

        // Submit form sau 1s
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
}
