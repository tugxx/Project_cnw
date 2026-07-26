<?php 
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng nhập</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        background: #f4f5f7;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 16px;
    }
    .login-box {
        background: #fff;
        width: 100%;
        max-width: 380px;
        padding: 32px 28px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .login-box h1 {
        font-size: 22px;
        margin-bottom: 24px;
        text-align: center;
        color: #1a1a1a;
    }
    .form-group {
        margin-bottom: 16px;
    }
    label {
        display: block;
        font-size: 14px;
        margin-bottom: 6px;
        color: #333;
    }
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #2563eb;
    }
    .field-error {
        color: #dc2626;
        font-size: 13px;
        margin-top: 4px;
    }
    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 16px;
    }
    button {
        width: 100%;
        padding: 11px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
    }
    button:hover {
        background: #1d4ed8;
    }
    .forgot-password-link {
        text-align: right;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .forgot-password-link a {
        color: #2563eb;
        text-decoration: none;
    }

    .forgot-password-link a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="login-box">
    <h1>Đăng nhập</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label for="login_input">Email hoặc Tên đăng nhập</label>
            <input 
                type="text" 
                id="login_input" 
                name="login_input" 
                value="<?= htmlspecialchars($loginInput) ?>"
                autocomplete="username"
            >
            <?php if (!empty($errors['login_input'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['login_input']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                autocomplete="current-password"
            >
            <?php if (!empty($errors['password'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="forgot-password-link">
            <a href="quen-mat-khau">Quên mật khẩu?</a>
        </div>

        <button type="submit">Đăng nhập</button>
    </form><br>

      <?php if (!empty($errors['auth'])): ?>
        <div class="alert-error"><?= htmlspecialchars($errors['auth']) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors['system'])): ?>
        <div class="alert-error"><?= htmlspecialchars($errors['system']) ?></div>
    <?php endif; ?>
</div>

</body>
</html>