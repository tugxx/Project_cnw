<?php
require_once __DIR__ . '/../components/input.php';
require_once __DIR__ . '/../components/alert.php';
require_once __DIR__ . '/../components/button.php';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>

<body style="background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 16px; box-sizing: border-box;">
    <div style="max-width: 400px; width: 100%; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f3f4f6; padding: 32px; box-sizing: border-box;">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">Đặt lại mật khẩu</h2>
            <p style="font-size: 13px; color: #6b7280; margin: 0;">Vui lòng nhập mật khẩu mới cho tài khoản của bạn</p>
        </div>

        <?php 
        if (!empty($success)) {
            renderAlert($success, 'success');
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                renderAlert($err, 'error');
            }
        }
        ?>

        <?php if (empty($success)): ?>
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <?php 
                    renderInput('new_password', 'Mật khẩu mới', 'password', '', '', '', true, true);
                    renderInput('confirm_password', 'Xác nhận mật khẩu mới', 'password', '', '', '', true, true);
                    renderButton('Cập nhật mật khẩu', 'submit');
                ?>
            </form>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
            <a href="dang-nhap" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                <span style="font-size: 14px;">←</span> Quay lại đăng nhập
            </a>
        </div>

    </div>

</body>
</html>