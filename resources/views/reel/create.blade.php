<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Bài Đăng Mới</title>
    <!-- Kết nối file -->
    @vite(['resources/css/reel.css', 'resources/js/reel.js'])
</head>
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tạo tin mới</h2>
        <form action="{{ route('reel.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="media">Chọn ảnh hoặc video:</label>
                <input type="file" name="media" id="media" class="form-control" accept="image/*,video/*" required>
            </div>

            <div class="form-group">
                <label for="caption">Mô tả:</label>
                <input type="text" name="caption" id="caption" class="form-control" placeholder="Nhập mô tả (không bắt buộc)">
            </div>

            <div class="form-group">
                <label for="duration">Thời lượng (giây):</label>
                <input type="number" name="duration" id="duration" class="form-control" min="1" placeholder="Thời lượng của video">
            </div>

            <div class="form-group">
                <label for="status">Trạng thái:</label>
                <select name="status" id="status" class="form-control">
                    <option value="active" selected>Hiển thị</option>
                    <option value="draft">Nháp</option>
                    <option value="hidden">Ẩn</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_public">Ai có thể xem?</label>
                <select name="is_public" id="is_public" class="form-control">
                    <option value="1" selected>Công khai</option>
                    <option value="0">Chỉ mình tôi</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Đăng tin</button>
        </form>
    </div>
    @endsection

</body>
</html>
