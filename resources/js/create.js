// JavaScript cho create.js - Updated with Cloudinary Delete

document.addEventListener('DOMContentLoaded', function () {
    console.log('🔥 DOMContentLoaded bắt đầu');
    const MAX_IMAGES = 5;
    const MAX_VIDEOS = 3;

    const imageInput = document.getElementById('images');
    const videoInput = document.getElementById('videos');
    const imagePreview = document.getElementById('image-preview');
    const videoPreview = document.getElementById('video-preview');
    const contentTextarea = document.getElementById('content');

    // Lưu trữ cả file gốc và data đã upload
    window.selectedImages = [];
    window.selectedVideos = [];
    window.uploadedImageData = [];
    window.uploadedVideoData = [];

    // Validate required elements
    if (!imageInput || !videoInput || !imagePreview || !videoPreview) {
        console.error('❌ Thiếu elements cần thiết');
        return;
    }

    setupCustomFileUpload(imageInput, `Kéo thả hoặc chọn ảnh (tối đa ${MAX_IMAGES})`, 'fa-image');
    setupCustomFileUpload(videoInput, `Kéo thả hoặc chọn video (tối đa ${MAX_VIDEOS})`, 'fa-video');

    // Image input handler
    imageInput.addEventListener('change', async function (event) {
        let newFiles = Array.from(event.target.files);

        if (window.selectedImages.length + newFiles.length > MAX_IMAGES) {
            alert(`Bạn chỉ được chọn tối đa ${MAX_IMAGES} ảnh.`);
            this.value = '';
            return;
        }

        showLoadingForInput(this, 'Đang xử lý ảnh...');

        try {
            window.selectedImages = window.selectedImages.concat(newFiles);
            updateFilePreview(imagePreview, window.selectedImages, 'image', this);

            // Upload files
            for (let file of newFiles) {
                try {
                    const resizedFile = await resizeImage(file, 1024);
                    const uploaded = await uploadImageToCloudinary(resizedFile);
                    window.uploadedImageData.push(uploaded);
                } catch (error) {
                    console.error('Lỗi upload ảnh:', error);
                    window.uploadedImageData.push({
                        file: file,
                        url: null,
                        public_id: null,
                        error: true
                    });
                }
            }

            updateHiddenInput('image-data', window.uploadedImageData);
        } catch (error) {
            console.error('Lỗi xử lý ảnh:', error);
            alert('Có lỗi xảy ra khi xử lý ảnh!');
        }

        hideLoadingForInput(this);
    });

    // Video input handler
    videoInput.addEventListener('change', async function (event) {
        let newFiles = Array.from(event.target.files);

        if (window.selectedVideos.length + newFiles.length > MAX_VIDEOS) {
            alert(`Bạn chỉ được chọn tối đa ${MAX_VIDEOS} video.`);
            this.value = '';
            return;
        }

        showLoadingForInput(this, 'Đang xử lý video...');

        try {
            window.selectedVideos = window.selectedVideos.concat(newFiles);
            updateFilePreview(videoPreview, window.selectedVideos, 'video', this);

            // Upload files
            for (let file of newFiles) {
                try {
                    const uploaded = await uploadVideoToCloudinary(file);
                    window.uploadedVideoData.push(uploaded);
                } catch (error) {
                    console.error('Lỗi upload video:', error);
                    window.uploadedVideoData.push({
                        file: file,
                        url: null,
                        public_id: null,
                        error: true
                    });
                }
            }

            updateHiddenInput('video-data', window.uploadedVideoData);
        } catch (error) {
            console.error('Lỗi xử lý video:', error);
            alert('Có lỗi xảy ra khi xử lý video!');
        }

        hideLoadingForInput(this);
    });

    // Setup additional features
    if (contentTextarea) {
        autoResizeTextarea(contentTextarea);
    }

    setupDragAndDrop(imageInput, MAX_IMAGES);
    setupDragAndDrop(videoInput, MAX_VIDEOS);
    setupFormSubmission();

    console.log('✅ Initialization complete');
});

// =============== CLOUDINARY CONFIGURATION ===============
const CLOUDINARY_CONFIG = {
    cloudName: 'dwvt3snha',
    unsigned: {
        enabled: true,
        imagePreset: 'unsigned_post_images',
        videoPreset: 'unsigned_post_videos'
    },
    preferredMethod: 'unsigned'
};

