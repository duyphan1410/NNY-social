
<!DOCTYPE html>
<html>
<head>
    <title>Upload Image to Cloudinary</title>
</head>
    <body>
            @if (session('success'))
                <p>{{ session('success') }}</p>
                <img src="{{ session('image_url') }}" alt="Uploaded Image">
            @endif

            <form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="image" required>
                <button type="submit">Upload</button>
            </form>
    </body>
</html>
