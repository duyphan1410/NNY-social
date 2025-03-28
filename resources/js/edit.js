document.addEventListener('DOMContentLoaded', function () {
    let selectedImages = [];
    let selectedVideos = [];
    const MAX_IMAGES = 5;
    const MAX_VIDEOS = 3;

    function updatePreview(container, files, type, inputElement) {
        container.innerHTML = '';

        files.forEach((file, index) => {
            let wrapper = document.createElement('div');
            wrapper.style.position = "relative";
            wrapper.style.display = "inline-block";
            wrapper.style.margin = "5px";

            let removeBtn = document.createElement('button');
            removeBtn.innerHTML = '×';
            removeBtn.style.position = "absolute";
            removeBtn.style.top = "-5px";
            removeBtn.style.right = "-5px";
            removeBtn.style.background = "red";
            removeBtn.style.color = "white";
            removeBtn.style.border = "none";
            removeBtn.style.cursor = "pointer";
            removeBtn.style.padding = "2px 5px";
            removeBtn.style.borderRadius = "50%";

            removeBtn.onclick = function () {
                console.log(`🗑 Xóa ${type}:`, files[index].name);
                files.splice(index, 1);
                syncFiles(inputElement, files);
                updatePreview(container, files, type, inputElement);
            };

            if (type === 'image') {
                let img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = "1px solid #ddd";
                img.style.borderRadius = "5px";
                wrapper.appendChild(img);
            } else {
                let video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.style.width = '200px';
                video.style.height = 'auto';
                wrapper.appendChild(video);
            }

            wrapper.appendChild(removeBtn);
            container.appendChild(wrapper);
        });

        console.log(`📸 Tổng số ${type} đã chọn:`, files.length);
    }

    function syncFiles(inputElement, files) {
        let dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        inputElement.files = dataTransfer.files;
        console.log(`🔄 Đồng bộ input ${inputElement.id} - Số file:`, inputElement.files.length);
    }

    document.getElementById('images').addEventListener('change', function (event) {
        let files = Array.from(event.target.files);
        console.log("📤 Chọn ảnh:", files.map(f => f.name));

        if (selectedImages.length + files.length > MAX_IMAGES) {
            alert(`Bạn chỉ có thể chọn tối đa ${MAX_IMAGES} ảnh.`);
            return;
        }

        selectedImages = selectedImages.concat(files);
        syncFiles(event.target, selectedImages);
        updatePreview(document.getElementById('image-preview'), selectedImages, 'image', event.target);
    });

    document.getElementById('videos').addEventListener('change', function (event) {
        let files = Array.from(event.target.files);
        console.log("📤 Chọn video:", files.map(f => f.name));

        if (selectedVideos.length + files.length > MAX_VIDEOS) {
            alert(`Bạn chỉ có thể chọn tối đa ${MAX_VIDEOS} video.`);
            return;
        }

        selectedVideos = selectedVideos.concat(files);
        syncFiles(event.target, selectedVideos);
        updatePreview(document.getElementById('video-preview'), selectedVideos, 'video', event.target);
    });

    document.querySelector('form').addEventListener('submit', function (event) {
        syncFiles(document.getElementById('images'), selectedImages);
        syncFiles(document.getElementById('videos'), selectedVideos);

        console.log("🚀 Đang gửi form...");
        console.log("📸 Số lượng ảnh gửi đi:", document.getElementById('images').files.length);
        console.log("🎥 Số lượng video gửi đi:", document.getElementById('videos').files.length);
    });

    // Gỡ ảnh/video hiện tại
    document.querySelectorAll(".remove-current-image").forEach(button => {
        button.addEventListener("click", function () {
            let imgId = this.dataset.id;
            document.getElementById(`current-image-${imgId}`).remove();
            let hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "remove_images[]";
            hiddenInput.value = imgId;
            document.querySelector("form").appendChild(hiddenInput);
        });
    });

    document.querySelectorAll(".remove-current-video").forEach(button => {
        button.addEventListener("click", function () {
            let videoId = this.dataset.id;
            document.getElementById(`current-video-${videoId}`).remove();
            let hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "remove_videos[]";
            hiddenInput.value = videoId;
            document.querySelector("form").appendChild(hiddenInput);
        });
    });

});