// =============== UPLOAD FUNCTIONS ===============

async function uploadUnsigned(file, resourceType, folder) {
    const preset = resourceType === 'image' ?
        CLOUDINARY_CONFIG.unsigned.imagePreset :
        CLOUDINARY_CONFIG.unsigned.videoPreset;

    const url = `https://api.cloudinary.com/v1_1/${CLOUDINARY_CONFIG.cloudName}/${resourceType}/upload`;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', preset);
    formData.append('folder', folder);

    if (resourceType === 'image') {
        formData.append('quality', 'auto');
        formData.append('fetch_format', 'auto');
    }

    const response = await fetch(url, {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        throw new Error(`Upload failed: ${response.status}`);
    }

    const data = await response.json();
    if (data.error) {
        throw new Error(data.error.message);
    }

    return {
        file: file,
        url: data.secure_url,
        public_id: data.public_id,
        method: 'unsigned',
        original_filename: file.name
    };
}

async function uploadImageToCloudinary(file) {
    return await uploadUnsigned(file, 'image', 'post_images');
}

async function uploadVideoToCloudinary(file) {
    return await uploadUnsigned(file, 'video', 'post_videos');
}

// =============== CLOUDINARY DELETE FUNCTION ===============

async function deleteFromCloudinary(publicId, resourceType) {
    try {
        console.log(`🗑️ Deleting from Cloudinary: ${publicId} (${resourceType})`);

        const response = await fetch('/social-network/public/cloudinary/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                public_id: publicId,
                resource_type: resourceType
            })
        });

        if (!response.ok) {
            throw new Error(`Delete request failed: ${response.status}`);
        }

        const data = await response.json();
        console.log(`✅ Delete response:`, data);
        return data.success;
    } catch (error) {
        console.error('❌ Error deleting from Cloudinary:', error);
        return false;
    }
}

// =============== HELPER FUNCTIONS ===============

