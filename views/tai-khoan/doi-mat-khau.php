<?php if (!defined('ALLOW_ACCESS')) { header("HTTP/1.1 404 Not Found"); exit; } ?>

<div class="container" style="max-width: 500px; margin: 40px auto;">
    <h2>Đổi mật khẩu</h2>

    <?php if (!empty($errors)): ?>
        <div style="background:#fdecea; color:#b71c1c; padding:10px 15px; border-radius:4px; margin-bottom:15px;">
            <ul style="margin:0; padding-left:20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div style="background:#e6f4ea; color:#1e7e34; padding:10px 15px; border-radius:4px; margin-bottom:15px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom:15px;">
            <label for="old_password">Mật khẩu cũ</label>
            <input type="password" id="old_password" name="old_password" style="width:100%; padding:8px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="new_password">Mật khẩu mới</label>
            <input type="password" id="new_password" name="new_password" style="width:100%; padding:8px; margin-top:5px;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="confirm_password">Xác nhận mật khẩu mới</label>
            <input type="password" id="confirm_password" name="confirm_password" style="width:100%; padding:8px; margin-top:5px;">
        </div>

        <button type="submit" style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            Cập nhật mật khẩu
        </button>
    </form>
</div>