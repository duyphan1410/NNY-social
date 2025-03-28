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
            removeBtn.style.top = "5px";
            removeBtn.style.right = "5px";
            removeBtn.style.background = "red";
            removeBtn.style.color = "white";
            removeBtn.style.border = "none";
            removeBtn.style.cursor = "pointer";
            removeBtn.style.padding = "5px 10px";
            removeBtn.style.borderRadius = "50%";
            removeBtn.style.fontSize = "16px";
            removeBtn.style.zIndex = "10"; // Đảm bảo nằm trên ảnh

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
                img.style.display = "block";

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

    });
});
