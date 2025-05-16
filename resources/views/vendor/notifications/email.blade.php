<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: 'Roboto', 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #2C5F9B;
        }
        .header img {
            height: 50px;
        }
        .content {
            padding: 30px;
        }
        h1 {
            color: #2C5F9B;
            font-size: 24px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
        }
        p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            background-color: #2C5F9B;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 500;
            padding: 12px 35px;
            border-radius: 4px;
            font-size: 16px;
            transition: all 0.2s ease;
        }
        .button:hover {
            background-color: #224b7a;
        }
        .note {
            font-size: 14px;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }
        .footer {
            text-align: center;
            padding: 20px 30px;
            background-color: #f5f7fa;
            color: #777;
            font-size: 14px;
            border-top: 1px solid #eaeaea;
        }
        .footer a {
            color: #2C5F9B;
            text-decoration: none;
        }
        .subcopy {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
            font-size: 13px;
            color: #666;
            word-break: break-all;
        }
        .text-danger {
            color: #dc3545;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @if(!empty($logoData))
            <img src="data:image/png;base64,{{ $logoData }}" alt="{{ config('app.name', 'Method…') }}" style="max-height: 50px;">
        @else
            {{ config('app.name', 'Method…') }}
        @endif
    </div>

    <div class="content">
        <h1>Xin chào!</h1>

        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

        <div class="button-container">
            <a href="{{ $actionUrl }}" class="button">Đặt lại mật khẩu</a>
        </div>

        <p>Liên kết đặt lại mật khẩu này sẽ hết hạn sau <strong>60 phút</strong>.</p>

        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.</p>

        <div class="subcopy">
            <p>Nếu bạn gặp vấn đề khi nhấp vào nút "Đặt lại mật khẩu", hãy sao chép và dán đường dẫn dưới đây vào trình duyệt web của bạn:</p>
            <p><a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
        </div>
    </div>

    <div class="footer">
        <p>Trân trọng,<br>{{ config('app.name') }}</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
        <p>
            <a href="{{ url('/privacy') }}">Chính sách bảo mật</a> |
            <a href="{{ url('/terms') }}">Điều khoản sử dụng</a>
        </p>
    </div>
</div>
</body>
</html>