function showLoadingForInput(inputElement, message) {
    const wrapper = inputElement.closest('.custom-file-upload');
    if (wrapper) {
        const displayText = wrapper.querySelector('span');
        if (displayText) {
            displayText.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${message}`;
        }
    }
    inputElement.disabled = true;
}

function hideLoadingForInput(inputElement) {
    const wrapper = inputElement.closest('.custom-file-upload');
    if (wrapper) {
        const displayText = wrapper.querySelector('span');
        if (displayText) {
            const files = inputElement.id === 'images' ? window.selectedImages : window.selectedVideos;
            updateSelectedFileCount(inputElement, files);
        }
    }
    inputElement.disabled = false;
}

async function resizeImage(file, maxSize) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const reader = new FileReader();

        reader.onerror = () => reject(new Error('Không thể đọc file'));
        reader.onload = function (e) {
            img.src = e.target.result;
        };

        img.onerror = () => reject(new Error('Không thể load ảnh'));
        img.onload = function () {
            try {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > maxSize || height > maxSize) {
                    if (width > height) {
                        height = Math.round((maxSize / width) * height);
                        width = maxSize;
                    } else {
                        width = Math.round((maxSize / height) * width);
                        height = maxSize;
                    }
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function (blob) {
                    if (blob) {
                        const resizedFile = new File([blob], file.name, {
                            type: 'image/webp',
                            lastModified: Date.now()
                        });
                        resolve(resizedFile);
                    } else {
                        reject(new Error('Không thể resize ảnh'));
                    }
                }, 'image/webp', 0.8);
            } catch (error) {
                reject(error);
            }
        };

        reader.readAsDataURL(file);
    });
}

function updateHiddenInput(id, dataArray) {
    const hiddenInput = document.getElementById(id);
    if (hiddenInput) {
        const data = dataArray
            .filter(i => !i.error)
            .map(i => ({
                url: i.url,
                public_id: i.public_id,
                method: i.method || 'unsigned'
            }));
        hiddenInput.value = JSON.stringify(data);
        console.log(`Hidden input ${id} updated:`, data);
    }
}

function setupCustomFileUpload(inputElement, placeholderText, iconClass) {
    const wrapper = document.createElement('div');
    wrapper.className = 'custom-file-upload';

    const icon = document.createElement('i');
    icon.className = 'fas ' + iconClass;
    wrapper.appendChild(icon);

    const text = document.createElement('span');
    text.textContent = placeholderText;
    wrapper.appendChild(text);

    inputElement.parentNode.insertBefore(wrapper, inputElement);
    wrapper.appendChild(inputElement);
}

function updateSelectedFileCount(inputElement, files) {
    const wrapper = inputElement.closest('.custom-file-upload');
    if (!wrapper) return;

    const displayText = wrapper.querySelector('span');
    if (!displayText) return;

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

function autoResizeTextarea(textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    setTimeout(function() {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }, 100);
}

function setupDragAndDrop(inputElement, maxFiles) {
    const wrapper = inputElement.closest('.custom-file-upload');
    if (!wrapper) return;

    const type = inputElement.id === 'images' ? 'image' : 'video';

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        wrapper.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        wrapper.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        wrapper.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        wrapper.classList.add('highlight-drop');
    }

    function unhighlight() {
        wrapper.classList.remove('highlight-drop');
    }

    wrapper.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const droppedFiles = Array.from(dt.files);

        const filesArray = type === 'image' ? window.selectedImages : window.selectedVideos;

        if (filesArray.length + droppedFiles.length > maxFiles) {
            alert(`Bạn chỉ có thể chọn tối đa ${maxFiles} ${type === 'image' ? 'ảnh' : 'video'}.`);
            return;
        }

        // Create new DataTransfer with dropped files
        const dataTransfer = new DataTransfer();
        droppedFiles.forEach(file => dataTransfer.items.add(file));
        inputElement.files = dataTransfer.files;

        // Trigger change event
        const changeEvent = new Event('change', { bubbles: true });
        inputElement.dispatchEvent(changeEvent);
    });
}

function updateFilePreview(previewContainer, files, type, inputElement) {
    if (!previewContainer) return;

    previewContainer.innerHTML = '';
    previewContainer.className = 'preview-container';

    if (files.length > 0) {
        const wrapper = inputElement.closest('.custom-file-upload');
        if (wrapper) {
            wrapper.classList.add('pulse-animation');
            setTimeout(() => {
                wrapper.classList.remove('pulse-animation');
            }, 500);
        }
    }

    updateSelectedFileCount(inputElement, files);

    files.forEach((file, index) => {
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.dataset.index = index;

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(this.src);
            };
            previewItem.appendChild(img);
        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.setAttribute('controls', 'controls');
            video.onloadeddata = function() {
                URL.revokeObjectURL(this.src);
            };
            previewItem.appendChild(video);
        }

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.className = 'preview-remove';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            // Disable button and show loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                await deleteFileFromPreview(index, type, previewContainer, inputElement);
            } catch (error) {
                console.error('Error deleting file:', error);
                alert('Có lỗi xảy ra khi xóa file!');
            } finally {
                // Re-enable button (if it still exists)
                if (this.parentNode) {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-times"></i>';
                }
            }
        });

        previewItem.appendChild(removeBtn);
        previewContainer.appendChild(previewItem);
    });
}

async function deleteFileFromPreview(index, type, previewContainer, inputElement) {
    console.log(`🗑️ Deleting file at index ${index} (${type})`);

    if (type === 'image') {
        // Remove from selected files array
        window.selectedImages.splice(index, 1);

        // Get the uploaded data and remove from array
        const removed = window.uploadedImageData.splice(index, 1)[0];

        // Delete from Cloudinary if it was uploaded
        if (removed?.public_id && !removed.error) {
            console.log(`🔄 Deleting image from Cloudinary: ${removed.public_id}`);
            const deleteSuccess = await deleteFromCloudinary(removed.public_id, 'image');
            if (deleteSuccess) {
                console.log('✅ Image deleted from Cloudinary successfully');
            } else {
                console.log('⚠️ Failed to delete image from Cloudinary');
            }
        }

        // Update UI and form data
        updateFilePreview(previewContainer, window.selectedImages, type, inputElement);
        updateHiddenInput('image-data', window.uploadedImageData);
        syncFiles(inputElement, window.selectedImages);
    } else {
        // Remove from selected files array
        window.selectedVideos.splice(index, 1);

        // Get the uploaded data and remove from array
        const removed = window.uploadedVideoData.splice(index, 1)[0];

        // Delete from Cloudinary if it was uploaded
        if (removed?.public_id && !removed.error) {
            console.log(`🔄 Deleting video from Cloudinary: ${removed.public_id}`);
            const deleteSuccess = await deleteFromCloudinary(removed.public_id, 'video');
            if (deleteSuccess) {
                console.log('✅ Video deleted from Cloudinary successfully');
            } else {
                console.log('⚠️ Failed to delete video from Cloudinary');
            }
        }

        // Update UI and form data
        updateFilePreview(previewContainer, window.selectedVideos, type, inputElement);
        updateHiddenInput('video-data', window.uploadedVideoData);
        syncFiles(inputElement, window.selectedVideos);
    }
}

function syncFiles(inputElement, files) {
    try {
        let dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        inputElement.files = dataTransfer.files;
    } catch (error) {
        console.error('Lỗi đồng bộ files:', error);
    }
}

// =============== FORM SUBMISSION ===============

function setupFormSubmission() {
    const form = document.getElementById('post-form');
    if (!form) {
        console.error('❌ Không tìm thấy form');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) {
        console.error('❌ Không tìm thấy submit button');
        return;
    }

    console.log('🔧 Setting up form submission');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        console.log('🚀 Form submitted');

        // Check if already submitting
        if (submitBtn.disabled) {
            console.log('🚫 Already submitting');
            return;
        }

        // Validate form
        const errors = validateForm();
        if (errors.length > 0) {
            alert('Lỗi: ' + errors.join(', '));
            return;
        }

        // Disable submit button
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

        try {
            // Create FormData
            const formData = new FormData(form);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            // Debug FormData
            console.log('📤 FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}:`, value);
            }

            // Submit form
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            console.log('📥 Response:', response.status);

            if (response.ok) {
                const data = await response.json();
                console.log('✅ Success:', data);

                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('Đăng bài thành công!');
                    window.location.reload();
                }
            } else {
                const errorData = await response.json();
                console.error('❌ Error:', errorData);
                alert('Có lỗi xảy ra: ' + (errorData.message || 'Unknown error'));
            }

        } catch (error) {
            console.error('❌ Network error:', error);
            alert('Có lỗi xảy ra: ' + error.message);
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

function validateForm() {
    const errors = [];

    // Check required fields
    const content = document.getElementById('content');
    if (!content || !content.value.trim()) {
        errors.push('Nội dung bài viết là bắt buộc');
    }

    // Check if uploads are still processing
    const totalImages = window.selectedImages?.length || 0;
    const totalVideos = window.selectedVideos?.length || 0;
    const uploadedImages = window.uploadedImageData?.length || 0;
    const uploadedVideos = window.uploadedVideoData?.length || 0;

    if (totalImages + totalVideos > uploadedImages + uploadedVideos) {
        errors.push('Vui lòng đợi quá trình tải lên hoàn tất');
    }

    // Check for upload errors
    const imageErrors = window.uploadedImageData?.filter(img => img.error).length || 0;
    const videoErrors = window.uploadedVideoData?.filter(vid => vid.error).length || 0;

    if (imageErrors > 0 || videoErrors > 0) {
        errors.push('Có file tải lên thất bại, vui lòng thử lại');
    }

    return errors;
}

// =============== CSS STYLES ===============

// Add required CSS
const style = document.createElement('style');
style.textContent = `
    .custom-file-upload {
        position: relative;
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .custom-file-upload:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .custom-file-upload.highlight-drop {
        border-color: #28a745;
        background-color: #d4edda;
    }

    .custom-file-upload input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .custom-file-upload i {
        font-size: 24px;
        color: #6c757d;
        margin-bottom: 10px;
        display: block;
    }

    .custom-file-upload span {
        color: #6c757d;
        font-size: 14px;
    }

    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .preview-item {
        position: relative;
        width: 150px;
        height: 150px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .preview-item img,
    .preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.2s ease;
    }

    .preview-remove:hover {
        background: #c82333;
        transform: scale(1.1);
    }

    .preview-remove:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .pulse-animation {
        animation: pulse 0.5s;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;

document.head.appendChild(style);
