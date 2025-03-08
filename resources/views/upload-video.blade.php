
<!DOCTYPE html>
<html>
<head>
    <title>Upload Image to Cloudinary</title>
</head>
<body>
{{--            Video--}}
<form action="{{ route('video.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="video" accept="video/*" required>
    <button type="submit">Upload Video</button>
</form>

@if (session('success'))
    <p>{{ session('success') }}</p>
    <video controls>
        <source src="{{ session('video_url') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
@endif
</body>
</html>
