<?php
$loginInput = $loginInput ?? '';
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
</head>
<body style="background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif;">

    <div style="max-width: 400px; width: 100%; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); padding: 32px;">
        
        <h2 style="font-size: 22px; font-weight: 700; color: #111827; text-align: center; margin-bottom: 24px;">Đăng nhập</h2>
        <form action="" method="POST">
            <?php 
                renderInput('login_input', 'Tên đăng nhập', 'text', $loginInput, $errors['login_input'] ?? '', 'Nhập email hoặc username...', true);
            ?>

        <div style="position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: -22px; position: relative; z-index: 1;">
                <span style="font-size: 14px; opacity: 0;"></span>
                <a href="quen-mat-khau" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    Quên mật khẩu?
                </a>
            </div>
            <?php renderInput('password', 'Mật khẩu', 'password', '', $errors['password'] ?? '', '', true, true); ?>
        </div>

        <div style="margin-top: 24px;">
            <?php renderButton('Đăng nhập', 'submit'); ?>
        </div>
        </form><br>

        <?php if (!empty($errors['auth']) || !empty($errors['system'])): ?>
            <?php renderAlert($errors['auth'] ?? $errors['system'], 'error'); ?>
        <?php endif; ?>
    </div>

</body>
</html>