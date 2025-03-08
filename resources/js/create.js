document.addEventListener('DOMContentLoaded', function () {
    let selectedImages = [];
    let selectedVideos = [];
    const MAX_IMAGES = 5;
    const MAX_VIDEOS = 3;

    function updatePreview(container, files, type) {
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
                if (type === 'image') {
                    selectedImages.splice(index, 1);
                } else {
                    selectedVideos.splice(index, 1);
                }
                updatePreview(container, type === 'image' ? selectedImages : selectedVideos, type);
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
    }

    document.getElementById('images').addEventListener('change', function(event) {
        let files = Array.from(event.target.files);

        if (selectedImages.length + files.length > MAX_IMAGES) {
            alert(`Bạn chỉ có thể chọn tối đa ${MAX_IMAGES} ảnh.`);
            return;
        }

        selectedImages = selectedImages.concat(files);
        updatePreview(document.getElementById('image-preview'), selectedImages, 'image');
    });

    document.getElementById('videos').addEventListener('change', function(event) {
        let files = Array.from(event.target.files);

        if (selectedVideos.length + files.length > MAX_VIDEOS) {
            alert(`Bạn chỉ có thể chọn tối đa ${MAX_VIDEOS} video.`);
            return;
        }

        selectedVideos = selectedVideos.concat(files);
        updatePreview(document.getElementById('video-preview'), selectedVideos, 'video');
    });

    document.querySelector('form').addEventListener('submit', function (event) {
        let imageInput = document.getElementById('images');
        let videoInput = document.getElementById('videos');

        let imageDataTransfer = new DataTransfer();
        selectedImages.forEach(file => imageDataTransfer.items.add(file));
        imageInput.files = imageDataTransfer.files;

        let videoDataTransfer = new DataTransfer();
        selectedVideos.forEach(file => videoDataTransfer.items.add(file));
        videoInput.files = videoDataTransfer.files;
    });
});
