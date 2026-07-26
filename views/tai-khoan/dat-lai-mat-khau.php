<?php 
if (!defined('ALLOW_ACCESS')) { 
    header("HTTP/1.1 404 Not Found"); 
    exit; 
} 
$token = $token ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đặt lại mật khẩu</title>
<style>
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        background: #f4f5f7;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }
    .card {
        background: #fff;
        width: 100%;
        max-width: 400px;
        padding: 32px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .card h1 {
        font-size: 20px;
        margin: 0 0 20px;
        text-align: center;
        color: #222;
    }
    .field {
        margin-bottom: 16px;
    }
    .field label {
        display: block;
        font-size: 14px;
        margin-bottom: 6px;
        color: #333;
    }
    .field input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }
    .field input:focus {
        outline: none;
        border-color: #2563eb;
    }
    .btn {
        width: 100%;
        padding: 11px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
    }
    .btn:hover {
        background: #1d4ed8;
    }
    .errors {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .errors ul {
        margin: 0;
        padding-left: 18px;
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 16px;
        font-size: 13px;
        color: #2563eb;
        text-decoration: none;
    }
</style>
</head>
<body>
    <div class="card">
        <h1>Đặt lại mật khẩu</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="field">
                <label for="new_password">Mật khẩu mới</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="field">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Đặt lại mật khẩu</button>
        </form>

        <a class="back-link" href="dang-nhap">Quay lại đăng nhập</a>
    </div>
</body>
</html>