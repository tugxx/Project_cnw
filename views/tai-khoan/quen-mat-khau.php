<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f5f7;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .box {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        h1 {
            font-size: 20px;
            margin: 0 0 8px;
            text-align: center;
        }

        p.desc {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin: 0 0 24px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 16px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .alert {
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-error {
            background: #fdecea;
            color: #b91c1c;
        }

        .alert-success {
            background: #eafaf0;
            color: #15803d;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
        }

        .back-link a {
            color: #2563eb;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Quên mật khẩu</h1>
        <p class="desc">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($success)): ?>
            <form method="POST" action="">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="abc@example.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >
                <button type="submit">Gửi yêu cầu</button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="dang-nhap">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>